<?php
$pageKey = 'dashboard';
$pageTitle = 'Dashboard Ringkasan';
$pageSubtitle = 'Pantau statistik kunjungan dan ringkasan konten website';

require_once __DIR__ . '/inc/header.php';

$db = db();

$todayVisitors = 0;
$totalVisitors = 0;
$totalLogos = 0;
$totalTeam = 0;
$unreadFeedbacks = 0;

try {
    $todayVisitors = (int)$db->query("SELECT COUNT(*) FROM visitor_logs WHERE DATE(visited_at) = CURDATE()")->fetchColumn();
    $totalVisitors = (int)$db->query("SELECT COUNT(*) FROM visitor_logs")->fetchColumn();
    $totalLogos = (int)$db->query("SELECT COUNT(*) FROM tp_logos WHERE status = 1")->fetchColumn();
    $totalTeam = (int)$db->query("SELECT COUNT(*) FROM tp_profile_members WHERE status = 1")->fetchColumn();
    $unreadFeedbacks = (int)$db->query("SELECT COUNT(*) FROM layouting_feedbacks WHERE status = 'unread'")->fetchColumn();
} catch (Throwable $e) {}

$recentVisitors = [];
try {
    $stmt = $db->query("SELECT * FROM visitor_logs ORDER BY id DESC LIMIT 8");
    $recentVisitors = $stmt->fetchAll();
} catch (Throwable $e) {}

$recentFeedbacks = [];
try {
    $stmt = $db->query("SELECT * FROM layouting_feedbacks ORDER BY id DESC LIMIT 5");
    $recentFeedbacks = $stmt->fetchAll();
} catch (Throwable $e) {}
?>

<div class="space-y-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-primary/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kunjungan Hari Ini</span>
                    <div class="text-3xl font-extrabold text-white mt-1.5"><?= number_format($todayVisitors) ?></div>
                    <span class="text-[11px] text-emerald-400 flex items-center gap-1 mt-1 font-medium">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span>Total: <?= number_format($totalVisitors) ?> kunjungan</span>
                    </span>
                </div>
                <div class="size-12 rounded-xl bg-blue-500/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">visibility</span>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-primary to-blue-400"></div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Katalog Aset Logo</span>
                    <div class="text-3xl font-extrabold text-white mt-1.5"><?= number_format($totalLogos) ?></div>
                    <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-1">
                        <span>Format PNG Transparan</span>
                    </span>
                </div>
                <div class="size-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">palette</span>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tim & Peserta Magang</span>
                    <div class="text-3xl font-extrabold text-white mt-1.5"><?= number_format($totalTeam) ?></div>
                    <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-1">
                        <span>Tim Inti, PKL, Kemnaker</span>
                    </span>
                </div>
                <div class="size-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">group</span>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-400"></div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-rose-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saran Masuk (Unread)</span>
                    <div class="text-3xl font-extrabold text-white mt-1.5"><?= number_format($unreadFeedbacks) ?></div>
                    <span class="text-[11px] text-rose-400 flex items-center gap-1 mt-1 font-medium">
                        <span class="material-symbols-outlined text-xs">mark_email_unread</span>
                        <span>Dari halaman layouting</span>
                    </span>
                </div>
                <div class="size-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">mail</span>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="pengaturan.php" class="p-5 rounded-2xl bg-[#121c27] border border-[#1e2d3d] hover:border-primary/50 hover:bg-[#152332] transition-all flex items-center gap-4">
            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">tune</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Pengaturan Global</h3>
                <p class="text-xs text-slate-400 mt-0.5">Logo, kontak, floating WA</p>
            </div>
        </a>

        <a href="beranda.php" class="p-5 rounded-2xl bg-[#121c27] border border-[#1e2d3d] hover:border-emerald-500/50 hover:bg-[#152332] transition-all flex items-center gap-4">
            <div class="size-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">view_carousel</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Halaman Beranda</h3>
                <p class="text-xs text-slate-400 mt-0.5">Hero, kalkulator & produk</p>
            </div>
        </a>

        <a href="profil.php" class="p-5 rounded-2xl bg-[#121c27] border border-[#1e2d3d] hover:border-indigo-500/50 hover:bg-[#152332] transition-all flex items-center gap-4">
            <div class="size-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Halaman Profil</h3>
                <p class="text-xs text-slate-400 mt-0.5">Timeline, owner & tim</p>
            </div>
        </a>

        <a href="aset-logo.php" class="p-5 rounded-2xl bg-[#121c27] border border-[#1e2d3d] hover:border-amber-500/50 hover:bg-[#152332] transition-all flex items-center gap-4">
            <div class="size-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">palette</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Katalog Aset Logo</h3>
                <p class="text-xs text-slate-400 mt-0.5">Master kategori & repositori</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">troubleshoot</span>
                        <span>Log Kunjungan Terbaru</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Aktivitas pengunjung yang terdeteksi secara otomatis</p>
                </div>
                <a href="visitor-logs.php" class="text-xs font-semibold text-primary hover:text-blue-400 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">IP Address</th>
                            <th class="py-3 px-3">Perangkat</th>
                            <th class="py-3 px-3">Lokasi</th>
                            <th class="py-3 px-3">Halaman</th>
                            <th class="py-3 px-3 text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#182330]">
                        <?php if (empty($recentVisitors)): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data kunjungan yang tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentVisitors as $v): ?>
                            <tr class="hover:bg-[#162331]/50 transition-colors">
                                <td class="py-3 px-3 font-mono font-medium text-slate-200"><?= htmlspecialchars($v['ip_address']) ?></td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $v['device'] === 'Mobile' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : ($v['device'] === 'Tablet' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20') ?>">
                                        <?= htmlspecialchars($v['device']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-300">
                                    <?= htmlspecialchars($v['city'] . ($v['country'] ? ', ' . $v['country'] : '')) ?>
                                </td>
                                <td class="py-3 px-3 text-slate-400 truncate max-w-[150px]" title="<?= htmlspecialchars($v['page_url']) ?>">
                                    <?= htmlspecialchars($v['page_url']) ?>
                                </td>
                                <td class="py-3 px-3 text-right text-slate-500 font-mono text-[11px]">
                                    <?= date('d M H:i', strtotime($v['visited_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 shadow-xl flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-400 text-xl">forward_to_inbox</span>
                        <span>Saran Masuk Terbaru</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Umpan balik dari page layouting</p>
                </div>
                <a href="saran.php" class="text-xs font-semibold text-primary hover:text-blue-400 flex items-center gap-1">
                    <span>Semua</span>
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar">
                <?php if (empty($recentFeedbacks)): ?>
                    <div class="py-12 text-center text-slate-500 text-xs">Belum ada saran masuk.</div>
                <?php else: ?>
                    <?php foreach ($recentFeedbacks as $f): ?>
                    <div class="p-3.5 rounded-2xl bg-[#0f1722] border <?= $f['status'] === 'unread' ? 'border-rose-500/30' : 'border-[#1b2736]' ?> hover:border-primary/40 transition-all space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-white"><?= htmlspecialchars($f['nama'] ?: 'Anonim') ?></span>
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#1a2838] text-slate-300">
                                <?= htmlspecialchars($f['kategori']) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($f['isi_feedback']) ?>
                        </p>
                        <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1">
                            <span><?= htmlspecialchars($f['kontak'] ?: '-') ?></span>
                            <span><?= date('d/m/Y H:i', strtotime($f['created_at'])) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
