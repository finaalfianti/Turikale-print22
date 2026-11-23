<?php
$currentPage = 'beranda';
$basePath    = '';

require_once 'config/database.php';

$spandukPrice = 25000;
$settingPrice = function_exists('get_setting') ? get_setting('calc_base_price') : null;
if ($settingPrice !== null && $settingPrice !== '') {
    $spandukPrice = (int)$settingPrice;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmtP = $pdo->query("SELECT price FROM tp_product_prices WHERE product_name = 'Spanduk' AND status = 1 LIMIT 1");
        $rowP = $stmtP->fetch();
        if ($rowP && isset($rowP['price'])) {
            $spandukPrice = (int)$rowP['price'];
        }
    } catch (Throwable $e) {}
}

$calcFootnote = function_exists('get_setting') ? get_setting('calc_footnote', '* Harga belum termasuk ongkos jasa desain.') : '* Harga belum termasuk ongkos jasa desain.';
$workshopAddress = function_exists('get_setting') ? get_setting('workshop_address', "Jl. Nasrun Amrullah No. 5, Pettuadae,\nKec. Turikale, Kabupaten Maros,\nSulawesi Selatan 90511") : "Jl. Nasrun Amrullah No. 5, Pettuadae,\nKec. Turikale, Kabupaten Maros,\nSulawesi Selatan 90511";
$workshopHours = function_exists('get_setting') ? get_setting('working_hours', 'Senin – Minggu: 08.00 – 21.00') : 'Senin – Minggu: 08.00 – 21.00';
$phone1 = function_exists('get_setting') ? get_setting('contact_phone_1', '+62 878-4106-4160') : '+62 878-4106-4160';
$phone2 = function_exists('get_setting') ? get_setting('contact_phone_2', '+62 815-2401-1136') : '+62 815-2401-1136';
$phone3 = function_exists('get_setting') ? get_setting('contact_phone_3', '+62 896-0149-3996') : '+62 896-0149-3996';
$gmapsIframe = function_exists('get_setting') ? get_setting('gmaps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12399.668396465226!2d119.56715520000002!3d-5.0193679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbef92997e8dd0b%3A0xf34aa0af5aa85dd9!2sTurikale%20Print!5e1!3m2!1sid!2sid!4v1772575540497!5m2!1sid!2sid') : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12399.668396465226!2d119.56715520000002!3d-5.0193679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbef92997e8dd0b%3A0xf34aa0af5aa85dd9!2sTurikale%20Print!5e1!3m2!1sid!2sid!4v1772575540497!5m2!1sid!2sid';

$pageTitle       = 'Turikale Print - Solusi untuk Setiap Kebutuhan Cetakmu';
$pageDescription = 'Turikale Print melayani cetak spanduk, banner, dokumen, ID card, ATK, stiker, foto dan desain grafis profesional di Maros.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css?v=<?= file_exists('assets/css/main.css') ? filemtime('assets/css/main.css') : '1.0' ?>">
</head>
<body>

<!-- ==========================================================================
     1. NAVIGATION HEADER
     ========================================================================== -->
<?php include 'includes/navbar.php'; ?>

<!-- ==========================================================================
     2. HERO SECTION
     ========================================================================== -->
<section class="hero" id="beranda">
    <div class="hero-bg-layer">
        <picture>
            <source srcset="assets/images/hero-printer-blend.webp" type="image/webp">
            <img src="assets/images/hero-printer-blend.png" alt="Canon Printer Printing" class="hero-bg-printer">
        </picture>
        <div class="hero-gradient-overlay"></div>
    </div>
    <div class="wrap hero-wrap">
        <div class="hero-stickers">
            <img src="assets/images/snap-crop.png" alt="SNAP IT" class="sticker-snap">
            <img src="assets/images/style-crop.png" alt="STYLE IT" class="sticker-style">
            <img src="assets/images/print-crop.png" alt="PRINT IT" class="sticker-print">
        </div>
    </div>
</section>

<!-- ==========================================================================
     3. INTRO SECTION ("Wujudkan Ide!")
     ========================================================================== -->
<section class="intro-section">
    <div class="wrap">
        <div class="intro-heading">
            <h2>Wujudkan Ide!<br><span>Solusi untuk Setiap Kebutuhan Cetakmu</span></h2>
            <a href="#kunjungi" class="btn-come-to-us">Come to Us</a>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN CONTENT
     ========================================================================== -->
<main class="wrap" id="main-content">
    
    <!-- 4. TABS KATEGORI -->
    <div class="tabs-bar" id="tabsBar">
        <button type="button" class="tab-btn active" data-tab="spanduk">Spanduk</button>
        <button type="button" class="tab-btn" data-tab="banner">Banner</button>
        <button type="button" class="tab-btn" data-tab="printing">Printing</button>
        <button type="button" class="tab-btn" data-tab="idcard">ID Card</button>
        <button type="button" class="tab-btn" data-tab="laminating">Laminating</button>
        <button type="button" class="tab-btn" data-tab="atk">ATK</button>
        <button type="button" class="tab-btn" data-tab="stiker">Stiker</button>
        <button type="button" class="tab-btn" data-tab="cetakfoto">Cetak Foto</button>
        <button type="button" class="tab-btn" data-tab="desain">Desain</button>
    </div>

    <!-- 5. PRODUCT PANEL -->
    <div class="service-panel" id="servicePanel">
        <div class="panel-content">
            <h3 id="panelTitle">Setiap Usaha &amp; Acara Butuh Tampil Menarik</h3>
            <p id="panelDesc">Punya acara atau usaha yang butuh perhatian lebih? Spanduk dari Turikale Print siap jadi solusi andalan Anda.</p>
            
            <div class="panel-q">Apa yang Anda dapatkan? &rarr;</div>
            <div class="panel-q-sub" id="panelSubtext">Hasil terbaik untuk setiap kebutuhan spanduk Anda secara maksimal.</div>
            
            <hr class="panel-divider">
            
            <div class="panel-features" id="panelFeatures">
                <div class="feat-row">
                    <img src="assets/images/logo-color-palette.png" alt="" class="feat-icon">
                    <span id="feat1">Warna Tajam &amp; Awet</span>
                </div>
                <div class="feat-row">
                    <img src="assets/images/logo-timer.png" alt="" class="feat-icon">
                    <span id="feat2">Pengerjaan Cepat</span>
                </div>
                <div class="feat-row">
                    <img src="assets/images/ikon-harga.png" alt="" class="feat-icon">
                    <span id="feat3">Harga Terjangkau</span>
                </div>
            </div>
        </div>
        
        <div class="panel-media">
            <img src="assets/images/spanduk.png" alt="Mesin Cetak Spanduk" id="panelImg" loading="lazy">
        </div>
    </div>

    <!-- 6. SOLUSI ADMINISTRASI & DOKUMEN ANDA -->
    <div class="sec-header">
        <h3 class="sec-title">Solusi Administrasi &amp; Dokumen Anda</h3>
        <p class="sec-subtitle">Wujudkan kelancaran urusan administrasimu bersama Turikale Print</p>
    </div>
    
    <div class="admin-cards-grid">
        <div class="admin-card card-purple-1">
            <div class="card-thumb">
                <img src="assets/images/pengetikan.png" alt="Jasa Pengetikan & Format Dokumen" loading="lazy">
            </div>
            <h4>Jasa Pengetikan &amp; Format Dokumen</h4>
            <p>Makalah, laporan, surat-menyurat, hingga transkip data rapi tanpa repot.</p>
        </div>
        
        <div class="admin-card card-purple-2">
            <div class="card-thumb">
                <img src="assets/images/pendaftaran.png" alt="Bantuan Pendaftaran Online" loading="lazy">
            </div>
            <h4>Bantuan Pendaftaran Online</h4>
            <p>Layanan bantuan pendaftaran CPNS, PPPK, kuliah, dan instansi resmi.</p>
        </div>
        
        <div class="admin-card card-purple-3">
            <div class="card-thumb">
                <img src="assets/images/olah-dokumen.png" alt="Pembuatan & Pengolahan Dokumen" loading="lazy">
            </div>
            <h4>Pembuatan &amp; Pengolahan Dokumen</h4>
            <p>Scan, convert, edit PDF, hingga penyusunan berkas lamaran siap kirim.</p>
        </div>
    </div>

    <!-- 7. KALKULATOR SPANDUK -->
    <div class="sec-header">
        <h3 class="sec-title">Ukur Sendiri, Pesan Spanduk Tanpa Ragu</h3>
        <p class="sec-subtitle">Masukkan ukuran panjang dan lebar sesuai kebutuhan, harga langsung muncul otomatis</p>
    </div>
    
    <div class="calc-section-grid" id="calc-section">
        <div class="calc-card-img">
            <img src="assets/images/Calculator.png" alt="Kalkulator Spanduk" loading="lazy">
        </div>
        
        <div class="calc-card-form">
            <h4 class="calc-card-title">Kalkulator Spanduk</h4>
            
            <div class="calc-input-group">
                <label for="panjang">Panjang (cm)</label>
                <input type="number" id="panjang" min="0" placeholder="Contoh: 200">
            </div>
            
            <div class="calc-input-group">
                <label for="lebar">Lebar (cm)</label>
                <input type="number" id="lebar" min="0" placeholder="Contoh: 100">
            </div>
            
            <div class="calc-btn-row">
                <button type="button" class="btn-calc-hit" id="hitungHargaBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3h5v2h-5V6zm-5 0h3v2H7V6zm0 4h3v2H7v-2zm0 4h3v2H7v-2zm9 4h-5v-2h5v2zm0-4h-2v-2h2v2zm0-4h-2v-2h2v2zm-4 4h-1v-2h1v2z"/>
                    </svg>
                    <span>Hitung</span>
                </button>
                <button type="button" class="btn-calc-ref" id="refBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
            
            <div class="calc-price-box">
                <span class="est-text">Estimasi Harga</span>
                <div class="est-val" id="hasilHarga">Rp 50.000</div>
                <small class="est-sub"><?= htmlspecialchars($calcFootnote) ?></small>
            </div>
        </div>
    </div>

    <!-- 8. KUNJUNGI WORKSHOP KAMI -->
    <div class="workshop-layout" id="kunjungi">
        <div class="workshop-text-col">
            <h3 class="workshop-title">Kunjungi Workshop Kami</h3>
            <p class="workshop-desc">Kami berlokasi di jantung Turikale, siap melayani konsultasi desain dan kebutuhan cetak Anda secara langsung.</p>
            
            <div class="workshop-item">
                <div class="workshop-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                    </svg>
                </div>
                <div class="workshop-detail">
                    <strong>Alamat</strong>
                    <p><?= nl2br(htmlspecialchars($workshopAddress)) ?></p>
                </div>
            </div>
            
            <div class="workshop-item">
                <div class="workshop-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"/>
                    </svg>
                </div>
                <div class="workshop-detail">
                    <strong>Jam Operasional</strong>
                    <p><?= htmlspecialchars($workshopHours) ?></p>
                </div>
            </div>
            
            <div class="workshop-item">
                <div class="workshop-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                </div>
                <div class="workshop-detail">
                    <strong>Hubungi Kami</strong>
                    <p><?= htmlspecialchars($phone1) ?><br><?= htmlspecialchars($phone2) ?><br><?= htmlspecialchars($phone3) ?></p>
                </div>
            </div>
        </div>
        
        <div class="workshop-map-col">
            <iframe src="<?= htmlspecialchars($gmapsIframe) ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

</main>

<!-- ==========================================================================
     9. FOOTER
     ========================================================================== -->
<?php include 'includes/footer.php'; ?>

<!-- ==========================================================================
     CLIENT SCRIPTS
     ========================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('show');
        });
    }

    // 2. Interactive Category Tabs Data
    const tabData = {
        spanduk: {
            title: "Setiap Usaha & Acara Butuh Tampil Menarik",
            desc: "Punya acara atau usaha yang butuh perhatian lebih? Spanduk dari Turikale Print siap jadi solusi andalan Anda.",
            subtext: "Hasil terbaik untuk setiap kebutuhan spanduk Anda secara maksimal.",
            feat1: "Warna Tajam & Awet",
            feat2: "Pengerjaan Cepat",
            feat3: "Harga Terjangkau",
            img: "assets/images/spanduk.png"
        },
        banner: {
            title: "Promosi Praktis & Elegan dengan Banner",
            desc: "X-Banner, Y-Banner, dan Roll-Up Banner berkualitas tinggi, mudah dibawa dan siap memikat audiens acara Anda.",
            subtext: "Tampilan visual menarik untuk stand pameran, event kantor, maupun outlet.",
            feat1: "Material Tahan Air",
            feat2: "Rangka Kuat & Kokoh",
            feat3: "Cetak Beresolusi Tinggi",
            img: "assets/images/brosur-benefit.webp"
        },
        printing: {
            title: "Cetak Dokumen Cepat, Rapi & Tajam",
            desc: "Layanan print hitam putih dan warna untuk laporan kantor, tugas akhir, skripsi, modul, dan kebutuhan surat resmi.",
            subtext: "Kualitas cetakan tajam dengan ketebalan kertas beragam sesuai kebutuhan.",
            feat1: "Kertas Berkualitas",
            feat2: "Warna Akurat & Presisi",
            feat3: "Siap Jilid Langsung",
            img: "assets/images/foto-temp.jpg"
        },
        idcard: {
            title: "ID Card & Kartu Member Profesional",
            desc: "Cetak kartu tanda pengenal karyawan, kartu pelajar, atau kartu keanggotaan dengan bahan PVC tebal standar kartu ATM.",
            subtext: "Desain custom dengan ketahanan luar biasa tanpa khawatir mengelupas.",
            feat1: "Standar Bahan PVC Tebal",
            feat2: "Anti Air & Tidak Pudar",
            feat3: "Bisa Barcode / QR Code",
            img: "assets/images/foto-temp.jpg"
        },
        laminating: {
            title: "Perlindungan Maksimal Dokumen Berharga",
            desc: "Lindungi ijazah, sertifikat, kartu keluarga, dan berkas penting dari basah, debu, dan kerusakan fisik.",
            subtext: "Laminasi panas berdaya rekat kuat dengan hasil jernih mengkilap.",
            feat1: "Plastik Presisi & Bening",
            feat2: "Tahan Kelembaban",
            feat3: "Pengerjaan Langsung Jadi",
            img: "assets/images/foto-temp.jpg"
        },
        atk: {
            title: "Kebutuhan Alat Tulis Kantor Lengkap",
            desc: "Menyediakan aneka kebutuhan administrasi kantor dan sekolah, mulai dari kertas, amplop, map, hingga binder.",
            subtext: "Pilihan produk bermutu tinggi untuk menunjang aktivitas kerja setiap hari.",
            feat1: "Stok Lengkap & Selalu Tersedia",
            feat2: "Harga Terjangkau & Grosir",
            feat3: "Kualitas Terpercaya",
            img: "assets/images/hero-ATK.webp"
        },
        stiker: {
            title: "Stiker Label Produk & Kemasan Menarik",
            desc: "Tingkatkan nilai jual produk Anda dengan stiker cromo, vinyl, atau transparan berdaya rekat kuat dan cutting presisi.",
            subtext: "Cetak stiker custom bentuk kiss-cut maupun die-cut siap tempel.",
            feat1: "Cutting Sangat Presisi",
            feat2: "Tahan Air & Minyak",
            feat3: "Daya Rekat Super Kuat",
            img: "assets/images/stiker-benefit.webp"
        },
        cetakfoto: {
            title: "Abadikan Momen Indah Kualitas Studio",
            desc: "Cetak foto ukuran pas foto resmi, 4R, hingga ukuran besar dengan kertas foto premium anti pudar puluhan tahun.",
            subtext: "Pilihan finishing glossy maupun doff sesuai selera Anda.",
            feat1: "Kertas Foto Premium",
            feat2: "Warna Hidup & Tajam",
            feat3: "Tahan Lama & Anti Pudar",
            img: "assets/images/foto-temp.jpg"
        },
        desain: {
            title: "Jasa Desain Grafis Kreatif & Siap Cetak",
            desc: "Tim desainer kami siap mewujudkan konsep visual terbaik untuk spanduk, banner, logo, brosur, hingga kartu nama.",
            subtext: "Konsultasi ramah dan revisi fleksibel hingga hasil memuaskan.",
            feat1: "Konsep Kreatif & Unik",
            feat2: "Konsultasi Langsung",
            feat3: "File Siap Cetak Presisi",
            img: "assets/images/brosur-benefit.webp"
        }
    };

    const tabButtons = document.querySelectorAll('.tab-btn');
    const panelTitle = document.getElementById('panelTitle');
    const panelDesc = document.getElementById('panelDesc');
    const panelSubtext = document.getElementById('panelSubtext');
    const feat1 = document.getElementById('feat1');
    const feat2 = document.getElementById('feat2');
    const feat3 = document.getElementById('feat3');
    const panelImg = document.getElementById('panelImg');
    const servicePanel = document.getElementById('servicePanel');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const tabKey = this.getAttribute('data-tab');
            const data = tabData[tabKey];
            if (data && servicePanel) {
                servicePanel.style.opacity = '0.7';
                setTimeout(() => {
                    panelTitle.textContent = data.title;
                    panelDesc.textContent = data.desc;
                    if (panelSubtext) panelSubtext.textContent = data.subtext;
                    feat1.textContent = data.feat1;
                    feat2.textContent = data.feat2;
                    feat3.textContent = data.feat3;
                    panelImg.src = data.img;
                    servicePanel.style.opacity = '1';
                }, 120);
            }
        });
    });

    // 3. Spanduk Price Calculator
    const PRICE_PER_M2 = <?= (int)$spandukPrice ?>;
    const inputPanjang = document.getElementById('panjang');
    const inputLebar = document.getElementById('lebar');
    const hasilHarga = document.getElementById('hasilHarga');
    const hitungBtn = document.getElementById('hitungHargaBtn');
    const refBtn = document.getElementById('refBtn');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function hitungSpanduk() {
        const p = parseFloat(inputPanjang.value);
        const l = parseFloat(inputLebar.value);

        if (!p || !l || p <= 0 || l <= 0) {
            hasilHarga.textContent = 'Rp 0';
            return;
        }

        const luasM2 = (p / 100) * (l / 100);
        const total = Math.max(luasM2 * PRICE_PER_M2, 10000);
        hasilHarga.textContent = formatRupiah(total);
    }

    if (hitungBtn) {
        hitungBtn.addEventListener('click', hitungSpanduk);
    }

    if (refBtn) {
        refBtn.addEventListener('click', function () {
            if (inputPanjang) inputPanjang.value = '';
            if (inputLebar) inputLebar.value = '';
            if (hasilHarga) hasilHarga.textContent = 'Rp 50.000';
        });
    }
});
</script>

<script>

(function () {
    var links = document.querySelectorAll('.nav-item[data-fallback]');
    if (!links.length) return;

    // Cek dengan HEAD request ke /profil (salah satu clean URL)
    var testUrl = '/profil';
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', testUrl, true);
    xhr.timeout = 2000;

    xhr.onload = function () {
        if (xhr.status === 404 || xhr.status === 0) {
            useFallback();
        }
        // status 200 / 302 / 301 → router aktif, biarkan href aslinya
    };
    xhr.onerror   = useFallback;
    xhr.ontimeout = useFallback;
    xhr.send();

    function useFallback() {
        links.forEach(function (a) {
            var fallback = a.getAttribute('data-fallback');
            if (fallback) a.href = fallback;
        });
    }
})();
</script>
</body>
</html>
