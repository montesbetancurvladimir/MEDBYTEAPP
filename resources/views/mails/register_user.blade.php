<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Medbyte</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #cccccc; /* Borde gris */
        }
        /* Encabezado */
        .header {
            text-align: center;
            padding: 15px;
            background-color: #f7f7f7; /* Fondo gris claro */
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Espacio adicional entre el encabezado y el contenido */
        }
        .header h1 {
            color: #333333;
            margin: 10px 0; /* Añadimos margen alrededor del título */
        }
        .header p {
            color: #555555;
            margin: 0;
        }
        /* Contenido */
        .content {
            margin-bottom: 30px;
        }
        .content p {
            color: #555555;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .content p.bold {
            font-weight: bold;
            font-size: 18px;
        }
        /* Pie de página */
        .footer {
            text-align: center;
            color: #777777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido!</h1>
            <p>Gracias por registrarte en nuestra plataforma.</p>
        </div>
        <div class="content">
            <p>Estamos emocionados de tenerte a bordo. Aquí encontrarás tu información util para comenzar:</p>
            <p><b>Correo Electrónico:</b> {{ $user->email }}</p>
            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en ponerte en contacto con nuestro equipo de soporte.</p>
            <p>Esperamos que disfrutes de tu experiencia con nosotros.</p>
            <p class="bold">¡Bienvenido una vez más!</p>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a esta dirección de correo electrónico.</p>
        </div>
    </div>
</body>
</html>
