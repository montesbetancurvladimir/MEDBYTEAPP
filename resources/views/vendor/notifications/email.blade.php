<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            border-top: 10px solid green; /* Línea de marco verde */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 26px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            margin: 10px 0;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .button.success {
            background-color: #28a745;
        }
        .button.error {
            background-color: #dc3545;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }
        .subcopy {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .break-all {
            word-wrap: break-word;
        }
        .footer-image {
            margin-top: 20px;
            text-align: center;
        }
        .footer-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Saludo -->
        <h1>
            ¡Hola!
        </h1>

        <!-- Líneas Introductorias -->
        <p>Estás recibiendo este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.</p>

        <!-- Botón de acción -->
        @isset($actionText)
        <?php
            $color = match ($level) {
                'success' => 'success',
                'error' => 'error',
                default => '',
            };
        ?>
        <a href="{{ $actionUrl }}" class="button {{ $color }}">Restablecer Contraseña</a>
        @endisset

        <p>Este enlace de restablecimiento de contraseña expirará en 60 minutos.</p><br>
        <p>Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna acción adicional.</p>

        <!-- Subcopy -->
        @isset($actionText)
        <div class="subcopy">
            <p>
                Si tienes problemas para hacer clic en el botón <b>"Restablecer Contraseña"</b>, copia y pega la URL a continuación
                en tu navegador web:
            </p>
            <p class="break-all"><b><a href="{{ $actionUrl }}">Restablecer Contraseña</a></b></p>
        </div>
        @endisset

        <!-- Imagen de Pie -->
        <div class="footer-image">
            <img src="https://i.postimg.cc/43cq9kPY/fondo-dos.png" alt="imagen-login">
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Medbyte. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
