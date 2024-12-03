<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inicio_completo.css') }}">
</head>

<body>
    @include('layouts.menu')
    
    <div class="message-container">
        <div class="text-container">
            <p class="message-text">Un pequeño paso para ti;</p>
            <p class="message-text">un gran salto <br> para tu felicidad</p><br><br>
            <p class="message-text-small">Sabemos que buscar ayuda de <strong>bienestar</strong> 
            <br> puede ser abrumador, 
            <br>por eso hicimos el proceso <strong>mejor</strong>.</p><br><br>
            <a href="{{ route('survey.inicio') }}" class="message-button">Comienza</a>
        </div>
        <div class="image-group">
            <div class="image-container">
                <img src="{{ asset('images/inicio/planet.png') }}" alt="Planeta" class="planet-image">
            </div>
            <div class="image-container">
                <img src="{{ asset('images/inicio/astronauta.svg') }}" alt="Astronauta" class="side-image">
            </div>
        </div>
    </div>
</body>
</html>
