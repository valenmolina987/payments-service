# Payments Service

Transactional Outbox + Clean Architecture + Redis + Docker

---

## Overview

Este proyecto implementa un servicio de pagos aplicando principios de arquitectura limpia y buenas prácticas de diseño backend.

El sistema:

- Procesa un pago exitosamente.
- Persiste el pago en base de datos.
- Registra un evento en una tabla outbox.
- Procesa eventos de forma asíncrona.
- Envía notificaciones por correo.
- Maneja reintentos y fallos controlados.

El objetivo es garantizar consistencia entre la base de datos y sistemas externos (email), evitando inconsistencias mediante el patrón Transactional Outbox.

---

# Arquitectura

app/
├── Domain/
├── Application/
├── Infrastructure/
├── Http/
└── Mail/

## Domain

Esta capa no conoce nada sobre base de datos, colas o framework.

---

## Application

Aquí se define el comportamiento del sistema sin depender de implementaciones concretas.

---

## Infrastructure

Es la capa donde viven los detalles técnicos.

---

# Flujo del Pago

## 1. Procesamiento del pago

El caso de uso `ProcessPaymentUseCase` ejecuta:

- Creación de la entidad Payment
- Cambio de estado a exitoso
- Persistencia en base de datos
- Registro del evento en la tabla outbox

Todo esto ocurre dentro de una transacción de base de datos:

DB::transaction(...)

Esto garantiza atomicidad:

- O se guardan ambos registros (payment + outbox)
- O no se guarda ninguno

No existen pagos sin evento asociado.

---

## 2. Patrón Transactional Outbox

En lugar de enviar el email directamente, el sistema:

- Guarda un mensaje en la tabla `outbox_messages`
- Un worker lo procesa posteriormente

Esto evita inconsistencias en caso de fallos del servicio externo.

---

## 3. Procesamiento asíncrono

Se utiliza Redis como sistema de colas:

QUEUE_CONNECTION=redis


Un worker ejecuta:

php artisan queue:work redis --sleep=1 --tries=1 --timeout=60


El worker:

- Obtiene mensajes no procesados
- Aplica `lockForUpdate()` para evitar procesamiento duplicado
- Intenta enviar la notificación
- Marca el mensaje como procesado
- O incrementa intentos si falla

---

# Manejo de Reintentos

El sistema controla manualmente los intentos mediante los campos:

- `attempts`
- `failed`
- `failed_at`
- `error_message`

Reglas:

- Máximo 5 intentos
- Si falla 5 veces → se marca como `failed = true`
- Se registra el mensaje de error

Se usa `--tries=1` en el worker porque los reintentos se controlan manualmente en la base de datos, no por Laravel Queue.

---

# Simulación de Fallos

El servicio de notificaciones implementa aleatoriedad:

if (random_int(1, 5) === 1) {
throw new Exception('Servicio temporalmente no disponible');
}


Esto simula:

- Fallos intermitentes
- Servicios externos inestables
- Escenarios reales de producción

Permite validar el sistema de reintentos.

---

# Infraestructura Docker

Servicios incluidos:

- PHP (app)
- Nginx
- MySQL 8
- Redis
- Mailpit (SMTP testing)
- Worker
- Scheduler

---

# Instalación y Ejecución

## 1. Construir contenedores

docker compose up -d --build


---

## 2. Instalar dependencias

docker compose exec app composer install

---

## 3. Ejecutar migraciones

docker compose exec app php artisan migrate

---

# Mailpit

Mailpit se usa como servidor SMTP local para pruebas.

Configuración:

MAIL_HOST=mailpit
MAIL_PORT=1025


Para visualizar los correos enviados:

http://localhost:8025


# Conclusión

El sistema demuestra:

- Manejo correcto de transacciones.
- Consistencia eventual.
- Procesamiento asíncrono real.
- Control robusto de reintentos.
- Separación clara entre dominio e infraestructura.
