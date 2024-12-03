<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prensa.css') }}">
</head>

<body>
    @include('layouts.menu')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <div class="landing-container">
        <div class="landing-landing">
            <div class="landing-menu-items"></div>
            <span class="landing-text">
                <span>Prensa</span>
                <br />
            </span>
            <span class="landing-text06">
                <span class="landing-text07">Conoce como MedByte está transformando el panorama de la salud global</span>
            </span>
            
            <!-- New Blog Section -->
            <div class="blog-section">
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/forbes_logo.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>¿Por qué análisis de datos para mi entidad de salud?</h2>
                    <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                    <button class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/group.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>¿Cómo aumentar tu volumen de pacientes?</h2>
                    <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                    <button class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/la_fm_logo.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>¿Cómo conservar y aumentar tus pacientes en la pandemia?</h2>
                    <p class="blog-description">Descubre cómo el análisis de datos puede transformar tu entidad de salud.</p>
                    <button class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
