<!DOCTYPE html>
<html lang="es">
<head>
    @include('layouts.head')
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inicio_completo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal_inicio.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>


<body>
    @include('layouts.menu')

    @if($mensaje)
    <script>
        window.onload = function() {
            var myModal = new bootstrap.Modal(document.getElementById('mensajeModal'));
            myModal.show();
        };
    </script>

    <!-- Modal de encuesta realizada -->
    <div class="modal fade" id="mensajeModal" tabindex="-1" aria-labelledby="mensajeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mensajeModalLabel">¡Gracias por tu tiempo!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">×</button>
                </div>
                <div class="modal-body text-center">
                    <p>Gracias por completar la encuesta. Ahora, lleva la experiencia al siguiente nivel descargando nuestra app.</p>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <!-- Botón de descarga Android -->
                        <a href="https://play.google.com/store/apps/details?id=tu.app" target="_blank" class="btn btn-success">
                            📲 Descargar en Google Play
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('page.nosotros') }}" class="btn btn-secondary">Conoce más</a>
                </div>
            </div>
        </div>
    </div>
    
@endif
 
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
