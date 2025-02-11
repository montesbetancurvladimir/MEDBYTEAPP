<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>

<body>
    @include('layouts.menu')
    <div>
        <div class="landing-container">
            <div class="landing-landing">
                <div class="landing-menu-items"></div>
                <span class="landing-text">
                    Nuestro blog
                </span><br>
                <p class="landing-text07">
                    Conoce las últimas noticias en el campo de la salud con <b>MedByte</b>
                </p>

                <!-- New Blog Section -->
                <div class="blog-section">
                    
                    <div class="large-blog-box">
                        <img src="{{ asset('images/blog/blog1.png') }}" alt="Imagen del Blog">
                        <h2>¿Por qué análisis de datos para mi entidad de salud?</h2>
                        <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                        <button class="boton_leer btn btn-primary">Leer más</button>
                    </div>

                    <div class="small-blog-boxes">
                        <div class="small-blog-box">
                            <img src="{{ asset('images/blog/blog2.png') }}" alt="Imagen del Blog">
                            <h2>¿Cómo aumentar tu volumen de pacientes?</h2>
                            <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                            <button class="btn btn-primary">Leer más</button>
                        </div>
                        <div class="small-blog-box">
                            <img src="{{ asset('images/blog/blog3.png') }}" alt="Imagen del Blog">
                            <h2>¿Cómo conservar y aumentar tus pacientes en la pandemia?</h2>
                            <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                            <button class="btn btn-primary">Leer más</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
