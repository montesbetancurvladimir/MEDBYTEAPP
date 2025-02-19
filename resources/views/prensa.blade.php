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
            <p class="landing-text07">
                Conoce como MedByte está transformando el panorama de la salud global
            </span>
            
            <!-- New Blog Section -->
            <div class="blog-section">
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/forbes_logo.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>Colombiano crea software para predecir crisis de salud mental en trabajadores</h2>
                    <a href="https://forbes.co/2022/10/16/actualidad/colombiano-crea-software-para-predecir-crisis-de-salud-mental-en-trabajadores" target="_blank" class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </a>
                </div>                
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/la_fm_logo.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>Una solución digital creada por un colombiano y que es capaz de identificar la probabilidad de que una persona llegue a sufrir una crisis </h2>
                    <a href="https://www.lafm.com.co/tecnologia/inteligencia-artificial-ayuda-a-predecir-la-ansiedad-el-estres-y-la-depresion" target="_blank" class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="blog-box">
                    <div class="blog-image-container">
                        <img src="{{ asset('images/prensa/ejemplo.svg') }}" alt="Imagen del Blog">
                        <img src="{{ asset('images/prensa/la_fm_logo.svg') }}" alt="Logo" class="logo-overlay">
                    </div>
                    <h2>Durante varios años los colombianos han enfrentado ineficiencias del sistema de salud, como demora en la asignación de citas médicas</h2>
                    <a href="https://www.lafm.com.co/tecnologia/la-plataforma-que-ayuda-a-la-prestacion-y-atencion-de-los-servicios-de-salud-en-colombia" target="_blank" class="btn btn-primary">
                        Leer más <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
