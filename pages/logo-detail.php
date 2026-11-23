<?php
$currentPage = 'aset-logo';
$basePath    = '../';

require_once __DIR__ . '/../config/database.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    http_response_code(404);
    exit('Logo tidak ditemukan.');
}

$logo = null;
$related = [];

if (isset($conn) && $conn instanceof mysqli) {
    try {
        $stmt = $conn->prepare("
            SELECT tp_logos.*, tp_provinces.name AS province_name, tp_cities.name AS city_name, tp_categories.name AS category
            FROM tp_logos
            LEFT JOIN tp_provinces ON tp_logos.province_id = tp_provinces.id
            LEFT JOIN tp_cities    ON tp_logos.city_id     = tp_cities.id
            LEFT JOIN tp_categories ON tp_logos.category_id = tp_categories.id
            WHERE tp_logos.slug = ?
            LIMIT 1
        ");
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $logo = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($logo) {
            $stmtRelated = $conn->prepare("
                SELECT tp_logos.id, tp_logos.slug, tp_logos.title, tp_logos.original_filename, tp_logos.converted_filename, tp_categories.name AS category
                FROM tp_logos
                LEFT JOIN tp_categories ON tp_logos.category_id = tp_categories.id
                WHERE tp_logos.category_id = ? AND tp_logos.id != ?
                ORDER BY RAND()
                LIMIT 6
            ");
            $stmtRelated->bind_param('ii', $logo['category_id'], $logo['id']);
            $stmtRelated->execute();
            $related = $stmtRelated->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmtRelated->close();
        }
    } catch (Throwable $e) {}
}

if (!$logo) {
    $candidate = __DIR__ . '/../uploads/logos/' . basename($slug);
    if (!file_exists($candidate) && file_exists($candidate . '.png')) {
        $candidate = $candidate . '.png';
    }
    if (file_exists($candidate)) {
        $fn = basename($candidate);
        $cleanTitle = preg_replace('/^logo-(logo-)?/i', '', pathinfo($fn, PATHINFO_FILENAME));
        $cleanTitle = preg_replace('/-[a-f0-9]{6}$/i', '', $cleanTitle);
        $cleanTitle = ucwords(str_replace('-', ' ', $cleanTitle));
        $logo = [
            'id' => 1,
            'title' => $cleanTitle,
            'slug' => $fn,
            'original_filename' => $fn,
            'converted_filename' => $fn,
            'category' => 'Pemerintahan',
            'province_name' => 'Sulawesi Selatan',
            'city_name' => 'Maros',
        ];
    }
}

if (!$logo) {
    http_response_code(404);
    exit('Logo tidak ditemukan.');
}
$stmtRelated->close();

$currentPage   = 'aset-logo';
$basePath      = '../';
$canonicalUrl  = 'https://turikaleprint.space/pages/logo-detail.php?slug=' . htmlspecialchars($logo['slug']);
$fileName      = !empty($logo['converted_filename']) ? $logo['converted_filename'] : $logo['original_filename'];
$logoImageUrl  = $basePath . 'uploads/logos/' . rawurlencode($fileName);

function categoryBadgeClass(string $cat): string {
    return match($cat) {
        'Sekolah'          => 'from-orange-500 to-yellow-400',
        'Pemerintahan'     => 'from-red-600 to-amber-500',
        'Perguruan Tinggi' => 'from-green-600 to-teal-500',
        'Organisasi'       => 'from-blue-600 to-cyan-500',
        default            => 'from-emerald-600 to-lime-500',
    };
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Download Logo <?= htmlspecialchars($logo['title']) ?> – PNG HD | Turikale Print</title>
    <meta name="description" content="Download logo <?= htmlspecialchars($logo['title']) ?> format PNG kualitas tinggi. Kategori: <?= htmlspecialchars($logo['category']) ?>. Gratis dan bebas digunakan untuk kebutuhan desain." />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Download Logo <?= htmlspecialchars($logo['title']) ?> | Turikale Print" />
    <meta property="og:description" content="Download logo <?= htmlspecialchars($logo['title']) ?> PNG kualitas tinggi gratis di Turikale Print." />
    <meta property="og:image" content="https://turikaleprint.space/uploads/logos/<?= rawurlencode($fileName) ?>" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:site_name" content="Turikale Print" />
    <link rel="icon" type="image/jpeg" href="../assets/images/Logo-Icon-TurikalePrint.jpg" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ImageObject",
        "name": "<?= addslashes(htmlspecialchars($logo['title'])) ?>",
        "description": "Logo <?= addslashes(htmlspecialchars($logo['title'])) ?> PNG kualitas tinggi",
        "contentUrl": "https://turikaleprint.space/uploads/logos/<?= rawurlencode($fileName) ?>",
        "url": "<?= $canonicalUrl ?>",
        "encodingFormat": "image/png",
        "license": "https://creativecommons.org/publicdomain/zero/1.0/"
    }
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
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
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

    <div class="relative flex min-h-screen flex-col overflow-x-hidden">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <main id="main-content" class="flex-1">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

                <nav class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 text-sm mb-8 flex-wrap" aria-label="Breadcrumb">
                    <a class="hover:text-primary transition-colors" href="../index.php">Beranda</a>
                    <span class="material-symbols-outlined text-xs" aria-hidden="true">chevron_right</span>
                    <a class="hover:text-primary transition-colors" href="aset-logo.php">Aset Logo</a>
                    <span class="material-symbols-outlined text-xs" aria-hidden="true">chevron_right</span>
                    <span class="text-slate-900 dark:text-white font-medium truncate max-w-[200px]"><?= htmlspecialchars($logo['title']) ?></span>
                </nav>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 mb-12">

                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 sm:p-12 flex items-center justify-center border border-slate-200 dark:border-slate-800 shadow-sm aspect-square">
                        <img
                            src="<?= $logoImageUrl ?>"
                            alt="Logo <?= htmlspecialchars($logo['title']) ?>"
                            class="max-w-full max-h-full object-contain mix-blend-multiply dark:mix-blend-normal"
                            fetchpriority="high"
                        />
                    </div>

                    <div class="flex flex-col justify-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r <?= categoryBadgeClass($logo['category']) ?> mb-4 w-fit uppercase tracking-wider">
                            <?= htmlspecialchars($logo['category']) ?>
                        </span>

                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mb-4 leading-tight">
                            <?= htmlspecialchars($logo['title']) ?>
                        </h1>

                        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-400 mb-8">
                            <?php if (!empty($logo['province_name'])): ?>
                            <div class="flex gap-3">
                                <dt class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-300 min-w-[120px]">
                                    <span class="material-symbols-outlined text-base text-primary" aria-hidden="true">map</span>
                                    Provinsi
                                </dt>
                                <dd><?= htmlspecialchars($logo['province_name']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($logo['city_name'])): ?>
                            <div class="flex gap-3">
                                <dt class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-300 min-w-[120px]">
                                    <span class="material-symbols-outlined text-base text-primary" aria-hidden="true">location_city</span>
                                    Kabupaten/Kota
                                </dt>
                                <dd><?= htmlspecialchars($logo['city_name']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <div class="flex gap-3">
                                <dt class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-300 min-w-[120px]">
                                    <span class="material-symbols-outlined text-base text-primary" aria-hidden="true">image</span>
                                    Format
                                </dt>
                                <dd><?= strtoupper(pathinfo($logo['original_filename'], PATHINFO_EXTENSION)) ?></dd>
                            </div>
                        </dl>

                        <a
                            href="<?= $logoImageUrl ?>"
                            download="<?= htmlspecialchars($logo['title']) . '.' . strtolower(pathinfo($logo['original_filename'], PATHINFO_EXTENSION)) ?>"
                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-primary text-white font-bold text-base px-8 py-4 rounded-xl hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20"
                            aria-label="Unduh logo <?= htmlspecialchars($logo['title']) ?> format <?= strtoupper(pathinfo($logo['original_filename'], PATHINFO_EXTENSION)) ?>"
                        >
                            <span class="material-symbols-outlined" aria-hidden="true">download</span>
                            Unduh Logo
                        </a>

                        <div class="mt-4 flex gap-2">
                            <a
                                href="https://wa.me/?text=<?= urlencode('Download Logo ' . $logo['title'] . ' - ' . $canonicalUrl) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-[#25D366] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                                aria-label="Bagikan ke WhatsApp"
                            >
                                Bagikan
                            </a>
                            <button
                                onclick="navigator.clipboard.writeText('<?= $canonicalUrl ?>').then(() => {this.textContent='Tersalin!'; setTimeout(() => this.textContent='Salin Link', 2000)})"
                                class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                aria-label="Salin tautan halaman ini"
                            >
                                Salin Link
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (!empty($related)): ?>
                <section aria-labelledby="related-logos-heading">
                    <h2 id="related-logos-heading" class="text-2xl font-bold mb-6">
                        Logo Terkait — <?= htmlspecialchars($logo['category']) ?>
                    </h2>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3" role="list">
                        <?php foreach ($related as $rel):
                            $relFile = !empty($rel['converted_filename']) ? $rel['converted_filename'] : $rel['original_filename'];
                        ?>
                        <a
                            href="logo-detail.php?slug=<?= htmlspecialchars($rel['slug']) ?>"
                            class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-3 flex flex-col items-center text-center hover:shadow-md transition-shadow"
                            role="listitem"
                        >
                            <div class="w-full aspect-square flex items-center justify-center bg-slate-50 dark:bg-slate-800 rounded-lg mb-2">
                                <img
                                    src="<?= $basePath ?>uploads/logos/<?= htmlspecialchars($relFile) ?>"
                                    alt="Logo <?= htmlspecialchars($rel['title']) ?>"
                                    class="max-w-[80%] max-h-[80%] object-contain mix-blend-multiply dark:mix-blend-normal group-hover:scale-105 transition-transform duration-200"
                                    loading="lazy"
                                />
                            </div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($rel['title']) ?>
                            </p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

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
