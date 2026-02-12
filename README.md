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

Contiene la lógica de negocio pura:

- Entidad `Payment`
- Estados del pago
- Reglas del dominio

Esta capa no conoce nada sobre base de datos, colas o framework.

---

## Application

Contiene:

- Casos de uso (`ProcessPaymentUseCase`)
- Interfaces (`PaymentRepository`, `OutboxRepository`, `NotificationService`)
- Orquestación transaccional

Aquí se define el comportamiento del sistema sin depender de implementaciones concretas.

---

## Infrastructure

Contiene:

- Implementaciones Eloquent
- Jobs de procesamiento de cola
- Servicio de envío de correos
- Integración con Redis
- Modelos de persistencia

Es la capa donde viven los detalles técnicos.

---

# Flujo del Pago

## 1. Procesamiento del pago

El caso de uso `ProcessPaymentUseCase` ejecuta:

- Creación de la entidad Payment
- Cambio de estado a exitoso
- Persistencia en base de datos
- Registro del evento en la tabla outbox

Todo esto ocurre dentro de una transacción:

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

El proyecto está completamente dockerizado y no requiere instalaciones locales de PHP, MySQL o Redis.

---

# Instalación y Ejecución

## Requisitos

- Docker
- Docker Compose

---

## 1. Clonar repositorio

git clone https://github.com/valenmolina987/payments-service.git
cd payments-service

---

## 2. Crear archivo de entorno

El archivo `.env` no se incluye por seguridad.

Crear a partir del ejemplo:

cp .env.example .env


Si no existe `.env.example`, crear manualmente un archivo `.env` con el siguiente contenido mínimo:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=payments
DB_USERNAME=user
DB_PASSWORD=password

QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=Laravel


---

## 3. Levantar contenedores

docker compose up -d --build


---

## 4. Instalar dependencias

docker compose exec app composer install


---

## 5. Generar clave de aplicación

docker compose exec app php artisan key:generate


---

## 6. Ejecutar migraciones

docker compose exec app php artisan migrate


---

## 7. Reiniciar worker y scheduler

docker compose restart scheduler

docker compose restart worker


---

# Acceso

Aplicación:
http://localhost:8000

Crear Pago:
Post: http://localhost:8000/api/payments

Body:
{
  "amount": 150,
  "email": "valen@test.com"
}

Consultar estado de pagos y notificaciones:
Get: http://localhost:8000/api/payments

Para ver el correo enviado ingrese a:

Mailpit:
http://localhost:8025

# Conclusión

El sistema demuestra:

- Manejo correcto de transacciones.
- Consistencia eventual mediante Transactional Outbox.
- Procesamiento asíncrono con Redis.
- Control manual robusto de reintentos.
- Separación clara entre dominio e infraestructura.
- Entorno reproducible mediante Docker.