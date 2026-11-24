<?php
$currentPage = 'profil';
$basePath    = '../';

require_once __DIR__ . '/../config/database.php';

$pageTitle       = 'Profil Turikale Print – Percetakan Terpercaya di Maros sejak 2017';
$pageDescription = 'Turikale Print adalah percetakan digital profesional di Kabupaten Maros sejak 2017. Bagian dari CV. Opu Barakati Jaya, melayani UMKM, kantor dinas, dan masyarakat umum.';
$canonicalUrl    = 'https://turikaleprint.store/pages/profil.php';

$heroTitle = function_exists('get_setting') ? get_setting('profile_hero_title', 'Membangun Kepercayaan Melalui Kualitas Cetak Terbaik Sejak 2017') : 'Membangun Kepercayaan Melalui Kualitas Cetak Terbaik Sejak 2017';
$heroDesc = function_exists('get_setting') ? get_setting('profile_hero_desc', 'Turikale Print hadir sebagai mitra solusi cetak UMKM, kantor pemerintahan, sekolah, dan masyarakat luas di Kabupaten Maros.') : 'Turikale Print hadir sebagai mitra solusi cetak UMKM, kantor pemerintahan, sekolah, dan masyarakat luas di Kabupaten Maros.';

// Stats
$stats = [
    ['icon' => 'check', 'label' => 'Total Pesanan', 'value' => '500+ Selesai', 'highlight' => false],
    ['icon' => 'group', 'label' => 'Pelanggan Aktif', 'value' => '50+ Perbulan', 'highlight' => false],
    ['icon' => 'star', 'label' => 'Rating Google', 'value' => '4.9★ Verified', 'highlight' => true],
];

// Timeline Events
$timelineEvents = [];
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmtTl = $pdo->query("SELECT * FROM tp_timeline WHERE status = 1 ORDER BY sort_order ASC, id ASC");
        $timelineEvents = $stmtTl->fetchAll();
    } catch (Throwable $e) {}
}

if (empty($timelineEvents)) {
    $timelineEvents = [
        [
            'year_text'   => '2017',
            'title'       => 'Awal Berdiri',
            'description' => 'Berdiri di Turikale Maros melayani fotokopi dan cetak dokumen sederhana untuk pelajar, mahasiswa, dan masyarakat umum.',
            'highlight'   => false
        ],
        [
            'year_text'   => '2020',
            'title'       => 'Ekspansi Mesin Digital',
            'description' => 'Menambah mesin digital printing indoor dan outdoor untuk memenuhi tingginya permintaan spanduk, baliho, dan stiker label.',
            'highlight'   => false
        ],
        [
            'year_text'   => '2022',
            'title'       => 'Digitalisasi Layanan',
            'description' => 'Mulai melayani pemesanan online via WhatsApp dan website katalog untuk memudahkan pelanggan memesan tanpa antre.',
            'highlight'   => false
        ],
        [
            'year_text'   => '2023',
            'title'       => 'Pengembangan Tim & Edukasi',
            'description' => 'Membuka program kemitraan magang nasional & PKL vokasi untuk mencetak talenta grafis muda berbakat.',
            'highlight'   => false
        ],
        [
            'year_text'   => 'Sekarang',
            'title'       => 'CV. Opu Barakati Jaya',
            'description' => 'Resmi berbadan hukum dan terus bertumbuh sebagai penyedia layanan percetakan terpercaya nomor satu di Kabupaten Maros.',
            'highlight'   => true
        ],
    ];
}

// Founder Info
$ownerPhoto = function_exists('get_setting') ? get_setting('profile_owner_photo', 'assets/images/owner-image.png') : 'assets/images/owner-image.png';
$ownerTagline = function_exists('get_setting') ? get_setting('profile_owner_tagline', 'Founding Vision') : 'Founding Vision';
$ownerName = function_exists('get_setting') ? get_setting('profile_owner_name', 'Andry Ridwan') : 'Andry Ridwan';
$ownerTitle = function_exists('get_setting') ? get_setting('profile_owner_title', 'Founder & Director CV. Opu Barakati Jaya') : 'Founder & Director CV. Opu Barakati Jaya';
$ownerBio = function_exists('get_setting') ? get_setting('profile_owner_bio', 'Komitmen kami adalah memberikan hasil cetak terbaik dengan harga yang bersahabat. Setiap lembar cetakan adalah cerminan kepercayaan pelanggan yang harus kami jaga dengan sepenuh hati.') : 'Komitmen kami adalah memberikan hasil cetak terbaik dengan harga yang bersahabat. Setiap lembar cetakan adalah cerminan kepercayaan pelanggan yang harus kami jaga dengan sepenuh hati.';
$floatingWaPhone = function_exists('get_setting') ? get_setting('floating_wa_phone', '6287841064160') : '6287841064160';

// Team Members (Tim Inti)
$coreTeam = [
    [
        'name'     => 'Sri Handayani',
        'instansi' => 'Turikale Print',
        'role'     => 'Administrasi & Keuangan',
        'photo'    => 'assets/images/suci-img.jpg',
    ],
    [
        'name'     => 'Mahdi',
        'instansi' => 'Turikale Print',
        'role'     => 'Operator Mesin Digital',
        'photo'    => 'assets/images/mahdi-img.jpg',
    ],
    [
        'name'     => 'Rifan Hardian',
        'instansi' => 'Turikale Print',
        'role'     => 'Graphic Designer',
        'photo'    => 'assets/images/rifan.jpeg',
    ],
    [
        'name'     => 'Fajar Pratama',
        'instansi' => 'Turikale Print',
        'role'     => 'Finishing & Quality Control',
        'photo'    => 'assets/images/adit-img.jpg',
    ],
];

// PKL Members (SMK / Vokasi) - 4 Anggota Utama
$pklTeam = [
    [
        'name'     => 'Rizky',
        'instansi' => 'SMK Negeri 1 Maros',
        'role'     => 'Siswa PKL',
        'photo'    => 'assets/images/riki-img.jpeg',
        'year'     => '2025',
    ],
    [
        'name'     => 'Anggi',
        'instansi' => 'SMK Negeri 1 Maros',
        'role'     => 'Siswa PKL',
        'photo'    => 'assets/images/anggi-img.jpeg',
        'year'     => '2025',
    ],
    [
        'name'     => 'Pute',
        'instansi' => 'SMK Negeri 1 Maros',
        'role'     => 'Siswa PKL',
        'photo'    => 'assets/images/pute-img.jpeg',
        'year'     => '2025',
    ],
    [
        'name'     => 'Salwa',
        'instansi' => 'SMK Negeri 1 Maros',
        'role'     => 'Siswa PKL',
        'photo'    => 'assets/images/salwa-img.jpeg',
        'year'     => '2025',
    ],
];

// Magang Nasional Members - Kategori Tahun 2025 (Batch 1 & Batch 2)
$magangTeam = [
    // --- Tahun 2025: Batch 1 ---
    [
        'name'     => 'Dila',
        'instansi' => 'Universitas Negeri Makassar',
        'role'     => 'Desain Grafis',
        'photo'    => 'assets/images/dila-img.jpg',
        'batch'    => 'batch-1',
        'year'     => '2025',
    ],
    [
        'name'     => 'Fahmi',
        'instansi' => 'Universitas Hasanuddin',
        'role'     => 'Digital Marketing',
        'photo'    => 'assets/images/mahdi-img.jpg',
        'batch'    => 'batch-1',
        'year'     => '2025',
    ],
    [
        'name'     => 'Lia',
        'instansi' => 'Politeknik Negeri Ujung Pandang',
        'role'     => 'Produksi Cetak',
        'photo'    => 'assets/images/lia-img.jpg',
        'batch'    => 'batch-1',
        'year'     => '2025',
    ],
    [
        'name'     => 'Nurul',
        'instansi' => 'Universitas Negeri Makassar',
        'role'     => 'Administrasi',
        'photo'    => 'assets/images/nurul-img.jpg',
        'batch'    => 'batch-1',
        'year'     => '2025',
    ],
    // --- Tahun 2025: Batch 2 ---
    [
        'name'     => 'Adit',
        'instansi' => 'SMK Negeri Maros',
        'role'     => 'Operator Mesin',
        'photo'    => 'assets/images/adit-img.jpg',
        'batch'    => 'batch-2',
        'year'     => '2025',
    ],
    [
        'name'     => 'Himawan',
        'instansi' => 'Universitas Muslim Indonesia',
        'role'     => 'Multimedia',
        'photo'    => 'assets/images/hima-img.jpg',
        'batch'    => 'batch-2',
        'year'     => '2025',
    ],
    [
        'name'     => 'Arul',
        'instansi' => 'Politeknik Negeri Ujung Pandang',
        'role'     => 'Desain & Layout',
        'photo'    => 'assets/images/arul-img.jpg',
        'batch'    => 'batch-2',
        'year'     => '2025',
    ],
];

// Integrasi Dinamis: Muat data anggota dari database CMS jika telah ditambahkan
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmtMem = $pdo->query("
            SELECT m.*, b.name AS batch_name, s.id AS sec_id
            FROM tp_profile_members m
            JOIN tp_profile_batches b ON m.batch_id = b.id
            JOIN tp_profile_sections s ON b.section_id = s.id
            WHERE m.status = 1
            ORDER BY s.sort_order ASC, b.sort_order ASC, m.sort_order ASC, m.id ASC
        ");
        $cmsMembers = $stmtMem ? $stmtMem->fetchAll(PDO::FETCH_ASSOC) : [];

        if (!empty($cmsMembers)) {
            $cmsCore = [];
            $cmsPkl = [];
            $cmsMagang = [];

            foreach ($cmsMembers as $cm) {
                $year = !empty($cm['year']) ? (string)$cm['year'] : '2025';
                if (empty($cm['year']) && preg_match('/202\d/', $cm['batch_name'], $ym)) {
                    $year = $ym[0];
                }
                $batchSlug = 'batch-1';
                if (preg_match('/batch\s*(\d+)/i', $cm['batch_name'], $bm)) {
                    $batchSlug = 'batch-' . $bm[1];
                } elseif (stripos($cm['batch_name'], 'batch 2') !== false) {
                    $batchSlug = 'batch-2';
                } elseif (stripos($cm['batch_name'], 'batch 3') !== false) {
                    $batchSlug = 'batch-3';
                }

                $item = [
                    'name'     => $cm['name'],
                    'instansi' => $cm['instansi'] ?? '',
                    'role'     => $cm['posisi'] ?: 'Anggota',
                    'photo'    => $cm['photo'] ?? '',
                    'batch'    => $batchSlug,
                    'year'     => $year,
                ];

                if ($cm['sec_id'] == 1) {
                    $cmsCore[] = $item;
                } elseif ($cm['sec_id'] == 2) {
                    $cmsPkl[] = $item;
                } elseif ($cm['sec_id'] == 3) {
                    $cmsMagang[] = $item;
                }
            }

            if (!empty($cmsCore)) $coreTeam = $cmsCore;
            if (!empty($cmsPkl)) $pklTeam = $cmsPkl;
            if (!empty($cmsMagang)) $magangTeam = $cmsMagang;
        }
    } catch (Throwable $e) {}
}
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/profil.css" />
</head>
<body>

    <!-- Include Navbar -->
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <main id="main-content">

        <!-- ==================================================================
             1. HERO SECTION
             ================================================================== -->
        <section class="profile-hero" aria-label="Hero Profil">
            <div class="profile-hero-wrap">
                <h1 class="profile-hero-title"><?= htmlspecialchars($heroTitle) ?></h1>
                <p class="profile-hero-sub"><?= htmlspecialchars($heroDesc) ?></p>

                <div class="profile-stats-grid">
                    <?php foreach ($stats as $st): ?>
                    <div class="prof-stat-card <?= !empty($st['highlight']) ? 'prof-stat-highlight' : '' ?>">
                        <div class="prof-stat-icon">
                            <?php if ($st['icon'] === 'check'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php elseif ($st['icon'] === 'group'): ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <?php else: ?>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <?php endif; ?>
                        </div>
                        <p class="prof-stat-label"><?= htmlspecialchars($st['label']) ?></p>
                        <p class="prof-stat-value"><?= htmlspecialchars($st['value']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             2. TENTANG PERUSAHAAN (TIMELINE)
             ================================================================== -->
        <section class="prof-section" aria-labelledby="timeline-heading">
            <div class="prof-wrap">
                <div class="prof-section-header">
                    <span class="prof-pill-badge">PERJALANAN KAMI</span>
                    <h2 id="timeline-heading" class="prof-section-title">Tentang Perusahaan</h2>
                    <p class="prof-section-desc">Perjalanan dedikasi kami dalam menghadirkan solusi cetak terbaik bagi masyarakat Maros dan sekitarnya.</p>
                </div>

                <div class="timeline-track">
                    <?php foreach ($timelineEvents as $idx => $ev): 
                        $isLeft = ($idx % 2 === 0);
                        $yearText = $ev['year_text'] ?? $ev['year'] ?? '';
                        $isHighlight = !empty($ev['highlight']) || (strtolower($yearText) === 'sekarang');
                    ?>
                    <div class="timeline-row <?= $isLeft ? 'tl-left' : 'tl-right' ?>">
                        <div class="timeline-node" aria-hidden="true">
                            <div class="timeline-node-inner"></div>
                        </div>
                        <div class="timeline-card">
                            <span class="timeline-badge <?= $isHighlight ? 'highlight' : '' ?>"><?= htmlspecialchars($yearText) ?></span>
                            <h3 class="timeline-title"><?= htmlspecialchars($ev['title']) ?></h3>
                            <p class="timeline-desc"><?= htmlspecialchars($ev['description']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             3. FOUNDING VISION (OWNER CARD)
             ================================================================== -->
        <section class="prof-section prof-section-alt" aria-labelledby="founder-heading">
            <div class="prof-wrap">
                <div class="founder-box">
                    <div class="founder-photo-col">
                        <div class="founder-photo-wrapper">
                            <img src="<?= $basePath . htmlspecialchars($ownerPhoto) ?>" alt="<?= htmlspecialchars($ownerName) ?>" loading="lazy" />
                        </div>
                    </div>
                    <div class="founder-info-col">
                        <span class="founder-tag"><?= htmlspecialchars($ownerTagline) ?></span>
                        <h2 id="founder-heading" class="founder-name"><?= htmlspecialchars($ownerName) ?></h2>
                        <div class="founder-designation"><?= htmlspecialchars($ownerTitle) ?></div>
                        <p class="founder-quote"><?= htmlspecialchars($ownerBio) ?></p>
                        
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $floatingWaPhone) ?>?text=<?= rawurlencode('Halo Pak Andry, saya ingin berkonsultasi mengenai cetak.') ?>" target="_blank" rel="noopener noreferrer" class="founder-wa-btn" aria-label="Hubungi Pak Andry lewat WhatsApp">
                            <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            <span>Konsultasi Langsung</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             4. TIM INTI KAMI
             ================================================================== -->
        <section class="prof-section" aria-labelledby="tim-inti-heading">
            <div class="prof-wrap">
                <div class="prof-section-header">
                    <h2 id="tim-inti-heading" class="prof-section-title">Tim Inti Kami</h2>
                    <p class="prof-section-desc">Didukung oleh tenaga profesional yang berdedikasi tinggi di bidang desain, operator mesin, dan layanan pelanggan.</p>
                </div>

                <div class="prof-grid-4">
                    <?php foreach ($coreTeam as $member): ?>
                    <div class="prof-team-card">
                        <div class="prof-avatar-wrap">
                            <?php if (!empty($member['photo']) && file_exists(__DIR__ . '/../' . $member['photo'])): ?>
                            <img src="<?= $basePath . htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" loading="lazy" />
                            <?php else: ?>
                            <span class="prof-avatar-placeholder"><?= mb_substr($member['name'], 0, 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="prof-team-instansi"><?= htmlspecialchars($member['instansi']) ?></p>
                        <h3 class="prof-team-name"><?= htmlspecialchars($member['name']) ?></h3>
                        <span class="prof-team-role"><?= htmlspecialchars($member['role']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             5. PRAKTIK KERJA LAPANGAN (PKL)
             ================================================================== -->
        <section class="prof-section prof-section-alt" aria-labelledby="pkl-heading">
            <div class="prof-wrap">
                <div class="prof-flex-header">
                    <div class="prof-left-heading">
                        <h2 id="pkl-heading" class="prof-section-title">Praktik Kerja Lapangan (PKL)</h2>
                        <p class="prof-section-desc">Memberikan ruang belajar bagi siswa SMK untuk mendalami dunia percetakan dan industri kreatif secara langsung.</p>
                    </div>
                    <span class="prof-action-badge">SMK / Vokasi</span>
                </div>

                <div class="prof-grid-4">
                    <?php foreach ($pklTeam as $student): ?>
                    <div class="prof-team-card">
                        <div class="prof-avatar-wrap">
                            <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/../' . $student['photo'])): ?>
                            <img src="<?= $basePath . htmlspecialchars($student['photo']) ?>" alt="<?= htmlspecialchars($student['name']) ?>" loading="lazy" />
                            <?php else: ?>
                            <span class="prof-avatar-placeholder"><?= mb_substr($student['name'], 0, 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="prof-team-instansi"><?= htmlspecialchars($student['instansi']) ?></p>
                        <h3 class="prof-team-name"><?= htmlspecialchars($student['name']) ?></h3>
                        <span class="prof-team-role"><?= htmlspecialchars($student['role']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================================================================
             6. PROGRAM MAGANG NASIONAL
             ================================================================== -->
        <section class="prof-section" aria-labelledby="magang-heading">
            <div class="prof-wrap">
                <div class="prof-flex-header">
                    <div class="prof-left-heading">
                        <h2 id="magang-heading" class="prof-section-title">Program Magang Nasional</h2>
                        <p class="prof-section-desc">Kolaborasi bersama kementerian ketenagakerjaan dan perguruan tinggi dalam program magang bersertifikat industri percetakan digital.</p>
                    </div>
                    <?php
                    $magangYears = ['2025', '2026'];
                    foreach ($magangTeam as $mItem) {
                        if (!empty($mItem['year']) && !in_array($mItem['year'], $magangYears)) {
                            $magangYears[] = $mItem['year'];
                        }
                    }
                    sort($magangYears);

                    $magangBatches = ['batch-1', 'batch-2', 'batch-3'];
                    foreach ($magangTeam as $mItem) {
                        if (!empty($mItem['batch']) && !in_array($mItem['batch'], $magangBatches)) {
                            $magangBatches[] = $mItem['batch'];
                        }
                    }
                    ?>
                    <div class="prof-filters-wrapper">
                        <div class="prof-filter-row">
                            <span class="prof-filter-label">Kategori Tahun:</span>
                            <button type="button" class="prof-filter-btn active" data-magang-year="all">Semua Tahun</button>
                            <?php foreach ($magangYears as $yVal): ?>
                            <button type="button" class="prof-filter-btn" data-magang-year="<?= htmlspecialchars($yVal) ?>">Tahun <?= htmlspecialchars($yVal) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="prof-filter-row">
                            <span class="prof-filter-label">Kategori Batch:</span>
                            <button type="button" class="prof-filter-btn active" data-magang-batch="all">Semua Batch</button>
                            <?php foreach ($magangBatches as $bVal): 
                                $bNum = str_replace('batch-', '', $bVal);
                                $bLabel = is_numeric($bNum) ? 'Batch ' . $bNum : ucfirst($bNum);
                            ?>
                            <button type="button" class="prof-filter-btn" data-magang-batch="<?= htmlspecialchars($bVal) ?>"><?= htmlspecialchars($bLabel) ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="prof-grid-4" id="magangGrid">
                    <?php foreach ($magangTeam as $intern): 
                        $internYear = $intern['year'] ?? '2025';
                        $batchNum = str_replace('batch-', '', $intern['batch'] ?? '1');
                    ?>
                    <div class="prof-team-card" data-year="<?= htmlspecialchars($internYear) ?>" data-batch="<?= htmlspecialchars($intern['batch']) ?>">
                        <span class="prof-card-tag <?= $internYear === '2026' ? 'tag-2026' : 'tag-2025' ?>">
                            <?= htmlspecialchars($internYear) ?> • Batch <?= htmlspecialchars($batchNum) ?>
                        </span>

                        <div class="prof-avatar-wrap">
                            <?php if (!empty($intern['photo']) && file_exists(__DIR__ . '/../' . $intern['photo'])): ?>
                            <img src="<?= $basePath . htmlspecialchars($intern['photo']) ?>" alt="<?= htmlspecialchars($intern['name']) ?>" loading="lazy" />
                            <?php else: ?>
                            <span class="prof-avatar-placeholder"><?= mb_substr($intern['name'], 0, 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="prof-team-instansi"><?= htmlspecialchars($intern['instansi']) ?></p>
                        <h3 class="prof-team-name"><?= htmlspecialchars($intern['name']) ?></h3>
                        <span class="prof-team-role"><?= htmlspecialchars($intern['role']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div id="magangEmptyState" class="prof-empty-state" style="display: none;">
                        Belum ada anggota untuk kategori/batch yang dipilih. Anggota baru dapat ditambahkan melalui panel CMS.
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Include Footer -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <!-- Interactive Script for Magang Category Filters (Year & Batch) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentMagangYear = 'all';
        let currentMagangBatch = 'all';
        const magangYearBtns = document.querySelectorAll('[data-magang-year]');
        const magangBatchBtns = document.querySelectorAll('[data-magang-batch]');
        const magangCards = document.querySelectorAll('#magangGrid .prof-team-card');
        const emptyState = document.getElementById('magangEmptyState');

        function filterMagang() {
            let visibleCount = 0;
            magangCards.forEach(card => {
                const cardYear = card.getAttribute('data-year');
                const cardBatch = card.getAttribute('data-batch');

                const matchYear = (currentMagangYear === 'all' || cardYear === currentMagangYear);
                const matchBatch = (currentMagangBatch === 'all' || cardBatch === currentMagangBatch);

                if (matchYear && matchBatch) {
                    card.classList.remove('fade-hidden');
                    visibleCount++;
                } else {
                    card.classList.add('fade-hidden');
                }
            });

            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        magangYearBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                magangYearBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentMagangYear = this.getAttribute('data-magang-year');
                filterMagang();
            });
        });

        magangBatchBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                magangBatchBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentMagangBatch = this.getAttribute('data-magang-batch');
                filterMagang();
            });
        });
    });
    </script>
</body>
</html>
