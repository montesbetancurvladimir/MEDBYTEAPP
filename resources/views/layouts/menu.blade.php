<div class="top-line">
    <p>Realiza nuestro test de riesgo de salud mental con IA gratis</p>
</div>
<div class="header-container">
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img src="{{ asset('external/logo31111500-fd8m-200h.png') }}" alt="LOGO31111500" class="landing-logo3111" />
        </a>
    </div>
    <div class="menu-buttons">
        <a href="{{ route('survey.complete_individual') }}" class="menu-button">Planes</a>
        <a href="{{ route('page.empresas') }}" class="menu-button">Empresas</a>
        <div class="select-wrapper">
            <div class="select-wrapper">
                <select class="menu-select" onchange="window.location.href=this.value;">
                    <option value="" disabled selected>Recursos</option>
                    <option value="{{ route('page.blog') }}">Blog</option>
                    <option value="{{ route('page.prensa') }}">Prensa</option>
                </select>
            </div>
        </div>
        <a href="{{ route('survey.inicio') }}" class="menu-button special-button">Comienza ya</a>
    </div>
</div>
{{-- el br hace que no se desparezca en algunas vistas la linea --}}
<div class="divider"><br></div>


