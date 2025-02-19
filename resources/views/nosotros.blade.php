<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nosotros_new.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nosotros_1.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nosotros_2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nosotros_3.css') }}">
</head>

<body>
    @include('layouts.menu')
    {{-- Primer página  --}}
    <div class="message-container">
        <div class="text-container">
            <p class="message-text">Medbyte:</p><br>
            <p class="message-text-medium">Tu mente, tu bienestar, tu futuro</p><br>
            <p class="message-text-small">En Medbyte, creemos que el bienestar es la llave para una vida plena y significativa. Por eso, nos dedicamos a ofrecerte soluciones innovadoras y accesibles que te empoderan para tomar el control de tu bienestar físico y mental para alcanzar tu máximo potencial.</p><br>
        </div>
        <div class="image-group">
            <div class="image-empresas">
                <img src="{{ asset('images/nosotros/personaje.svg') }}" alt="Personaje Nosotros" class="side-image">
            </div>
        </div>
    </div>
    <div class="image-container-scribble">
        <div class="image-scribble">
            <img src="{{ asset('images/scribble.svg') }}" alt="Scribble Image">
        </div>
    </div>
   
    {{-- Segunda página  --}}
    <div class="container_pagina2">
        <div class="message-container">
            <br>
            <div class="text-container_nosotros">
                <br><br>
                <p class="message-text-nosotros">¿Qué nos hace <br>
                    diferentes?</p><br>
            </div>
        </div>
        <div class="columns-container">
            <div class="column">
                <h2 class="titulo_n2" >Tecnología de vanguardia</h2>
                <p>Utilizamos tecnología de última generación para brindarte un servicio personalizado y eficaz.</p>
                <div class="line"></div>
            </div>
            <div class="column">
                <h2 class="titulo_n2" >Equipo de expertos</h2>
                <p>Un equipo de profesionales altamente calificados y con amplia experiencia en salud mental está a tu disposición.</p>
                <div class="line"></div>
            </div>
            <div class="column">
                <h2 class="titulo_n2" >Enfoque centrado en ti</h2>
                <p>Nos enfocamos en tus necesidades y nos esforzamos por ofrecerte una experiencia excepcional.</p>
                <div class="line"></div><br><br>
            </div>
        </div>
        <br><br>
    </div>


    {{-- Tercera página  --}}
    <div class="container_pagina3">
        <div class="message-container">
            <div class="text-container_nosotros">
                <p class="message-text-page3_small">Soluciones para todos</p><br>
            </div>
        </div>

        <div class="columns-container_pag3">
            <div class="column_pag3">
                <h2>Chatbots de salud mental</h2>
                <p>Soporte confidencial 24/7 para una variedad de problemas de salud mental.</p>
            </div>
            <div class="column_pag3">
                <h2>Terapia en línea</h2>
                <p>Terapia con terapeutas con licencia desde la comodidad de tu hogar.</p>
            </div>
            <div class="column_pag3">
                <h2>Recursos de autoayuda</h2>
                <p>Herramientas para ayudarte a manejar tu salud mental.</p>
            </div>
        </div>
        <a href="{{ route('survey.inicio') }}" class="message-button">Comienza gratis</a>
        <p>.</p>
    </div>
</body>
</html>
