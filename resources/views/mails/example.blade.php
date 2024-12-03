<!-- resources/views/emails/example_email.blade.php -->

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $subject }}</title>
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
                border: 1px solid #cccccc; /* Agregamos el borde gris */
            }
            /* Encabezado */
            .header {
                text-align: center;
                margin-bottom: 20px;
                padding: 15px;
                background-color: #f7f7f7; /* Fondo gris claro */
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px; /* Espacio adicional entre el encabezado y el contenido */
            }
            .header h1 {
                color: #333333;
                margin-bottom: 10px;
                margin-top: 0; /* Eliminamos el margen superior para el título */
            }
            .header p {
                color: #555555;
                margin-bottom: 0;
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
                <img src="{{ asset('logo_jey2.png') }}" alt="Logo de la empresa" style="max-width: 200px;">
                <img src="{{ asset('assets/images/logo_jey2.png') }}" alt="Logo de la empresa" style="max-width: 200px;">
                <h1>{{ $subject }}</h1>
                <p>¡Hola {{ $user->username }}!</p>
            </div>
            <div class="content">
                <p>Te informamos que se ha creado un nuevo contrato con el siguiente código:</p>
                <p style="font-size: 18px; font-weight: bold;">Contrato: {{ $contrato->codigo }}</p>
                <p>Por favor, revisa los detalles del contrato y ponte en contacto con nosotros si necesitas más información.</p>
            </div>
            <div class="footer">
                <p>Este es un mensaje automático, por favor no respondas a esta dirección de correo electrónico.</p>
            </div>
        </div>
    </body>
</html>
