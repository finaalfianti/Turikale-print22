<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($pdo) || !$pdo) {
    die("Koneksi database PDO gagal diinisialisasi. Periksa config/database.php.");
}

function ensure_schema_updates(PDO $db): void {
    static $executed = false;
    if ($executed) {
        return;
    }
    $executed = true;
    try {
        $cols = $db->query("SHOW COLUMNS FROM tp_hero_slides LIKE 'focal_point'")->fetchAll();
        if (empty($cols)) {
            $db->exec("ALTER TABLE tp_hero_slides ADD COLUMN focal_point VARCHAR(50) NOT NULL DEFAULT 'object-center', ADD COLUMN object_fit VARCHAR(50) NOT NULL DEFAULT 'object-cover'");
        }
    } catch (Throwable $e) {}
    try {
        $colsMob = $db->query("SHOW COLUMNS FROM tp_hero_slides LIKE 'image_mobile_url'")->fetchAll();
        if (empty($colsMob)) {
            $db->exec("ALTER TABLE tp_hero_slides ADD COLUMN image_mobile_url VARCHAR(500) DEFAULT NULL");
        }
    } catch (Throwable $e) {}
    try {
        $colsMem = $db->query("SHOW COLUMNS FROM tp_profile_members LIKE 'photo_position'")->fetchAll();
        if (empty($colsMem)) {
            $db->exec("ALTER TABLE tp_profile_members ADD COLUMN photo_position VARCHAR(50) NOT NULL DEFAULT 'object-center', ADD COLUMN frame_shape VARCHAR(50) NOT NULL DEFAULT 'rounded-full'");
        }
    } catch (Throwable $e) {}
}

function db(): PDO {
    global $pdo;
    ensure_schema_updates($pdo);
    return $pdo;
}

function get_setting_val(string $key, string $default = ''): string {
    return get_setting($key, $default) ?? $default;
}

function set_setting_val(string $key, ?string $value): bool {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO tp_sys_settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
        return $stmt->execute([$key, $value]);
    } catch (Throwable $e) {
        return false;
    }
}

function cms_asset_url(?string $path): string {
    if (empty($path)) {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
        return $path;
    }

    $clean = ltrim(preg_replace('#^\.\./#', '', $path), '/');
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if (str_contains($host, 'opu-akses') || preg_match('/^[a-z0-9_-]+\.turikaleprint\.[a-z]+/i', $host)) {
        return 'https://turikaleprint.space/' . $clean;
    }

    if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/nam1tp/')) {
        return '../' . $clean;
    }

    if (!empty($host) && str_contains($host, 'turikaleprint.space')) {
        return 'https://turikaleprint.space/' . $clean;
    }

    return '../' . $clean;
}

function upload_file(array $fileInput, string $targetDir = 'uploads/', array $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg']): ?string {
    if (!isset($fileInput['name']) || empty($fileInput['name']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return null;
    }

    $rootDir = dirname(__DIR__, 2);
    $fullTargetDir = rtrim($rootDir, '/\\') . '/' . trim($targetDir, '/\\') . '/';
    if (!is_dir($fullTargetDir)) {
        @mkdir($fullTargetDir, 0755, true);
    }

    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $destination = $fullTargetDir . $filename;

    if (move_uploaded_file($fileInput['tmp_name'], $destination)) {
        return trim($targetDir, '/\\') . '/' . $filename;
    }

    return null;
}

function create_slug(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $slug = strtolower($text);
    if (empty($slug)) {
        $slug = 'item-' . time();
    }
    return $slug;
}
