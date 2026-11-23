<?php
$currentPage    = 'desain';
$basePath       = '../';

require_once __DIR__ . '/../config/database.php';

$pageTitle      = 'Desain & Portofolio Kreatif – Turikale Print Maros';
$pageDescription = 'Temukan desain terbaikmu. Galeri portofolio desain spanduk, kartu nama, stempel, dan identitas visual UMKM di Maros.';
$canonicalUrl   = 'https://turikaleprint.store/pages/desain.php';

$floatingWaPhone = get_setting('floating_wa_phone', '6287841064160');
$floatingWaMsg   = get_setting('floating_wa_template', 'Halo Turikale Print, saya ingin konsultasi desain.');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    
    <!-- Favicon & Fonts -->
    <link rel="icon" type="image/jpeg" href="../assets/images/Logo-Icon-TurikalePrint.jpg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    
    <!-- CSS Utama & CSS Halaman Desain -->
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/desain.css" />
</head>
<body>

    <div class="page-wrapper">
        <!-- Include Navbar -->
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <main id="main-content" class="site-main">
            
            <!-- HERO SECTION DESAIN -->
            <section class="desain-hero">
                <div class="wrap text-center">
                    <span class="desain-hero-badge">Kreativitas & Identitas Visual</span>
                    <h1 class="desain-hero-title">D E S A I N</h1>
                    <div class="desain-hero-subtitle-box">
                        <h2>Temukan Desain Terbaikmu</h2>
                        <p>Wujudkan ide Anda menjadi identitas visual yang berkarakter</p>
                    </div>
                    <a href="#video-section" class="btn-explore-desain">Mulai Lihat Desain</a>
                </div>
            </section>

            <!-- SECTION VIDEO PROSES PENGERJAAN -->
            <section id="video-section" class="wrap py-12">
                <div class="video-showcase-panel">
                    <div class="video-panel-header">
                        <span class="sub-label">01 / DI BALIK LAYAR KERJA</span>
                        <a href="https://wa.me/<?= htmlspecialchars($floatingWaPhone) ?>" class="link-consult">Konsultasi Desain Gratis &rarr;</a>
                    </div>
                    <div class="video-wrapper">
                        <video class="process-video" controls poster="../assets/images/video-poster.jpg">
                            <source src="../assets/videos/proses-desain.mp4" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                    </div>
                </div>
            </section>

            <!-- SECTION GALERI PORTFOLIO & FILTER KATEGORI -->
            <section class="wrap py-12">
                <div class="portfolio-header-block">
                    <div>
                        <h2 class="section-main-title">Karya Terbaik Kami untuk Kesuksesan Bisnis Anda</h2>
                        <p class="section-main-desc">Wujudkan kesan positif di mata konsumen lewat karya profesional Turikale Print.</p>
                    </div>
                    
                    <!-- Kategori Filter Tab -->
                    <div class="portfolio-filters" role="tablist">
                        <button class="filter-btn active" data-filter="all" role="tab" aria-selected="true">Semua</button>
                        <button class="filter-btn" data-filter="logo" role="tab" aria-selected="false">Brand Identitas</button>
                        <button class="filter-btn" data-filter="spanduk" role="tab" aria-selected="false">Spanduk</button>
                        <button class="filter-btn" data-filter="lainnya" role="tab" aria-selected="false">Lainnya</button>
                    </div>
                </div>

                <div class="sub-counter">02 / GALERI DESAIN PILIHAN</div>

                <!-- GRID KARTU PORTFOLIO (Sesuai Gambar Referensi) -->
                <div class="portfolio-grid" id="portfolioGrid">
                    
                    <!-- Card 1 -->
                    <div class="port-card card-light" data-category="logo">
                        <div class="port-top-info">01 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">IDE<br>BESAR.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Spanduk & Banner</span>
                            <span class="port-num">01</span>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="port-card card-navy" data-category="logo">
                        <div class="port-top-info">02 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">tempel<br>cerita.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Sticker & Packaging</span>
                            <span class="port-num">02</span>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="port-card card-light" data-category="lainnya">
                        <div class="port-top-info">03 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">Halo,<br>kenalan!</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Kartu Nama</span>
                            <span class="port-num">03</span>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="port-card card-light" data-category="spanduk">
                        <div class="port-top-info">04 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">JEJAK<br>KARYA.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Spanduk</span>
                            <span class="port-num">04</span>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="port-card card-light" data-category="spanduk">
                        <div class="port-top-info">05 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">JADI<br>SOROTAN.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Banner</span>
                            <span class="port-num">05</span>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="port-card card-navy" data-category="lainnya">
                        <div class="port-top-info">06 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">RUANG<br>EKSPRESI.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Outdoor Media</span>
                            <span class="port-num">06</span>
                        </div>
                    </div>

                    <!-- Card 7 -->
                    <div class="port-card card-navy" data-category="logo">
                        <div class="port-top-info">07 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">Nama,<br>jadi makna.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Kartu Nama</span>
                            <span class="port-num">07</span>
                        </div>
                    </div>

                    <!-- Card 8 -->
                    <div class="port-card card-light" data-category="spanduk">
                        <div class="port-top-info">08 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">BEBAS<br>BERKARYA.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Spanduk</span>
                            <span class="port-num">08</span>
                        </div>
                    </div>

                    <!-- Card 9 -->
                    <div class="port-card card-light" data-category="lainnya">
                        <div class="port-top-info">09 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">Kecil.<br>Berarti.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Stiker</span>
                            <span class="port-num">09</span>
                        </div>
                    </div>

                    <!-- Card 10 -->
                    <div class="port-card card-navy" data-category="spanduk">
                        <div class="port-top-info">10 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">CERITA<br>VISUAL.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Banner</span>
                            <span class="port-num">10</span>
                        </div>
                    </div>

                    <!-- Card 11 -->
                    <div class="port-card card-light" data-category="lainnya">
                        <div class="port-top-info">11 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">TANDA<br>KHAS.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Stempel</span>
                            <span class="port-num">11</span>
                        </div>
                    </div>

                    <!-- Card 12 -->
                    <div class="port-card card-navy" data-category="lainnya">
                        <div class="port-top-info">12 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">LIHAT<br>LEBIH.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Outdoor Media</span>
                            <span class="port-num">12</span>
                        </div>
                    </div>

                    <!-- Card 13 -->
                    <div class="port-card card-light" data-category="lainnya">
                        <div class="port-top-info">13 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">Suka<br>suka.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Stiker</span>
                            <span class="port-num">13</span>
                        </div>
                    </div>

                    <!-- Card 14 -->
                    <div class="port-card card-navy" data-category="logo">
                        <div class="port-top-info">14 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">Satu<br>identitas.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Kartu Nama</span>
                            <span class="port-num">14</span>
                        </div>
                    </div>

                    <!-- Card 15 -->
                    <div class="port-card card-light" data-category="spanduk">
                        <div class="port-top-info">15 / IDENTITAS BISNIS</div>
                        <h3 class="port-card-title">CETAK<br>IDEMU.</h3>
                        <div class="port-card-footer">
                            <span class="port-type">Spanduk</span>
                            <span class="port-num">15</span>
                        </div>
                    </div>

                </div>

                <!-- Footer Callout di Bawah Grid -->
                <div class="portfolio-bottom-bar">
                    <span class="bottom-brand">PUNYA IDE CERUT?</span>
                    <a href="https://wa.me/<?= htmlspecialchars($floatingWaPhone) ?>" class="bottom-link">Konsultasi Sekarang &rarr;</a>
                </div>
            </section>

        </main>

        <!-- Include Footer -->
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>

    <!-- Script Filter Interaktif & Mobile Menu -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const cards = document.querySelectorAll('.port-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Update tombol aktif
                    filterBtns.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');

                    const filterValue = btn.getAttribute('data-filter');

                    // Filter kartu
                    cards.forEach(card => {
                        if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
    <script src="../assets/js/main.js" defer></script>
</body>
</html>