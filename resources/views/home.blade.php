<!DOCTYPE html>
<html lang="es">
<head>
    <title>Medbyte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta property="twitter:card" content="summary_large_image" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/styles_home.css') }}">
</head>

<body>
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img
                src="{{ asset('external/logo31111500-fd8m-200h.png') }}"
                alt="LOGO31111500"
                class="landing-logo3111"
            />
        </a>
    </div>
    <div>
        <div class="landing-container">
            <div class="landing-landing">
                <div class="landing-menu-items"></div>
                <span class="landing-text">
                    <span>Encuentra la ayuda</span>
                    <br />
                    <span>que te entienda</span>
                    <br />
                    <span></span>
                </span>
                <span class="landing-text06">
                    <span class="landing-text07">Ya tomaste un paso importante.</span>
                    <br>
                    <br />
                    <span class="landing-text12">
                        Desde ayuda gratuita hasta terapeutas en tu región 24/7 Medbyte te
                        empareja con los recursos perfectos, respondiendo un par de
                        preguntas
                    </span>
                </span>
                
                <div class="landing-find">
                    <a href="{{ route('survey.start') }}" class="btn btn-primary">
                        <span class="landing-text13">Comienza tu recorrido</span>
                    </a>
                </div>
                <br>
                <div class="landing-frameiconclock">
                    <img
                        src="{{ asset('external/vector316-cvo6.svg') }}"
                        alt="Vector316"
                        class="landing-vector"
                    />
                    <span class="landing-text11"><span>  Dura 1 minuto</span></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
