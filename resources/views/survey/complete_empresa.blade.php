<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medbyte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta property="twitter:card" content="summary_large_image" />
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/animate.css@4.1.1/animate.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=STIX+Two+Text:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" data-tag="font" />
    <!-- Add more font styles if needed -->
    <!-- External stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/landing1.css') }}" />
</head>
<body>
    <div class="landing1-container">
        <div class="landing1-landing">
            <div class="landing1-menu-items"></div>

            <span class="landing1-text2">
                <h2>Escoge tu plan</h2>
            </span>

            <a href="{{ route('home') }}">
                <img src="{{ asset('external/logo31111500-fd8m-200h.png') }}" alt="LOGO31111500" class="landing-logo3111" />
            </a>

            <div class="plans-container">
                <div class="plan-box <?php if($PlanRecomendado == 'Care_EMPRESAS') echo 'plan-recommended'; ?>">
                    
                    <div class="plan-title">Mia</div>
                    <div class="plan-sub-subtitle">Gratis</div>
                    <div class="plan-subtitle">Por Siempre</div>
                    <div class="plan-description">Chatea con psicólogos 24/7 los 365 días del año de forma ilimitada</div>
                    <ul class="plan-features">
                        <li>Límite de conversación diario</li>
                        <li>Disponible en WhatsApp</li>
                        <li>Recompensas</li>
                        <li>Más de 100 horas en contenido de ayuda</li>
                    </ul>
                    <div class="plan-action">
                        <button>Seleccionar</button>
                    </div>
                </div>
                <div class="plan-box <?php if($PlanRecomendado == 'Anytime_EMPRESAS') echo 'plan-recommended'; ?>">
                    
                    <div class="plan-title">Anytime</div>
                    <div class="plan-sub-subtitle">$10</div>
                    <div class="plan-subtitle">Por mes</div>
                    <div class="plan-description">Chatea con psicólogos 24/7 los 365 días del año de forma ilimitada</div>
                    <ul class="plan-features">
                        <li>Acceso a Mia Pro</li>
                        <li>Disponible en WhatsApp</li>
                        <li>Chaterapia ilimitada</li>
                        <li>Acceso a teleconsultas con cientos de profesionales</li>
                    </ul>
                    <div class="plan-action">
                        <button>Seleccionar</button>
                    </div>
                </div>
                <div class="plan-box <?php if($PlanRecomendado == 'Full_EMPRESAS') echo 'plan-recommended'; ?>">
                    
                    <div class="plan-title">Full</div>
                    <div class="plan-sub-subtitle">$200</div>
                    <div class="plan-subtitle">Por mes</div>
                    <div class="plan-description">Accede a una experiencia integral de bienestar con cientos de profesionales</div>
                    <ul class="plan-features">
                        <li>Acceso preferencial a Mia Pro y Anytime</li>
                        <li>Plan personalizado con IA de bienestar 360</li>
                        <li>4 teleconsultas con profesionales locales</li>
                        <li>Acceso a recompensas exclusivas</li>
                    </ul>
                    <div class="plan-action">
                        <button>Seleccionar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
