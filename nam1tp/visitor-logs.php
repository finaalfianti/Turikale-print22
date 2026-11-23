<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'clear_old_logs') {
        $db->query("DELETE FROM visitor_logs WHERE visited_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        set_flash('success', 'Log pengunjung lebih dari 30 hari berhasil dibersihkan.');
        header('Location: visitor-logs.php');
        exit;
    }
}

$pageKey = 'visitor-logs';
$pageTitle = 'Log Visitor (Tracking Pengunjung)';
$pageSubtitle = 'Rekaman pengunjung website otomatis meliputi alamat IP, jenis perangkat, lokasi, dan waktu';

require_once __DIR__ . '/inc/header.php';

$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

$search = trim($_GET['q'] ?? '');
$filterDevice = trim($_GET['device'] ?? '');

$where = ["1=1"];
$params = [];

if ($search !== '') {
    $where[] = "(ip_address LIKE ? OR city LIKE ? OR country LIKE ? OR page_url LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($filterDevice !== '') {
    $where[] = "device = ?";
    $params[] = $filterDevice;
}

$whereSql = implode(' AND ', $where);

$totalLogs = 0;
$stmtCount = $db->prepare("SELECT COUNT(*) FROM visitor_logs WHERE {$whereSql}");
$stmtCount->execute($params);
$totalLogs = (int)$stmtCount->fetchColumn();

$totalPages = max(1, ceil($totalLogs / $limit));

$stmt = $db->prepare("SELECT * FROM visitor_logs WHERE {$whereSql} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
$stmt->execute($params);
$logs = $stmt->fetchAll();

$todayCount = (int)$db->query("SELECT COUNT(*) FROM visitor_logs WHERE DATE(visited_at) = CURDATE()")->fetchColumn();
$mobileCount = (int)$db->query("SELECT COUNT(*) FROM visitor_logs WHERE device = 'Mobile'")->fetchColumn();
$desktopCount = (int)$db->query("SELECT COUNT(*) FROM visitor_logs WHERE device = 'Desktop'")->fetchColumn();
?>

<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kunjungan Hari Ini</span>
                <div class="text-2xl font-black text-white mt-1"><?= number_format($todayCount) ?></div>
            </div>
            <div class="size-11 rounded-xl bg-blue-500/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">today</span>
            </div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Perangkat Mobile</span>
                <div class="text-2xl font-black text-amber-400 mt-1"><?= number_format($mobileCount) ?></div>
            </div>
            <div class="size-11 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">smartphone</span>
            </div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Perangkat Desktop</span>
                <div class="text-2xl font-black text-emerald-400 mt-1"><?= number_format($desktopCount) ?></div>
            </div>
            <div class="size-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">desktop_windows</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="visitor-logs.php" class="flex items-center gap-2 flex-1 max-w-lg">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari IP, kota, negara, atau URL..." class="w-full h-10 pl-10 pr-3 rounded-xl bg-[#121c27] border border-[#1e2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <select name="device" class="h-10 px-3 rounded-xl bg-[#121c27] border border-[#1e2d3d] text-white text-xs focus:outline-none focus:border-primary">
                <option value="">Semua Device</option>
                <option value="Desktop" <?= $filterDevice === 'Desktop' ? 'selected' : '' ?>>Desktop</option>
                <option value="Mobile" <?= $filterDevice === 'Mobile' ? 'selected' : '' ?>>Mobile</option>
                <option value="Tablet" <?= $filterDevice === 'Tablet' ? 'selected' : '' ?>>Tablet</option>
            </select>
            <button type="submit" class="h-10 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">Filter</button>
        </form>

        <form method="POST" action="visitor-logs.php" onsubmit="return confirm('Bersihkan rekaman log yang lebih dari 30 hari?')">
            <input type="hidden" name="action" value="clear_old_logs">
            <button type="submit" class="h-10 px-4 rounded-xl bg-[#15202b] hover:bg-rose-950/40 border border-[#1e2d3d] hover:border-rose-500/40 text-slate-300 hover:text-rose-300 text-xs font-semibold flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-sm text-rose-400">cleaning_services</span>
                <span>Bersihkan Log Lama</span>
            </button>
        </form>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-[#101822]">
                        <th class="py-3.5 px-4">Alamat IP</th>
                        <th class="py-3.5 px-4">Perangkat</th>
                        <th class="py-3.5 px-4">Lokasi Geografis</th>
                        <th class="py-3.5 px-4">Halaman Dikunjungi</th>
                        <th class="py-3.5 px-4 text-right">Waktu Akses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182330]">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="py-16 text-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-600">person_off</span>
                                <span>Belum ada log pengunjung yang sesuai dengan kriteria filter.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $row): ?>
                        <tr class="hover:bg-[#152230]/50 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-white whitespace-nowrap">
                                <?= htmlspecialchars($row['ip_address']) ?>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $row['device'] === 'Mobile' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : ($row['device'] === 'Tablet' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20') ?>">
                                    <?= htmlspecialchars($row['device']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-200 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-emerald-400">location_on</span>
                                    <span><?= htmlspecialchars(($row['city'] ?: 'Unknown') . ', ' . ($row['country'] ?: 'Indonesia')) ?></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-mono text-[11px] truncate max-w-xs" title="<?= htmlspecialchars($row['page_url']) ?>">
                                <?= htmlspecialchars($row['page_url']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                <?= date('d M Y, H:i:s', strtotime($row['visited_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-[#1e2d3d] bg-[#101822] flex items-center justify-between text-xs">
            <span class="text-slate-400">
                Menampilkan halaman <strong class="text-white"><?= $page ?></strong> dari <strong class="text-white"><?= $totalPages ?></strong> (Total <?= number_format($totalLogs) ?> kunjungan)
            </span>
            <div class="flex items-center gap-1">
                <?php if ($page > 1): ?>
                    <a href="visitor-logs.php?p=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&device=<?= urlencode($filterDevice) ?>" class="size-8 rounded-lg bg-[#15202b] text-slate-300 hover:text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                    </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="visitor-logs.php?p=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&device=<?= urlencode($filterDevice) ?>" class="size-8 rounded-lg bg-[#15202b] text-slate-300 hover:text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
