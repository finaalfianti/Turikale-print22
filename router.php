<?php
/**
 * Router script untuk PHP Built-in Web Server
 * Jalankan: php -S localhost:8000 router.php
 *
 * Mengemulasikan URL Rewrite dari .htaccess Apache.
 * Kunci: pakai chdir() ke folder pages/ sebelum require
 * agar semua path relatif (../config, ../includes) tetap bekerja.
 */

$root = __DIR__;
$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$filePath = $root . $uri;

// ----------------------------------------------------------------
// 1. File statis / fisik (CSS, JS, gambar, dll) → layani langsung
// ----------------------------------------------------------------
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// ----------------------------------------------------------------
// 2. Helper: require file di folder pages/ dengan working-dir yang benar
// ----------------------------------------------------------------
function servePageFile(string $pageFile): void {
    // Pindahkan cwd ke folder pages/ agar ../config dan ../includes valid
    chdir(dirname($pageFile));
    require $pageFile;
}

// ----------------------------------------------------------------
// 3. Routing
// ----------------------------------------------------------------

// Beranda
if ($uri === '/' || $uri === '/index.php' || $uri === '/beranda') {
    chdir($root);
    require $root . '/index.php';
    return;
}

// Halaman utama (clean URL)
$routeMap = [
    '/profil'    => $root . '/pages/profil.php',
    '/profile'   => $root . '/pages/profil.php',
    '/aset-logo' => $root . '/pages/aset-logo.php',
    '/layouting' => $root . '/pages/layouting.php',
    '/desain'    => $root . '/pages/desain.php',
];

if (isset($routeMap[$uri])) {
    servePageFile($routeMap[$uri]);
    return;
}

// Detail Logo: /logo/{slug}
if (preg_match('#^/logo/([^/]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    servePageFile($root . '/pages/logo-detail.php');
    return;
}

// Fallback: coba file PHP di folder pages/ tanpa ekstensi
$pagesFile = $root . '/pages' . $uri . '.php';
if (file_exists($pagesFile)) {
    servePageFile($pagesFile);
    return;
}

// Fallback: coba file PHP di root tanpa ekstensi
$rootFile = $root . $uri . '.php';
if (file_exists($rootFile)) {
    chdir($root);
    require $rootFile;
    return;
}

// 404
http_response_code(404);
echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>";
echo "<h1>404 – Halaman tidak ditemukan</h1>";
echo "<p><a href='/'>← Kembali ke Beranda</a></p>";
echo "</body></html>";
