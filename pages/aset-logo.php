<?php
$currentPage = 'aset-logo';
$basePath    = '../';

require_once __DIR__ . '/../config/database.php';

$limit      = 30;
$page       = max(1, (int) ($_GET['page'] ?? 1));
$search     = trim($_GET['search'] ?? '');
$provinceId = isset($_GET['province']) ? (int) $_GET['province'] : 0;
$cityId     = isset($_GET['city'])     ? (int) $_GET['city']     : 0;
$start      = ($page - 1) * $limit;

$allowedCategories = [];
$categories        = [];
$logoRows          = [];
$shownLogos        = 0;
$totalLogo         = 0;
$totalPages        = 0;
$provinces         = null;

if (isset($conn) && $conn instanceof mysqli) {
    try {
        $catRes = $conn->query("SELECT id, name, slug FROM tp_categories WHERE status = 1 ORDER BY sort_order ASC, name ASC");
        if ($catRes) {
            while ($cat = $catRes->fetch_assoc()) {
                $allowedCategories[(int)$cat['id']] = $cat['name'];
            }
        }

        $rawCats = isset($_GET['category']) && is_array($_GET['category']) ? $_GET['category'] : [];
        $categories = [];
        foreach ($rawCats as $cVal) {
            $cInt = (int)$cVal;
            if ($cInt > 0 && isset($allowedCategories[$cInt])) {
                $categories[] = $cInt;
            }
        }

        $whereArr  = ['l.status = 1', 'l.is_deleted = 0'];
        $bindTypes = '';
        $bindVals  = [];

        if ($search !== '') {
            $whereArr[]  = 'l.title LIKE ?';
            $bindTypes  .= 's';
            $bindVals[]  = '%' . $search . '%';
        }

        if (!empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $whereArr[]   = "l.category_id IN ($placeholders)";
            $bindTypes   .= str_repeat('i', count($categories));
            foreach ($categories as $catId) {
                $bindVals[] = $catId;
            }
        }

        if ($provinceId > 0) {
            $whereArr[]  = 'l.province_id = ?';
            $bindTypes  .= 'i';
            $bindVals[]  = $provinceId;
        }

        if ($cityId > 0) {
            $whereArr[]  = 'l.city_id = ?';
            $bindTypes  .= 'i';
            $bindVals[]  = $cityId;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $whereArr);

        $stmt = $conn->prepare("
            SELECT l.*, c.name AS category_name, c.slug AS category_slug
            FROM tp_logos l
            LEFT JOIN tp_categories c ON l.category_id = c.id
            $whereSQL
            ORDER BY l.id DESC LIMIT ?, ?
        ");

        $paginatedTypes = $bindTypes . 'ii';
        $paginatedVals  = array_merge($bindVals, [$start, $limit]);

        if (!empty($paginatedVals)) {
            $stmt->bind_param($paginatedTypes, ...$paginatedVals);
        }
        $stmt->execute();
        $result     = $stmt->get_result();
        $shownLogos = $result ? $result->num_rows : 0;
        $logoRows   = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $stmtCount = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM tp_logos l
            $whereSQL
        ");

        if (!empty($bindVals)) {
            $stmtCount->bind_param($bindTypes, ...$bindVals);
        }

        $stmtCount->execute();
        $totalLogo  = (int) ($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
        $stmtCount->close();
        $totalPages = (int) ceil($totalLogo / $limit);

        $provinces = $conn->query('SELECT id, name FROM tp_provinces ORDER BY name ASC');
    } catch (Throwable $e) {}
}

// Fallback: jika database belum terisi atau offline, ambil file dari folder uploads/logos
if (empty($logoRows)) {
    $files = glob(__DIR__ . '/../uploads/logos/*.png');
    if ($files) {
        $filteredFiles = [];
        foreach ($files as $f) {
            $fn = basename($f);
            $cleanTitle = preg_replace('/^logo-(logo-)?/i', '', pathinfo($fn, PATHINFO_FILENAME));
            $cleanTitle = preg_replace('/-[a-f0-9]{6}$/i', '', $cleanTitle);
            $cleanTitle = ucwords(str_replace('-', ' ', $cleanTitle));

            if ($search !== '' && stripos($cleanTitle, $search) === false) {
                continue;
            }

            $filteredFiles[] = [
                'id'                 => count($filteredFiles) + 1,
                'title'              => $cleanTitle,
                'converted_filename' => $fn,
                'category_name'      => 'Pemerintahan',
                'slug'               => $fn
            ];
        }

        $totalLogo  = count($filteredFiles);
        $totalPages = (int) ceil($totalLogo / $limit);
        $logoRows   = array_slice($filteredFiles, $start, $limit);
        $shownLogos = count($logoRows);
    }
}

$currentPage     = 'aset-logo';
$basePath        = '../';
$canonicalUrl    = 'https://turikaleprint.space/pages/aset-logo.php';

if ($search !== '') {
    $pageTitle       = 'Cari Logo "' . htmlspecialchars($search) . '" – Download PNG HD | Turikale Print';
    $pageDescription = 'Hasil pencarian logo untuk "' . htmlspecialchars($search) . '". Download logo format transparan HD gratis kualitas tinggi di Turikale Print.';
} elseif (!empty($categories)) {
    $catNames = [];
    foreach ($categories as $cId) {
        if (isset($allowedCategories[$cId])) $catNames[] = $allowedCategories[$cId];
    }
    $catStr = implode(', ', $catNames);
    $pageTitle       = 'Koleksi Logo ' . htmlspecialchars($catStr) . ' – Download PNG | Turikale Print';
    $pageDescription = 'Download koleksi logo ' . htmlspecialchars($catStr) . ' format PNG transparan HD gratis tanpa background di Turikale Print.';
} else {
    $pageTitle       = 'Aset Logo Terlengkap – kumpulan Logo Institusi, Sekolah & Pemerintahan PNG | Turikale Print';
    $pageDescription = 'Download kumpulan aset logo institusi, sekolah (SD/SMP/SMA), perguruan tinggi, dan pemerintahan dalam format PNG kualitas tinggi. Cari berdasarkan provinsi dan kota.';
}

function buildPageUrl(int $p): string {
    $params = $_GET;
    $params['page'] = $p;
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return 'aset-logo.php?' . http_build_query($params, '', '&amp;', PHP_QUERY_RFC3986);
}

function categoryBadgeClass(string $cat): string {
    return match($cat) {
        'Sekolah'          => 'bg-gradient-to-r from-orange-500 to-yellow-400 text-white',
        'Pemerintahan'     => 'bg-gradient-to-r from-red-600 to-amber-500 text-white',
        'Perguruan Tinggi' => 'bg-gradient-to-r from-green-600 to-teal-500 text-white',
        'Organisasi'       => 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white',
        default            => 'bg-gradient-to-r from-emerald-600 to-lime-500 text-white',
    };
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta name="keywords" content="download logo png, logo transparan, logo sekolah, logo instansi, logo kabupaten, logo daerah, turikale print" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta property="og:image" content="https://turikaleprint.space/assets/images/Logo Biru-TurikalePrint.png" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:site_name" content="Turikale Print" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>" />
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta name="twitter:image" content="https://turikaleprint.space/assets/images/Logo Biru-TurikalePrint.png" />
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
            <?php
            $itemList = [];
            $pos = 1;
            foreach ($logoRows as $lRow) {
                $itemList[] = json_encode([
                    "@type" => "ListItem",
                    "position" => $pos++,
                    "url" => "https://turikaleprint.space/pages/logo-detail.php?slug=" . urlencode($lRow['slug']),
                    "name" => "Logo " . $lRow['title']
                ], JSON_UNESCAPED_UNICODE);
            }
            echo implode(",\n", $itemList);
            ?>
        ]
    }
    </script>
    <link rel="icon" type="image/jpeg" href="../assets/images/Logo-Icon-TurikalePrint.jpg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    screens: {
                        '3xl': '1920px',
                    },
                    colors: {
                        primary: '#136dec',
                        secondary: '#10b981',
                        'background-light': '#f6f7f8',
                        'background-dark': '#101822',
                    },
                    fontFamily: { display: ['Inter', 'system-ui', 'sans-serif'] },
                    borderRadius: { DEFAULT: '0.25rem', lg: '0.5rem', xl: '0.75rem', full: '9999px' },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script>
        const SELECTED_CITY = "<?= (int) ($cityId) ?>";
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

    <div class="relative flex min-h-screen flex-col overflow-x-hidden">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <main id="main-content">

            <section class="mb-10">
                <div class="relative overflow-hidden rounded-none bg-slate-900 px-6 py-14 text-center sm:px-12 sm:py-20 2xl:py-24"
                     style="background-image: linear-gradient(to bottom right, rgba(19, 109, 236, 0.85), rgba(16, 185, 129, 0.65)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuD-sialny-DA-T_gszQKGaL94SRU6hLEwYNttmV7KrkdWq2zgLALcrrzQseutxaTFhPfDucZWwBHGTovCANRDm-wx4pkD-IT2CxgO7zfsuOYhcE0YC4btaDkNZn6hzgEJepu62gWsZNDwVCf2IzYA5erVx1qs-9TZqKENnEqK-h0Ug0oPU83AxiBHMDyUROolcSMVEWSx7umZwR0yjXCAeJ2ekvScXs9V0RztFi7_5IbQWEqpAEkwDRJOLsXUjjew015UcyANkFJVzq'); background-size: cover; background-position: center;">
                    <div class="relative z-10 max-w-2xl 2xl:max-w-3xl mx-auto">
                        <h1 class="text-3xl font-black tracking-tight text-white sm:text-5xl 2xl:text-6xl mb-4">
                            Aset Logo
                        </h1>
                        <p class="text-lg 2xl:text-xl text-slate-100 mb-8">
                            Kumpulan aset logo institusi, sekolah, dan organisasi untuk kebutuhan desainmu.
                        </p>

                        <form method="GET" role="search" aria-label="Cari logo" class="relative max-w-xl 2xl:max-w-2xl mx-auto">
                            <?php foreach ($categories as $cat): ?>
                            <input type="hidden" name="category[]" value="<?= htmlspecialchars($cat) ?>" />
                            <?php endforeach; ?>
                            <?php if ($provinceId): ?><input type="hidden" name="province" value="<?= $provinceId ?>" /><?php endif; ?>
                            <?php if ($cityId):     ?><input type="hidden" name="city"     value="<?= $cityId ?>" /><?php endif; ?>

                            <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl shadow-lg p-1.5 sm:p-2">
                                <label for="hero-search" class="sr-only">Cari logo institusi, sekolah, atau organisasi</label>
                                <span class="material-symbols-outlined text-slate-400 px-3" aria-hidden="true">search</span>
                                <input
                                    id="hero-search"
                                    class="w-full border-none focus:ring-0 text-slate-900 dark:text-white bg-transparent text-sm sm:text-base"
                                    placeholder="Cari logo institusi, sekolah, atau organisasi..."
                                    type="search"
                                    name="search"
                                    value="<?= htmlspecialchars($search) ?>"
                                />
                                <button
                                    type="submit"
                                    class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg font-bold hover:bg-primary/90 transition-colors text-sm sm:text-base"
                                >
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pb-16">
                <div class="flex flex-col lg:flex-row gap-8 2xl:gap-10">

                    <div class="lg:hidden mb-0">
                        <button
                            id="filter-toggle"
                            class="flex items-center gap-2 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            aria-expanded="false"
                            aria-controls="filter-panel"
                        >
                            <span class="material-symbols-outlined text-primary" aria-hidden="true">filter_list</span>
                            <span>Filter Pencarian</span>
                            <span id="filter-arrow" class="material-symbols-outlined ml-auto transition-transform duration-300" aria-hidden="true">expand_more</span>
                        </button>
                    </div>

                    <aside class="w-full lg:w-64 2xl:w-72 shrink-0" aria-label="Filter logo">
                        <div
                            id="filter-panel"
                            class="hidden lg:block bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 lg:sticky lg:top-24"
                        >
                            <div class="hidden lg:flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-700 pb-3">
                                <span class="material-symbols-outlined text-primary" aria-hidden="true">filter_list</span>
                                <h2 class="font-bold text-slate-900 dark:text-white">Filter Pencarian</h2>
                            </div>

                            <form id="filterForm" method="GET">
                                <?php if ($search !== ''): ?>
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>" />
                                <?php endif; ?>

                                <div class="space-y-6">
                                    <div>
                                        <label for="province" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Provinsi</label>
                                        <select
                                            id="province"
                                            name="province"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-primary focus:border-primary text-sm"
                                        >
                                            <option value="">Pilih Provinsi</option>
                                            <?php if ($provinces) while ($p = $provinces->fetch_assoc()): ?>
                                            <option
                                                value="<?= (int) $p['id'] ?>"
                                                <?= $provinceId === (int) $p['id'] ? 'selected' : '' ?>
                                            ><?= htmlspecialchars($p['name']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kabupaten/Kota</label>
                                        <select
                                            id="city"
                                            name="city"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-primary focus:border-primary text-sm"
                                        >
                                            <option value="">Pilih Kabupaten</option>
                                        </select>
                                    </div>

                                    <fieldset>
                                        <legend class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3 flex items-center justify-between">
                                            <span>Kategori Khusus</span>
                                            <span class="text-[11px] text-slate-400 font-normal">Pilih Kategori</span>
                                        </legend>
                                        <div class="space-y-2">
                                            <?php
                                            $categoryIcons = [
                                                'Sekolah'                => 'school',
                                                'Perguruan Tinggi'       => 'local_library',
                                                'Instansi Pemerintah'    => 'account_balance',
                                                'Organisasi & Komunitas' => 'groups',
                                                'Perusahaan & Usaha'     => 'business',
                                            ];
                                            foreach ($allowedCategories as $val => $label):
                                                $icon = $categoryIcons[$label] ?? 'category';
                                                $isChecked = in_array($val, $categories);
                                            ?>
                                            <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-primary/50 transition-all cursor-pointer group">
                                                <input
                                                    class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300 dark:border-slate-600 dark:bg-slate-800"
                                                    type="checkbox"
                                                    name="category[]"
                                                    value="<?= htmlspecialchars($val) ?>"
                                                    <?= $isChecked ? 'checked' : '' ?>
                                                />
                                                <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-primary transition-colors"><?= $icon ?></span>
                                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors flex-1"><?= htmlspecialchars($label) ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </fieldset>

                                    <button
                                        type="submit"
                                        class="w-full py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors"
                                    >
                                        Terapkan Filter
                                    </button>

                                    <?php if ($search !== '' || !empty($categories) || $provinceId || $cityId): ?>
                                    <a
                                        href="aset-logo.php"
                                        class="block w-full py-2 text-center text-sm text-slate-500 hover:text-primary transition-colors"
                                    >
                                        Hapus semua filter
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </aside>

                    <div class="flex-1" id="main-content-inner">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-slate-600 dark:text-slate-400 text-sm">
                                Menampilkan
                                <strong class="text-slate-900 dark:text-white"><?= number_format($shownLogos) ?></strong>
                                dari <strong class="text-slate-900 dark:text-white"><?= number_format($totalLogo) ?></strong> aset logo
                            </p>
                        </div>

                        <?php if (empty($logoRows)): ?>
                        <div class="text-center py-20 col-span-full">
                            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block" aria-hidden="true">image_search</span>
                            <p class="text-slate-500 text-lg font-semibold">Logo tidak ditemukan</p>
                            <p class="text-slate-400 text-sm mt-2">Coba kata kunci atau filter yang berbeda.</p>
                            <a href="aset-logo.php" class="inline-block mt-4 px-5 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors">Reset filter</a>
                        </div>
                        <?php else: ?>

                        <div id="logoGrid" class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 3xl:grid-cols-8 gap-2 sm:gap-3 2xl:gap-4" role="list">
                            <?php foreach ($logoRows as $row): ?>
                            <article
                                class="group flex flex-col h-full bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 hover:shadow-xl transition-all duration-200"
                                role="listitem"
                            >
                                <div class="aspect-square w-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-3 sm:p-4 overflow-hidden">
                                    <img
                                        src="<?= $basePath ?>uploads/logos/<?= htmlspecialchars($row['converted_filename']) ?>"
                                        class="max-w-full max-h-full object-contain mix-blend-multiply dark:mix-blend-normal group-hover:scale-105 transition-transform duration-300"
                                        alt="<?= htmlspecialchars($row['title']) ?>"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                </div>
                                <div class="p-2 sm:p-3 flex flex-col flex-1 gap-2">
                                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-tight line-clamp-2">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </h3>
                                    <span class="w-fit text-[9px] sm:text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider <?= categoryBadgeClass($row['category_name'] ?? 'Lainnya') ?>">
                                        <?= htmlspecialchars($row['category_name'] ?? 'Lainnya') ?>
                                    </span>
                                    <div class="mt-auto pt-1">
                                        <a
                                            href="<?= $basePath ?>uploads/logos/<?= htmlspecialchars($row['converted_filename']) ?>"
                                            class="w-full inline-block"
                                            download="<?= htmlspecialchars($row['title']) . '.' . strtolower(pathinfo($row['converted_filename'], PATHINFO_EXTENSION)) ?>"
                                            aria-label="Unduh logo <?= htmlspecialchars($row['title']) ?>"
                                        >
                                            <button class="w-full bg-primary text-white text-xs font-bold py-1.5 sm:py-2 rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                                                Unduh
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($totalPages > 1): ?>
                        <nav class="mt-10 flex justify-center gap-1.5" aria-label="Navigasi halaman logo">
                            <?php if ($page > 1): ?>
                            <a href="<?= buildPageUrl($page - 1) ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm hover:border-primary hover:text-primary transition-colors" aria-label="Halaman sebelumnya">‹</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a
                                href="<?= buildPageUrl($i) ?>"
                                class="px-3 py-1.5 rounded-lg border text-sm transition-colors <?= $i === $page ? 'pagination-active' : 'bg-white dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700 hover:border-primary hover:text-primary' ?>"
                                <?= $i === $page ? 'aria-current="page"' : '' ?>
                                aria-label="Halaman <?= $i ?>"
                            ><?= $i ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                            <a href="<?= buildPageUrl($page + 1) ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm hover:border-primary hover:text-primary transition-colors" aria-label="Halaman berikutnya">›</a>
                            <?php endif; ?>
                        </nav>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>

    <script src="../assets/js/main.js" defer></script>
    <script>
        (function(){
            try {
                var d = {
                    page_url: window.location.href,
                    referrer: document.referrer || '',
                    screen_width: window.screen ? window.screen.width : 0
                };
                if (navigator.sendBeacon) {
                    var blob = new Blob([JSON.stringify(d)], {type: 'application/json'});
                    navigator.sendBeacon('/api/track.php', blob);
                } else {
                    fetch('/api/track.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(d),
                        keepalive: true
                    }).catch(function(){});
                }
            } catch(e){}
        })();
    </script>
</body>
</html>
