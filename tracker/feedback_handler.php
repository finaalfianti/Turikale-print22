<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database terputus.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true) ?? $_POST;

$isiFeedback = trim($payload['isi_feedback'] ?? $payload['pesan'] ?? $payload['catatan'] ?? '');
if ($isiFeedback === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Catatan / saran masukan tidak boleh kosong.']);
    exit;
}

$kategori  = trim($payload['kategori'] ?? 'Saran Fitur');
$nama      = trim($payload['nama'] ?? 'Anonim');
if ($nama === '') {
    $nama = 'Anonim';
}
$kontak    = trim($payload['kontak'] ?? '-');
if ($kontak === '') {
    $kontak = '-';
}

$userAgent = $payload['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

$rawIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($rawIp, ',') !== false) {
    $parts = explode(',', $rawIp);
    $ip = trim($parts[0]);
} else {
    $ip = trim($rawIp);
}

try {
    $stmt = $pdo->prepare("INSERT INTO layouting_feedbacks (nama, kontak, kategori, isi_feedback, user_agent, ip_address, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");
    $stmt->execute([$nama, $kontak, $kategori, $isiFeedback, $userAgent, $ip]);

    echo json_encode([
        'success' => true,
        'message' => 'Terima kasih! Saran & masukan Anda berhasil tersimpan.'
    ]);
    exit;
} catch (Throwable $e) {
    if (str_contains($e->getMessage(), 'Unknown column') || $e->getCode() == '42S22') {
        try {
            $pdo->exec("ALTER TABLE layouting_feedbacks ADD COLUMN nama VARCHAR(100) DEFAULT 'Anonim' AFTER id");
        } catch (Throwable $t) {}
        try {
            $pdo->exec("ALTER TABLE layouting_feedbacks ADD COLUMN kontak VARCHAR(100) DEFAULT '-' AFTER nama");
        } catch (Throwable $t) {}
        try {
            $pdo->exec("ALTER TABLE layouting_feedbacks ADD COLUMN status ENUM('unread','read') DEFAULT 'unread'");
        } catch (Throwable $t) {}

        try {
            $stmt = $pdo->prepare("INSERT INTO layouting_feedbacks (nama, kontak, kategori, isi_feedback, user_agent, ip_address, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");
            $stmt->execute([$nama, $kontak, $kategori, $isiFeedback, $userAgent, $ip]);

            echo json_encode([
                'success' => true,
                'message' => 'Terima kasih! Saran & masukan Anda berhasil tersimpan.'
            ]);
            exit;
        } catch (Throwable $e2) {
            try {
                $combined = "[Pengirim: " . $nama . " | Kontak: " . $kontak . "]\n\n" . $isiFeedback;
                $stmt = $pdo->prepare("INSERT INTO layouting_feedbacks (kategori, isi_feedback, user_agent, ip_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$kategori, $combined, $userAgent, $ip]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Terima kasih! Saran & masukan Anda berhasil tersimpan.'
                ]);
                exit;
            } catch (Throwable $e3) {}
        }
    }

    if (str_contains($e->getMessage(), "Table") && str_contains($e->getMessage(), "doesn't exist")) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS layouting_feedbacks (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(100) DEFAULT 'Anonim',
                kontak VARCHAR(100) DEFAULT '-',
                kategori VARCHAR(50) NOT NULL,
                isi_feedback TEXT NOT NULL,
                user_agent TEXT DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                status ENUM('unread','read') DEFAULT 'unread',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $stmt = $pdo->prepare("INSERT INTO layouting_feedbacks (nama, kontak, kategori, isi_feedback, user_agent, ip_address, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");
            $stmt->execute([$nama, $kontak, $kategori, $isiFeedback, $userAgent, $ip]);

            echo json_encode([
                'success' => true,
                'message' => 'Terima kasih! Saran & masukan Anda berhasil tersimpan.'
            ]);
            exit;
        } catch (Throwable $e4) {}
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan masukan ke database: ' . $e->getMessage()
    ]);
}
