<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'mark_read' && $id > 0) {
        $stmt = $db->prepare("UPDATE layouting_feedbacks SET status = 'read' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Pesan ditandai sebagai sudah dibaca.');
        header('Location: saran.php');
        exit;
    }

    if ($action === 'mark_unread' && $id > 0) {
        $stmt = $db->prepare("UPDATE layouting_feedbacks SET status = 'unread' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Pesan ditandai sebagai belum dibaca.');
        header('Location: saran.php');
        exit;
    }

    if ($action === 'delete' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM layouting_feedbacks WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Pesan berhasil dihapus.');
        header('Location: saran.php');
        exit;
    }

    if ($action === 'mark_all_read') {
        $db->query("UPDATE layouting_feedbacks SET status = 'read' WHERE status = 'unread'");
        set_flash('success', 'Semua pesan ditandai sebagai sudah dibaca.');
        header('Location: saran.php');
        exit;
    }
}

$pageKey = 'saran';
$pageTitle = 'Inbox Saran Masuk';
$pageSubtitle = 'Daftar pesan masukan, saran fitur, dan laporan dari formulir page layouting';

require_once __DIR__ . '/inc/header.php';

$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = ["1=1"];
$params = [];

if ($filter === 'unread') {
    $where[] = "status = 'unread'";
} elseif ($filter === 'read') {
    $where[] = "status = 'read'";
}

if ($search !== '') {
    $where[] = "(nama LIKE ? OR kontak LIKE ? OR isi_feedback LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereSql = implode(' AND ', $where);

$feedbacks = [];
try {
    $stmt = $db->prepare("SELECT * FROM layouting_feedbacks WHERE {$whereSql} ORDER BY id DESC LIMIT 100");
    $stmt->execute($params);
    $feedbacks = $stmt->fetchAll();
} catch (Throwable $e) {}

$unreadCount = (int)$db->query("SELECT COUNT(*) FROM layouting_feedbacks WHERE status = 'unread'")->fetchColumn();
$totalCount = (int)$db->query("SELECT COUNT(*) FROM layouting_feedbacks")->fetchColumn();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="saran.php?status=all" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all <?= $filter === 'all' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
                Semua (<?= $totalCount ?>)
            </a>
            <a href="saran.php?status=unread" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 <?= $filter === 'unread' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
                <span>Belum Dibaca</span>
                <?php if ($unreadCount > 0): ?>
                    <span class="px-1.5 py-0.2 rounded-full bg-white/20 text-[10px]"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="saran.php?status=read" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all <?= $filter === 'read' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
                Sudah Dibaca
            </a>
        </div>

        <div class="flex items-center gap-2">
            <form method="GET" action="saran.php" class="relative">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter) ?>">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / pesan..." class="h-9 pl-9 pr-3 rounded-xl bg-[#121c27] border border-[#1e2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </form>

            <?php if ($unreadCount > 0): ?>
                <form method="POST" action="saran.php">
                    <input type="hidden" name="action" value="mark_all_read">
                    <button type="submit" class="h-9 px-3 rounded-xl bg-[#15202b] hover:bg-[#1a2838] border border-[#1e2d3d] text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                        <span class="material-symbols-outlined text-sm text-emerald-400">done_all</span>
                        <span class="hidden sm:inline">Tandai Semua Dibaca</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-[#101822]">
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Nama Pengirim</th>
                        <th class="py-3.5 px-4">Kontak</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Isi Pesan / Saran</th>
                        <th class="py-3.5 px-4">Tanggal Masuk</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#182330]">
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-600">inbox</span>
                                <span>Tidak ada pesan saran yang ditemukan.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $fb): ?>
                        <tr class="hover:bg-[#152230]/50 transition-colors <?= $fb['status'] === 'unread' ? 'bg-rose-500/[0.03]' : '' ?>">
                            <td class="py-3.5 px-4">
                                <?php if ($fb['status'] === 'unread'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        <span class="size-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                        <span>Baru</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-400">
                                        <span>Dibaca</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white whitespace-nowrap">
                                <?= htmlspecialchars($fb['nama'] ?: 'Anonim') ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-300 whitespace-nowrap">
                                <?php if (!empty($fb['kontak']) && $fb['kontak'] !== '-'): ?>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $fb['kontak']) ?>" target="_blank" class="hover:text-emerald-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs text-emerald-400">chat</span>
                                        <span><?= htmlspecialchars($fb['kontak']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#1b2837] text-slate-300">
                                    <?= htmlspecialchars($fb['kategori']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 max-w-xs truncate cursor-pointer" onclick='viewFeedback(<?= json_encode($fb) ?>)' title="Klik untuk baca selengkapnya">
                                <span class="hover:text-primary transition-colors"><?= htmlspecialchars($fb['isi_feedback']) ?></span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                <?= date('d M Y, H:i', strtotime($fb['created_at'])) ?>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick='viewFeedback(<?= json_encode($fb) ?>)' class="size-7 rounded-lg bg-primary/10 hover:bg-primary text-primary hover:text-white flex items-center justify-center transition-colors" title="Buka Detail">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </button>
                                    <?php if ($fb['status'] === 'unread'): ?>
                                        <form method="POST" action="saran.php" class="inline">
                                            <input type="hidden" name="action" value="mark_read">
                                            <input type="hidden" name="id" value="<?= $fb['id'] ?>">
                                            <button type="submit" class="size-7 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white flex items-center justify-center transition-colors" title="Tandai Sudah Dibaca">
                                                <span class="material-symbols-outlined text-sm">done</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="saran.php" class="inline">
                                            <input type="hidden" name="action" value="mark_unread">
                                            <input type="hidden" name="id" value="<?= $fb['id'] ?>">
                                            <button type="submit" class="size-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors" title="Tandai Belum Dibaca">
                                                <span class="material-symbols-outlined text-sm">mark_email_unread</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="saran.php" onsubmit="return confirm('Hapus pesan masukan ini?')" class="inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $fb['id'] ?>">
                                        <button type="submit" class="size-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="feedbackDetailModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-400">mail</span>
                <h3 class="text-sm font-bold text-white">Detail Pesan Masukan</h3>
            </div>
            <button type="button" onclick="closeFeedbackDetail()" class="text-slate-400 hover:text-white">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3 p-3 rounded-2xl bg-[#0b1017] border border-[#1e2d3d]">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Pengirim</span>
                    <span id="fb_modal_nama" class="text-white font-bold text-sm"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Kontak / WhatsApp</span>
                    <span id="fb_modal_kontak" class="text-emerald-400 font-mono font-semibold"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Kategori Masukan</span>
                    <span id="fb_modal_kategori" class="text-blue-400 font-semibold"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Waktu Dikirim</span>
                    <span id="fb_modal_waktu" class="text-slate-400 font-mono"></span>
                </div>
            </div>

            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1.5">Isi Pesan / Saran</span>
                <div id="fb_modal_isi" class="p-4 rounded-2xl bg-[#0b1017] border border-[#1e2d3d] text-slate-200 text-xs leading-relaxed whitespace-pre-wrap"></div>
            </div>

            <div class="p-2.5 rounded-xl bg-[#0b1017] border border-[#1e2d3d] text-[10px] text-slate-500 font-mono space-y-0.5">
                <div><span class="text-slate-600">IP:</span> <span id="fb_modal_ip"></span></div>
                <div class="truncate"><span class="text-slate-600">UA:</span> <span id="fb_modal_ua"></span></div>
            </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-[#1e2d3d]">
            <form id="markReadForm" method="POST" action="saran.php">
                <input type="hidden" name="action" value="mark_read">
                <input type="hidden" id="fb_modal_id" name="id" value="0">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-md">
                    <span class="material-symbols-outlined text-sm">done</span>
                    <span>Tandai Sudah Dibaca</span>
                </button>
            </form>
            <button type="button" onclick="closeFeedbackDetail()" class="px-5 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Tutup</button>
        </div>
    </div>
</div>

<script>
    function viewFeedback(fb) {
        document.getElementById('fb_modal_id').value = fb.id;
        document.getElementById('fb_modal_nama').innerText = fb.nama || 'Anonim';
        document.getElementById('fb_modal_kontak').innerText = fb.kontak || '-';
        document.getElementById('fb_modal_kategori').innerText = fb.kategori;
        document.getElementById('fb_modal_waktu').innerText = fb.created_at;
        document.getElementById('fb_modal_isi').innerText = fb.isi_feedback;
        document.getElementById('fb_modal_ip').innerText = fb.ip_address || '-';
        document.getElementById('fb_modal_ua').innerText = fb.user_agent || '-';
        document.getElementById('feedbackDetailModal').classList.remove('hidden');
    }

    function closeFeedbackDetail() {
        document.getElementById('feedbackDetailModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
