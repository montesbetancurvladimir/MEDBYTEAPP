{{-- 
<div class="top-line">
    <p class= "text_top_line">Realiza nuestro test de riesgo de salud mental con IA gratis</p>
</div>
--}}
<div class="header-container">
    <div class="logo-container">
        <a href="{{ route('home') }}">
            <img src="{{ asset('external/logo31111500-fd8m-200h.png') }}" alt="LOGO31111500" class="landing-logo3111" />
        </a>
        <!-- Botón del menú hamburguesa -->
        <button class="hamburger-menu" onclick="toggleMenu()">☰</button>
    </div>
    <div class="menu-buttons" id="mobile-menu">
        {{-- <a href="{{ route('survey.complete_individual') }}" class="menu-button">Planes</a> --}}
        <a href="{{ route('page.empresas') }}" class="menu-button">Empresas</a>
        <a href="{{ route('page.nosotros') }}" class="menu-button">Nosotros</a>
        <div class="select-wrapper">
            <select class="menu-select" onchange="window.location.href=this.value;">
                <option value="" disabled selected>Recursos</option>
                <option value="{{ route('page.blog') }}">Blog</option>
                <option value="{{ route('page.prensa') }}">Prensa</option>
            </select>
        </div>
        <a href="{{ route('survey.inicio') }}" class="menu-button special-button">Comienza ya</a>
    </div>
</div>
<div class="divider"><br></div>
<br>
<script>
    function toggleMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('active');
    }
</script>
