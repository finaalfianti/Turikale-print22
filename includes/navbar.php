<?php
$bp = $basePath ?? '';
$cp = $currentPage ?? '';

$navLogo = function_exists('get_setting') ? get_setting('site_logo_header', 'assets/images/Logo Biru-TurikalePrint.png') : 'assets/images/Logo Biru-TurikalePrint.png';
if (!str_starts_with($navLogo, '/') && !str_starts_with($navLogo, 'http')) {
    $navLogo = $bp . $navLogo;
}

$homeUrl      = ($bp === '') ? 'index.php' : $bp . 'index.php';
$desainUrl    = ($bp === '') ? 'pages/desain.php' : $bp . 'pages/desain.php';
$profilUrl    = ($bp === '') ? 'pages/profil.php' : $bp . 'pages/profil.php';
$asetLogoUrl  = ($bp === '') ? 'pages/aset-logo.php' : $bp . 'pages/aset-logo.php';
$layoutingUrl = ($bp === '') ? 'pages/layouting.php' : $bp . 'pages/layouting.php';
?>
<header class="site-nav" role="banner">
    <div class="nav-container">
        <a href="<?= $homeUrl ?>" class="nav-logo" aria-label="Turikale Print - Beranda">
            <img src="<?= htmlspecialchars($navLogo) ?>" alt="Turikale Print">
        </a>
        
        <button type="button" class="menu-toggle" id="menuToggle" aria-label="Toggle navigation menu" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        
        <nav role="navigation">
            <ul class="nav-links" id="navLinks">
                <li><a href="<?= $homeUrl ?>" class="nav-item <?= $cp === 'beranda' ? 'active' : '' ?>" data-clean="/">Beranda</a></li>
                <li><a href="<?= $desainUrl ?>" class="nav-item <?= $cp === 'desain' ? 'active' : '' ?>" data-clean="/desain">Desain</a></li>        
                <li><a href="<?= $profilUrl ?>" class="nav-item <?= ($cp === 'profil' || $cp === 'profile') ? 'active' : '' ?>" data-clean="/profil">Profile</a></li>
                <li><a href="<?= $asetLogoUrl ?>" class="nav-item <?= $cp === 'aset-logo' ? 'active' : '' ?>" data-clean="/aset-logo">Aset Logo</a></li>
                <li><a href="<?= $layoutingUrl ?>" class="nav-item <?= $cp === 'layouting' ? 'active' : '' ?>" data-clean="/layouting">Layouting</a></li>
            </ul>
        </nav>
    </div>
</header>

<script>
(function() {
    var menuToggle = document.getElementById('menuToggle');
    var navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            var isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('show');
        });
    }

    if (window.fetch) {
        fetch('/profil', { method: 'HEAD' }).then(function(res) {
            if (res.ok) {
                document.querySelectorAll('.nav-item[data-clean]').forEach(function(el) {
                    el.setAttribute('href', el.getAttribute('data-clean'));
                });
            }
        }).catch(function() {});
    }
})();
</script>