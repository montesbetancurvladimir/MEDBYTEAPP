<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medbyte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta property="twitter:card" content="summary_large_image" />
    <style data-tag="reset-style-sheet">
        html { line-height: 1.15; }
        body { margin: 0; }
        * { box-sizing: border-box; border-width: 0; border-style: solid; }
        p, li, ul, pre, div, h1, h2, h3, h4, h5, h6, figure, blockquote, figcaption { margin: 0; padding: 0; }
        button { background-color: transparent; }
        button, input, optgroup, select, textarea { font-family: inherit; font-size: 100%; line-height: 1.15; margin: 0; }
        button, select { text-transform: none; }
        button, [type="button"], [type="reset"], [type="submit"] { -webkit-appearance: button; }
        button::-moz-focus-inner, [type="button"]::-moz-focus-inner, [type="reset"]::-moz-focus-inner, [type="submit"]::-moz-focus-inner { border-style: none; padding: 0; }
        button:-moz-focus, [type="button"]:-moz-focus, [type="reset"], [type="submit"]:-moz-focus { outline: 1px dotted ButtonText; }
        a { color: inherit; text-decoration: inherit; }
        input { padding: 2px 4px; }
        img { display: block; }
        html { scroll-behavior: smooth; }
    </style>
    <style data-tag="default-style-sheet">
        html {
            font-family: Inter;
            font-size: 16px;
        }
        body {
            font-weight: 400;
            font-style: normal;
            text-decoration: none;
            text-transform: none;
            letter-spacing: normal;
            line-height: 1.15;
            color: var(--dl-color-theme-neutral-dark);
            background-color: var(--dl-color-theme-neutral-light);
            fill: var(--dl-color-theme-neutral-dark);
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/animate.css@4.1.1/animate.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=STIX+Two+Text:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" data-tag="font" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" data-tag="font" />
    
    <!-- External stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
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
                @foreach ($planes_actualizados as $plan)
                    <div class="plan-box {{ $PlanRecomendado == $plan->nombre ? 'plan-recommended' : '' }}">
                        <div class="plan-title">
                            @if($PlanRecomendado == $plan->nombre)
                                <img src="{{ asset('images/planes/estrella.png') }}" alt="estrella" class="star">
                            @endif
                            {{ $plan->nombre }}
                        </div>
                        <div class="plan-sub-subtitle">{{ $plan->valor }}</div>
                        <div class="plan-subtitle">{{ $plan->periodo }}</div>
                        <div class="plan-description">{{ $plan->descripcion }}</div>
                        <ul class="plan-features">
                            @foreach ($plan->caracteristicas as $caracteristica)
                                <li>{{ $caracteristica }}</li>
                            @endforeach
                        </ul>
                        <div class="plan-action">
                            <button>Seleccionar</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
