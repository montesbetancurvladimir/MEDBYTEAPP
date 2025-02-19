<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medbyte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="{{ asset('css/styles_home.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/animate.css@4.1.1/animate.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=STIX+Two+Text:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Incluir Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Incluir jQuery y Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img src="{{ asset('external/logo31111500-fd8m-200h.png') }}" alt="LOGO31111500" class="landing-logo3111" />
        </a>
    </div>
    <div>

        <!-- Incluye la plantilla de mensajes -->
        @include('includes.mensajes')

        <div class="landing-container">
            <div class="landing-landing">
                <br><br>
                <div class="landing-menu-items"></div>
                <span class="landing-text">
                    <span>¿Donde estás ubicad@?</span>
                    <br />
                </span><br>
                <span class="landing-text06">
                    <span class="landing-text_intro_question">Te preguntamos para emparejarte <br> con servicios en tu área.</span>
                    <br>
                </span><br><br>
                <form method="POST" action="{{ route('survey.answer_cache') }}" class="form-container">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                    <div class="select-container">
                        <select name="selected_option" id="selected_option" required class="styled-select">
                            @foreach ($paises as $pais)
                                <option value="{{ $pais->id }}">{{ $pais->descripcion }} ({{ $pais->indicativo }})</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down"></i> <!-- Icono de flecha hacia abajo -->
                    </div>
                    <br><br><br>
                    <button class="btn btn-primary landing-text13" type="submit">Siguiente</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Inicializar Select2 -->
    <script>
        $(document).ready(function() {
            $('#selected_option').select2();
        });
    </script>
</body>
</html>
