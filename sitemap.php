<?php
header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/config/database.php';

$baseUrl = 'https://turikaleprint.space';

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <url>
        <loc><?= $baseUrl ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/pages/aset-logo.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/pages/profil.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/pages/layouting.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <?php
    $res = $conn->query("SELECT title, slug, converted_filename, original_filename, updated_at FROM tp_logos WHERE status = 1 AND is_deleted = 0 ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $logoUrl  = $baseUrl . '/pages/logo-detail.php?slug=' . urlencode($row['slug']);
            $imgFilename = !empty($row['converted_filename']) ? $row['converted_filename'] : $row['original_filename'];
            $imgUrl   = $baseUrl . '/uploads/logos/' . rawurlencode($imgFilename);
            $lastMod  = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d');
            ?>
    <url>
        <loc><?= htmlspecialchars($logoUrl, ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= $lastMod ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        <image:image>
            <image:loc><?= htmlspecialchars($imgUrl, ENT_XML1, 'UTF-8') ?></image:loc>
            <image:title><?= htmlspecialchars('Download Logo ' . $row['title'] . ' PNG Transparan', ENT_XML1, 'UTF-8') ?></image:title>
        </image:image>
    </url>
            <?php
        }
    }
    ?>
</urlset>
