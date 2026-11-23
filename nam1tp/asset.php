<?php
$rawPath = $_GET['p'] ?? '';
$path = ltrim(str_replace(['../', '..\\'], '', $rawPath), '/\\');

if ($path === '') {
    http_response_code(400);
    exit('Path missing');
}

if (!str_starts_with($path, 'uploads/') && !str_starts_with($path, 'assets/')) {
    http_response_code(403);
    exit('Forbidden');
}

$rootDir = dirname(__DIR__);
$filePath = $rootDir . '/' . $path;

if (file_exists($filePath) && is_file($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
    ];
    $contentType = $mimes[$ext] ?? (mime_content_type($filePath) ?: 'application/octet-stream');
    header('Content-Type: ' . $contentType);
    header('Cache-Control: public, max-age=604800');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

http_response_code(404);
exit('File not found');
