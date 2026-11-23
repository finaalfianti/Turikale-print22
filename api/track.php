<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

if (!$pdo) {
    echo json_encode(['success' => false, 'error' => 'Database not connected']);
    exit;
}

$rawIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($rawIp, ',') !== false) {
    $parts = explode(',', $rawIp);
    $ip = trim($parts[0]);
} else {
    $ip = trim($rawIp);
}

$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$pageUrl = $_GET['p'] ?? $_POST['p'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
if (strlen($pageUrl) > 255) {
    $pageUrl = substr($pageUrl, 0, 255);
}

$device = 'Desktop';
$uaLower = strtolower($ua);
if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $uaLower)) {
    $device = 'Tablet';
} elseif (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $uaLower)) {
    $device = 'Mobile';
}

$country = 'Indonesia';
$city = 'Maros';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$cacheKey = 'tp_geo_' . md5($ip);
if (!empty($_SESSION[$cacheKey])) {
    $country = $_SESSION[$cacheKey]['country'] ?? $country;
    $city = $_SESSION[$cacheKey]['city'] ?? $city;
} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 1.5,
            'ignore_errors' => true,
            'header' => "User-Agent: Mozilla/5.0\r\n"
        ]
    ]);
    $apiRes = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city", false, $ctx);
    if ($apiRes) {
        $data = json_decode($apiRes, true);
        if (!empty($data) && ($data['status'] ?? '') === 'success') {
            $country = $data['country'] ?? 'Indonesia';
            $city = $data['city'] ?? 'Unknown';
            $_SESSION[$cacheKey] = ['country' => $country, 'city' => $city];
        }
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO visitor_logs (ip_address, user_agent, device, country, city, page_url, visited_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$ip, $ua, $device, $country, $city, $pageUrl]);
    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
