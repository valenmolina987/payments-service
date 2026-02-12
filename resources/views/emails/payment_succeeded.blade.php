<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pago exitoso</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
        <tr>
            <td align="center">

                <table width="500" cellpadding="0" cellspacing="0" 
                       style="background:#ffffff; padding:30px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="margin:0; color:#2d3748;">Pago confirmado</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 0; color:#4a5568; font-size:15px;">
                            Tu pago fue procesado correctamente.
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:15px 0; font-size:14px; color:#2d3748;">
                            <strong>ID del Pago:</strong><br>
                            {{ $paymentId }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:15px 0; font-size:16px; color:#2b6cb0;">
                            <strong>Monto:</strong> ${{ number_format($amount, 2) }}
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
