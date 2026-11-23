<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$province_id = isset($_GET['province_id']) ? (int)$_GET['province_id'] : 0;

if ($province_id <= 0) {
    echo json_encode([]);
    exit;
}

$cities = [];

if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, city_code FROM tp_cities WHERE province_id = ? ORDER BY name ASC");
        $stmt->execute([$province_id]);
        while ($row = $stmt->fetch()) {
            $cities[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'city_code' => $row['city_code']
            ];
        }
    } catch (Throwable $e) {}
} elseif ($conn instanceof mysqli) {
    try {
        $stmt = $conn->prepare("SELECT id, name, city_code FROM tp_cities WHERE province_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $province_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $cities[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'city_code' => $row['city_code']
            ];
        }
        $stmt->close();
    } catch (Throwable $e) {}
}

echo json_encode($cities, JSON_UNESCAPED_UNICODE);
