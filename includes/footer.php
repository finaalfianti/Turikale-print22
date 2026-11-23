<?php
$bp = $basePath ?? '';
$footerWa = '6287841064160';
$footerHours = function_exists('get_setting') ? get_setting('working_hours', 'Senin – Minggu: 08.00 – 21.00') : 'Senin – Minggu: 08.00 – 21.00';
$footerPhone1 = function_exists('get_setting') ? get_setting('contact_phone_1', '+62 878-4106-4160') : '+62 878-4106-4160';
$footerLogo = function_exists('get_setting') ? get_setting('site_logo_footer', 'assets/images/Logo Putih-TurikalePrint.png') : 'assets/images/Logo Putih-TurikalePrint.png';
if (!str_starts_with($footerLogo, '/') && !str_starts_with($footerLogo, 'http')) {
    $footerLogo = $bp . $footerLogo;
}
$companyName = function_exists('get_setting') ? get_setting('company_name', 'CV. Opu Barakati Jaya') : 'CV. Opu Barakati Jaya';
$footerDesc = function_exists('get_setting') ? get_setting('footer_description', 'Pusat percetakan digital profesional dengan layanan berkualitas tinggi, cepat, dan terpercaya. Kami melayani segala kebutuhan cetak Anda dengan teknologi terkini.') : 'Pusat percetakan digital profesional dengan layanan berkualitas tinggi, cepat, dan terpercaya. Kami melayani segala kebutuhan cetak Anda dengan teknologi terkini.';
$tiktokUrl = function_exists('get_setting') ? get_setting('social_tiktok', 'https://www.tiktok.com/@turikale_digitalprinting') : 'https://www.tiktok.com/@turikale_digitalprinting';
$instagramUrl = function_exists('get_setting') ? get_setting('social_instagram', 'https://www.instagram.com/turikale.print') : 'https://www.instagram.com/turikale.print';
$facebookUrl = function_exists('get_setting') ? get_setting('social_facebook', '') : '';
$footerCopyright = function_exists('get_setting') ? get_setting('footer_copyright_text', '© 2026 Turikale Print. Semua hak dilindungi.') : '© 2026 Turikale Print. Semua hak dilindungi.';

$homeUrl      = ($bp === '') ? 'index.php' : $bp . 'index.php';
$desainUrl    = ($bp === '') ? 'pages/desain.php' : $bp . 'pages/desain.php';
$profilUrl    = ($bp === '') ? 'pages/profil.php' : $bp . 'pages/profil.php';
$asetLogoUrl  = ($bp === '') ? 'pages/aset-logo.php' : $bp . 'pages/aset-logo.php';
$layoutingUrl = ($bp === '') ? 'pages/layouting.php' : $bp . 'pages/layouting.php';

$servicesTitle = function_exists('get_setting') ? get_setting('footer_services_title', 'Layanan') : 'Layanan';
$servicesRaw = function_exists('get_setting') ? get_setting('footer_services_links', "Spanduk | {$homeUrl}#kategori\nStiker | {$homeUrl}#kategori\nKartu Nama | {$homeUrl}#kategori\nStempel | {$homeUrl}#kategori\nBanner | {$homeUrl}#kategori\nOutdoor Media | {$homeUrl}#kategori") : "Spanduk | {$homeUrl}#kategori\nStiker | {$homeUrl}#kategori\nKartu Nama | {$homeUrl}#kategori\nStempel | {$homeUrl}#kategori\nBanner | {$homeUrl}#kategori\nOutdoor Media | {$homeUrl}#kategori";

$serviceLinks = [];
foreach (explode("\n", str_replace("\r", "", $servicesRaw)) as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $parts = explode('|', $line, 2);
    $serviceLinks[] = [
        'label' => trim($parts[0]),
        'url'   => trim($parts[1] ?? ($homeUrl . '#kategori'))
    ];
}

$infoTitle = function_exists('get_setting') ? get_setting('footer_info_title', 'Informasi') : 'Informasi';
$infoLinks = [
    ['label' => 'Tentang Kami', 'url' => $profilUrl],
    ['label' => 'Aset Logo',   'url' => $asetLogoUrl],
    ['label' => 'Layouting',   'url' => $layoutingUrl],
    ['label' => 'Hubungi Kami', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $footerWa) . '?text=' . rawurlencode('Halo Turikale Print, saya ingin konsultasi atau pemesanan.')],
];
?>
<footer class="site-footer" role="contentinfo">
    <div class="wrap">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="<?= $homeUrl ?>" aria-label="Turikale Print - Beranda">
                    <img src="<?= htmlspecialchars($footerLogo) ?>" alt="Turikale Print" class="footer-logo">
                </a>
                <p class="footer-about-text">
                    <?= htmlspecialchars($footerDesc) ?>
                </p>
                <div class="footer-social-icons">
                    <?php if (!empty($tiktokUrl)): ?>
                    <a href="<?= htmlspecialchars($tiktokUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok Turikale Print" title="TikTok">
                        <img src="<?= $bp ?>assets/images/ikon-tiktok.png" alt="TikTok">
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($instagramUrl)): ?>
                    <a href="<?= htmlspecialchars($instagramUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram Turikale Print" title="Instagram">
                        <img src="<?= $bp ?>assets/images/ikon-instagram.png" alt="Instagram">
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($footerWa)): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $footerWa) ?>?text=<?= rawurlencode('Halo Turikale Print, saya ingin konsultasi atau pemesanan.') ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Turikale Print" title="WhatsApp (+62 878-4106-4160)">
                        <img src="<?= $bp ?>assets/images/ikon-wa.png" alt="WhatsApp">
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-nav">
                <h5><?= htmlspecialchars($servicesTitle) ?></h5>
                <ul>
                    <?php foreach ($serviceLinks as $sLink): ?>
                    <li><a href="<?= htmlspecialchars($sLink['url']) ?>"><?= htmlspecialchars($sLink['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-info">
                <h5><?= htmlspecialchars($infoTitle) ?></h5>
                <ul>
                    <?php foreach ($infoLinks as $iLink): ?>
                    <li><a href="<?= htmlspecialchars($iLink['url']) ?>"><?= htmlspecialchars($iLink['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="footer-contact-list">
                    <div class="f-contact-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span><?= htmlspecialchars($footerHours) ?></span>
                    </div>
                    <div class="f-contact-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span><?= htmlspecialchars($footerPhone1) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <span><?= htmlspecialchars($footerCopyright) ?></span>
            <span><?= htmlspecialchars($companyName) ?></span>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp Button (Sudut Kanan Bawah untuk Konsultasi & Pemesanan) -->
<a
    href="https://wa.me/6287841064160?text=<?= rawurlencode('Halo Turikale Print, saya ingin konsultasi atau pemesanan.') ?>"
    class="floating-wa-btn"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Konsultasi atau Pemesanan via WhatsApp (+62 878-4106-4160)"
    title="Konsultasi & Pemesanan WhatsApp (+62 878-4106-4160)"
>
    <span class="floating-wa-tooltip">Konsultasi / Pemesanan</span>
    <span class="floating-wa-icon-box">
        <svg viewBox="0 0 24 24" class="floating-wa-icon" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </span>
</a>
