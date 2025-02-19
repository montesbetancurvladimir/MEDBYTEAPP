<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas1.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas3.css') }}">
</head>

<body>
    @include('layouts.menu')
    <!-- Primera página -->
    <div class="message-container">
        <div class="text-container">
            <br>
            <p class="message-text">Medbyte: Empresas</p><br><br>
            <p class="message-text-small">Previene posibles crisis en salud mental de tu comunidad mediante una red de apoyo automatizada.</p><br>
            <div class="image-container-scribble">
                <div class="image-scribble">
                    <img src="{{ asset('images/scribble.svg') }}" alt="Scribble Image">
                </div>
            </div>
        </div>

        <div class="image-group">
            <div class="image-empresas">
                <img src="{{ asset('images/empresas/empresas_personaje.svg') }}" alt="Personaje Empresa" class="side-image">
            </div>
        </div>
    </div>

    {{-- Segunda página     --}}
    <div class="container_empresas_pag2">
        <div class="text-container_empresas">
            <p class="message-text-empresas">¿Sabes cómo funciona<br> MedByte Care?</p>
        </div>
        <!-- Aquí comienza la tabla -->
        <div class="custom-table-container">
            <table class="custom-table">
                <tr>
                    <td class="number">01</td>
                    <td class="name">CHAT</td>
                    <td class="description">Interactúa con MIA sobre tus preocupaciones.</td>
                </tr>
                <tr>
                    <td class="number">02</td>
                    <td class="name">Alerta de intervención</td>
                    <td class="description">Identifica quiénes tienen más riesgo de padecer una crisis.</td>
                </tr>
                <tr>
                    <td class="number">03</td>
                    <td class="name">Estadísticas</td>
                    <td class="description">Visualiza y analiza toda la información del bienestar mental de tu comunidad.</td>
                </tr>
                <tr>
                    <td class="number">04</td>
                    <td class="name">Directorio y línea de tiempo de intervención</td>
                    <td class="description">Información de contacto y datos clínicos.</td>
                </tr>
            </table>
        </div>
    </div>


    {{-- Tercer página --}}
    <div class="container_pagina3_empresas">
        <div class="employee-meter">
            <div class="title-container">
                <img src="{{ asset('images/empresas/empleados.svg') }}" alt="Descripción de la imagen" class="employee-icon">
                <p class="employee-title">
                    <span id="employeeCount" class="employee-count">0</span><br>
                    <b>Tus empleados</b>
                </p>
            </div>
            <div class="slider-container">
                <input type="range" min="0" max="1000" value="0" class="slider" id="employeeRange">
            </div>
            <div class="meter-values">
                <span class="medidor_item" id="variable1">
                    <span class="medidor_value">0</span><br>
                    <span class="medidor_text">Empleados luchando con un problema de <b>salud mental</b></span>
                </span>
                <span class="medidor_item" id="variable2">
                    <span class="medidor_value">0</span><br>
                    <span class="medidor_text">Días perdidos por año debido al estrés laboral, ansiedad y depresión</span>
                </span>
                <span class="medidor_item" id="variable3">
                    <span class="medidor_value">0</span><br>
                    <span class="medidor_text">El costo anual debido al ausentismo, la falta de productividad y rotación laboral</span>
                </span>
            </div>
        </div>
    </div>


    <script>
        // Obtener los elementos del rango y los spans donde se muestran los valores
        var slider = document.getElementById("employeeRange");
        var output = document.getElementById("employeeCount");
        var variable1 = document.getElementById("variable1");
        var variable2 = document.getElementById("variable2");
        var variable3 = document.getElementById("variable3");

        // Mostrar el valor inicial del rango
        output.innerHTML = slider.value;
        updateVariables(slider.value);

        // Actualizar el número de empleados y las variables conforme se mueva el rango
        slider.oninput = function() {
            output.innerHTML = this.value;
            updateVariables(this.value);
        }

        function updateVariables(value) {
            var val1 = Math.round(value * 0.75);
            var val2 = val1 * 20;
            var val3 = val2 * 51;

            variable1.querySelector('.medidor_value').innerHTML = val1;
            variable1.querySelector('.medidor_text').innerHTML = "Empleados luchando con un problema de <b>salud mental</b>";

            variable2.querySelector('.medidor_value').innerHTML = val2;
            variable2.querySelector('.medidor_text').innerHTML = "Días perdidos por año debido al estrés laboral, ansiedad y depresión";
            variable3.querySelector('.medidor_value').innerHTML = "$" + val3.toLocaleString();
            variable3.querySelector('.medidor_text').innerHTML = "El costo anual debido al ausentismo, la falta de productividad y rotación laboral";
        }
    </script>
    

    <a href="{{ route('survey.inicio') }}" class="menu-button_empresa special-button">Comienza gratis</a>
    <div class="message-container">
        <div class="text-container">
            <div class="image-container-scribble">
                <div class="image-scribble_pag3">
                    <img src="{{ asset('images/scribble.svg') }}" alt="Scribble Image">
                </div>
            </div>
        </div>
    </div>

    <br><br><br>
    <p>.</p>

</body>
</html>
