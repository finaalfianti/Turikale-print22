<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();
$activeTab = $_GET['tab'] ?? 'hero_stats';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_hero_stats') {
        $heroBg = trim($_POST['existing_hero_bg'] ?? '');
        if (isset($_FILES['hero_bg']) && $_FILES['hero_bg']['error'] === UPLOAD_ERR_OK) {
            $up = upload_file($_FILES['hero_bg'], 'uploads/hero/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($up) $heroBg = $up;
        }
        set_setting_val('profile_hero_bg', $heroBg);
        set_setting_val('profile_hero_title', trim($_POST['profile_hero_title'] ?? 'Profil Turikale Print'));
        set_setting_val('profile_hero_desc', trim($_POST['profile_hero_desc'] ?? ''));

        $stats = [];
        for ($i = 0; $i < 3; $i++) {
            $stats[] = [
                'icon'  => trim($_POST["stat_icon_{$i}"] ?? 'task_alt'),
                'label' => trim($_POST["stat_label_{$i}"] ?? ''),
                'value' => trim($_POST["stat_value_{$i}"] ?? ''),
                'link'  => trim($_POST["stat_link_{$i}"] ?? '')
            ];
        }
        set_setting_val('profile_stats_json', json_encode($stats, JSON_UNESCAPED_UNICODE));

        set_flash('success', 'Hero dan Statistik halaman Profil berhasil disimpan.');
        header('Location: profil.php?tab=hero_stats');
        exit;
    }

    if ($action === 'save_owner') {
        $ownerPhoto = trim($_POST['existing_owner_photo'] ?? 'assets/images/owner-image.png');
        if (isset($_FILES['owner_photo']) && $_FILES['owner_photo']['error'] === UPLOAD_ERR_OK) {
            $up = upload_file($_FILES['owner_photo'], 'assets/images/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($up) $ownerPhoto = $up;
        }
        set_setting_val('profile_owner_photo', $ownerPhoto);
        set_setting_val('profile_owner_tagline', trim($_POST['profile_owner_tagline'] ?? 'Founding Vision'));
        set_setting_val('profile_owner_name', trim($_POST['profile_owner_name'] ?? 'Andry Ridwan'));
        set_setting_val('profile_owner_title', trim($_POST['profile_owner_title'] ?? 'Pemilik Toko'));
        set_setting_val('profile_owner_bio', trim($_POST['profile_owner_bio'] ?? ''));

        set_flash('success', 'Profil Owner berhasil disimpan.');
        header('Location: profil.php?tab=owner');
        exit;
    }

    if ($action === 'save_timeline') {
        $id = (int)($_POST['id'] ?? 0);
        $year = trim($_POST['year_text'] ?? '');
        $icon = trim($_POST['icon'] ?? 'timeline');
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_timeline SET year_text = ?, icon = ?, title = ?, description = ?, sort_order = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$year, $icon, $title, $desc, $sort, $id]);
            set_flash('success', 'Peristiwa timeline berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_timeline (year_text, icon, title, description, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
            $stmt->execute([$year, $icon, $title, $desc, $sort]);
            set_flash('success', 'Peristiwa timeline baru berhasil ditambahkan.');
        }
        header('Location: profil.php?tab=timeline');
        exit;
    }

    if ($action === 'delete_timeline') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_timeline WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Peristiwa timeline berhasil dihapus.');
        }
        header('Location: profil.php?tab=timeline');
        exit;
    }

    if ($action === 'save_member') {
        $id = (int)($_POST['member_id'] ?? 0);
        $sectionId = (int)($_POST['section_id'] ?? 1);
        $batchId = (int)($_POST['batch_id'] ?? 0);
        $newBatchName = trim($_POST['new_batch_name'] ?? '');

        if ($newBatchName !== '') {
            $stmtNewBatch = $db->prepare("INSERT INTO tp_profile_batches (section_id, name, sort_order, created_at, updated_at) VALUES (?, ?, 99, NOW(), NOW())");
            $stmtNewBatch->execute([$sectionId, $newBatchName]);
            $batchId = (int)$db->lastInsertId();
        }

        if ($batchId <= 0) {
            $stmtDef = $db->prepare("SELECT id FROM tp_profile_batches WHERE section_id = ? ORDER BY id ASC LIMIT 1");
            $stmtDef->execute([$sectionId]);
            $batchId = (int)$stmtDef->fetchColumn();
            if ($batchId <= 0) {
                $stmtInsDef = $db->prepare("INSERT INTO tp_profile_batches (section_id, name, sort_order) VALUES (?, 'Default', 1)");
                $stmtInsDef->execute([$sectionId]);
                $batchId = (int)$db->lastInsertId();
            }
        }

        $name = trim($_POST['name'] ?? '');
        $instansi = trim($_POST['instansi'] ?? '');
        $jurusan = trim($_POST['jurusan'] ?? '');
        $posisi = trim($_POST['posisi'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 1);
        $status = isset($_POST['status']) ? 1 : 0;

        $photo = trim($_POST['existing_photo'] ?? '');
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $up = upload_file($_FILES['photo'], 'assets/images/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($up) $photo = $up;
        }

        $photoPos = 'object-center';
        $frameShape = 'rounded-full';

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_profile_members SET batch_id = ?, name = ?, photo = ?, instansi = ?, jurusan = ?, posisi = ?, sort_order = ?, status = ?, photo_position = ?, frame_shape = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$batchId, $name, $photo, $instansi, $jurusan, $posisi, $sortOrder, $status, $photoPos, $frameShape, $id]);
            set_flash('success', 'Data anggota berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_profile_members (batch_id, name, photo, instansi, jurusan, posisi, sort_order, status, photo_position, frame_shape, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$batchId, $name, $photo, $instansi, $jurusan, $posisi, $sortOrder, $status, $photoPos, $frameShape]);
            set_flash('success', 'Anggota baru berhasil ditambahkan.');
        }
        header('Location: profil.php?tab=tim');
        exit;
    }

    if ($action === 'delete_member') {
        $id = (int)($_POST['member_id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_profile_members WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Anggota berhasil dihapus.');
        }
        header('Location: profil.php?tab=tim');
        exit;
    }
}

$pageKey = 'profil';
$pageTitle = 'Kelola Halaman Profil';
$pageSubtitle = 'Kelola banner hero, statistik, timeline sejarah, profil owner, dan manajemen tim/magang';

require_once __DIR__ . '/inc/header.php';

$heroBg = get_setting_val('profile_hero_bg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDHGo1OGqRJ_jNnpQC_mAq_l_wN-d5UyW9mBCx56lQvWVi5Op8MCFw5dltzWaof24Uqapb5A4X-t55XrV7TIwAYUf8Xd1V712l3i37HGi33RlpGimYcSg34eE_tAFea4VszjhRlAHD04r-AQz1Esc20JvzMxhcL46uSJYtegwIdA0cLic6dtSeecY_NGvIldslxBPq3SFgZtlUeRT3PIl3HxRFJIVrf3o2GVM8juZkuffop0D1AD9sWHeJxofowbakYK5qS6t3MnWS7');
$heroTitle = get_setting_val('profile_hero_title', 'Profil Turikale Print');
$heroDesc = get_setting_val('profile_hero_desc', 'Turikale Print hadir sebagai solusi cetak untuk kebutuhan UMKM, bisnis, dan personal di Maros dan sekitarnya.');

$stats = json_decode(get_setting_val('profile_stats_json', '[]'), true);
if (empty($stats) || count($stats) < 3) {
    $stats = [
        ['icon' => 'task_alt', 'label' => 'Total Pesanan', 'value' => '500+ Selesai', 'link' => ''],
        ['icon' => 'groups', 'label' => 'Pelanggan Aktif', 'value' => '50+ Perbulan', 'link' => ''],
        ['icon' => 'stars', 'label' => 'Rating Google', 'value' => '4.9★ Verified', 'link' => 'https://www.google.com/search?q=Turikale+Print+Ulasan']
    ];
}

$ownerPhoto = get_setting_val('profile_owner_photo', 'assets/images/owner-image.png');
$ownerTagline = get_setting_val('profile_owner_tagline', 'Founding Vision');
$ownerName = get_setting_val('profile_owner_name', 'Andry Ridwan');
$ownerTitle = get_setting_val('profile_owner_title', 'Pemilik Toko');
$ownerBio = get_setting_val('profile_owner_bio', 'Andry Ridwan merupakan pendiri CV Opu Barakati Jaya sekaligus penggagas Turikale Print. Melalui komitmen pada kualitas dan pelayanan, beliau mengembangkan Turikale Print menjadi layanan percetakan yang dipercaya oleh masyarakat dan pelaku UMKM di Maros.');

$timelineEvents = [];
try {
    $stmt = $db->query("SELECT * FROM tp_timeline ORDER BY sort_order ASC, id ASC");
    $timelineEvents = $stmt->fetchAll();
} catch (Throwable $e) {}

$sections = [];
$batches = [];
$members = [];
try {
    $sections = $db->query("SELECT * FROM tp_profile_sections ORDER BY sort_order ASC, id ASC")->fetchAll();
    $batches = $db->query("SELECT * FROM tp_profile_batches ORDER BY section_id ASC, sort_order ASC")->fetchAll();
    $members = $db->query("
        SELECT m.*, b.name AS batch_name, s.title AS section_title, s.id AS sec_id
        FROM tp_profile_members m
        JOIN tp_profile_batches b ON m.batch_id = b.id
        JOIN tp_profile_sections s ON b.section_id = s.id
        ORDER BY s.sort_order ASC, b.sort_order ASC, m.sort_order ASC
    ")->fetchAll();
} catch (Throwable $e) {}
?>

<div class="space-y-6">
    <div class="flex items-center gap-2 border-b border-[#1e2d3d] pb-4 overflow-x-auto custom-scrollbar">
        <a href="profil.php?tab=hero_stats" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'hero_stats' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">dashboard_customize</span>
            <span>Hero & Statistik</span>
        </a>
        <a href="profil.php?tab=timeline" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'timeline' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">timeline</span>
            <span>Timeline Perusahaan</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($timelineEvents) ?></span>
        </a>
        <a href="profil.php?tab=owner" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'owner' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">person</span>
            <span>Profil Owner</span>
        </a>
        <a href="profil.php?tab=tim" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'tim' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">groups</span>
            <span>Tim, PKL & Magang</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($members) ?></span>
        </a>
    </div>

    <?php if ($activeTab === 'hero_stats'): ?>
        <form method="POST" action="profil.php?tab=hero_stats" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="save_hero_stats">
            <input type="hidden" name="existing_hero_bg" value="<?= htmlspecialchars($heroBg) ?>">

            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-5 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">panorama</span>
                        <span>Header Hero Profil</span>
                    </h3>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Simpan Hero & Statistik</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Judul Banner</label>
                            <input type="text" name="profile_hero_title" value="<?= htmlspecialchars($heroTitle) ?>" required class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi Singkat</label>
                            <textarea name="profile_hero_desc" rows="3" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($heroDesc) ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ganti Gambar Background Banner</label>
                            <input type="file" name="hero_bg" accept="image/*" data-crop="16:9" data-crop-shape="square" data-crop-title="Sesuaikan Background Banner Profil (16:9)" data-preview-id="hero_bg_preview" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-white file:text-xs hover:file:bg-primary-dark cursor-pointer">
                        </div>
                    </div>
                    <div class="p-3 bg-[#0b1017] rounded-2xl border border-[#1f2d3d] flex flex-col justify-center items-center overflow-hidden">
                        <span class="text-[10px] text-slate-500 mb-2">Preview Background Saat Ini</span>
                        <img id="hero_bg_preview" src="<?= htmlspecialchars(cms_asset_url($heroBg)) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($heroBg), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($heroBg), '/') ?>';}" alt="Hero BG" class="max-h-44 w-full object-cover rounded-xl shadow-md">
                    </div>
                </div>
            </div>

            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-5 shadow-xl">
                <div class="pb-3 border-b border-[#1e2d3d]">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-400">equalizer</span>
                        <span>3 Kartu Statistik Bisnis</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Metrik performa yang tampil di bawah banner profil</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <?php for ($i = 0; $i < 3; $i++): $st = $stats[$i] ?? ['icon' => 'task_alt', 'label' => '', 'value' => '', 'link' => '']; ?>
                    <div class="p-5 rounded-2xl bg-[#0f1722] border border-[#1f2d3d] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Statistik #<?= $i + 1 ?></span>
                            <span class="material-symbols-outlined text-2xl text-slate-300"><?= htmlspecialchars($st['icon']) ?></span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Ikon Material</label>
                            <input type="text" name="stat_icon_<?= $i ?>" value="<?= htmlspecialchars($st['icon']) ?>" class="w-full h-9 px-3 rounded-lg bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Nilai / Angka</label>
                            <input type="text" name="stat_value_<?= $i ?>" value="<?= htmlspecialchars($st['value']) ?>" placeholder="misal: 500+ Selesai" class="w-full h-9 px-3 rounded-lg bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-bold focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Label Keterangan</label>
                            <input type="text" name="stat_label_<?= $i ?>" value="<?= htmlspecialchars($st['label']) ?>" placeholder="misal: Total Pesanan" class="w-full h-9 px-3 rounded-lg bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Tautan Link (Opsional)</label>
                            <input type="url" name="stat_link_<?= $i ?>" value="<?= htmlspecialchars($st['link']) ?>" placeholder="https://..." class="w-full h-9 px-3 rounded-lg bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </form>

    <?php elseif ($activeTab === 'timeline'): ?>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white">Timeline Perjalanan Perusahaan</h2>
                    <p class="text-xs text-slate-400">Milestone perjalanan dari awal berdiri hingga saat ini</p>
                </div>
                <button type="button" onclick="openTimelineModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-base">add</span>
                    <span>Tambah Milestone</span>
                </button>
            </div>

            <div class="space-y-3">
                <?php foreach ($timelineEvents as $tl): ?>
                <div class="p-5 rounded-2xl bg-[#121c27] border border-[#1e2d3d] flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-primary/40 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="size-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl"><?= htmlspecialchars($tl['icon']) ?></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-primary text-white"><?= htmlspecialchars($tl['year_text']) ?></span>
                                <h3 class="text-sm font-bold text-white"><?= htmlspecialchars($tl['title']) ?></h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed"><?= htmlspecialchars($tl['description']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <span class="text-[11px] text-slate-500 font-mono mr-2">Urutan: <?= (int)$tl['sort_order'] ?></span>
                        <button type="button" onclick='editTimeline(<?= json_encode($tl) ?>)' class="size-8 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <form method="POST" action="profil.php?tab=timeline" onsubmit="return confirm('Hapus milestone ini?')" class="inline">
                            <input type="hidden" name="action" value="delete_timeline">
                            <input type="hidden" name="id" value="<?= $tl['id'] ?>">
                            <button type="submit" class="size-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="timelineModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="timelineModalTitle" class="text-sm font-bold text-white">Tambah Milestone Timeline</h3>
                    <button type="button" onclick="closeTimelineModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="profil.php?tab=timeline" class="space-y-4">
                    <input type="hidden" name="action" value="save_timeline">
                    <input type="hidden" id="tl_id" name="id" value="0">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Tahun / Masa</label>
                            <input type="text" id="tl_year" name="year_text" placeholder="2017, Sekarang..." required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ikon Material</label>
                            <input type="text" id="tl_icon" name="icon" placeholder="storefront, print..." value="timeline" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Judul Peristiwa</label>
                        <input type="text" id="tl_title" name="title" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi Narasi</label>
                        <textarea id="tl_desc" name="description" rows="3" required class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Urutan Tampil</label>
                        <input type="number" id="tl_sort" name="sort_order" value="1" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeTimelineModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Milestone</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'owner'): ?>
        <form method="POST" action="profil.php?tab=owner" enctype="multipart/form-data" class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl max-w-4xl">
            <input type="hidden" name="action" value="save_owner">
            <input type="hidden" name="existing_owner_photo" value="<?= htmlspecialchars($ownerPhoto) ?>">

            <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person</span>
                    <span>Informasi Profil Pemilik (Owner)</span>
                </h3>
                <button type="submit" class="px-6 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Simpan Profil Owner</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-[#0b1017] rounded-2xl border border-[#1f2d3d] flex flex-col items-center justify-center space-y-3">
                    <div class="w-36 aspect-[3/4] rounded-xl overflow-hidden border-2 border-primary/50 shadow-lg bg-slate-900">
                        <img id="owner_photo_preview" src="<?= htmlspecialchars(cms_asset_url($ownerPhoto)) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($ownerPhoto), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($ownerPhoto), '/') ?>';}" alt="Owner Preview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1 text-center">Ganti Foto Owner</label>
                        <input type="file" name="owner_photo" accept="image/*" data-crop="3:4" data-crop-shape="square" data-crop-title="Sesuaikan Foto Owner (3:4)" data-preview-id="owner_photo_preview" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-white file:text-xs hover:file:bg-primary-dark cursor-pointer">
                    </div>
                </div>

                <div class="md:col-span-2 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Tagline Kecil</label>
                            <input type="text" name="profile_owner_tagline" value="<?= htmlspecialchars($ownerTagline) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="profile_owner_name" value="<?= htmlspecialchars($ownerName) ?>" required class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-bold focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jabatan</label>
                        <input type="text" name="profile_owner_title" value="<?= htmlspecialchars($ownerTitle) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Narasi / Biografi Singkat</label>
                        <textarea name="profile_owner_bio" rows="4" class="w-full p-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs leading-relaxed focus:outline-none focus:border-primary"><?= htmlspecialchars($ownerBio) ?></textarea>
                    </div>
                </div>
            </div>
        </form>

    <?php elseif ($activeTab === 'tim'): ?>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white">Manajemen Tim Inti, PKL & Magang</h2>
                    <p class="text-xs text-slate-400">Kelola anggota per kategori dan batch</p>
                </div>
                <button type="button" onclick="openMemberModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-base">person_add</span>
                    <span>Tambah Anggota</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php foreach ($members as $m): ?>
                <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-5 shadow-lg flex flex-col justify-between group hover:border-primary/40 transition-all">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#1a2838] text-slate-300 truncate max-w-[140px]">
                                <?= htmlspecialchars($m['section_title']) ?>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase <?= $m['status'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-700 text-slate-400' ?>">
                                <?= $m['status'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>

                        <div class="flex items-center gap-3.5 mb-4">
                            <div class="size-14 rounded-full overflow-hidden border-2 border-emerald-400 shrink-0 bg-slate-800">
                                <?php if (!empty($m['photo'])): ?>
                                    <img src="<?= htmlspecialchars(cms_asset_url($m['photo'])) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($m['photo']), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($m['photo']), '/') ?>';}" alt="<?= htmlspecialchars($m['name']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center font-bold text-slate-400 text-lg">
                                        <?= substr($m['name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white"><?= htmlspecialchars($m['name']) ?></h3>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-primary/20 text-blue-300">
                                    <?= htmlspecialchars($m['posisi'] ?: 'Anggota') ?>
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1 text-xs text-slate-400 mb-4 bg-[#0c131c] p-2.5 rounded-xl border border-[#1a2636]">
                            <div class="text-[11px]"><span class="text-slate-500">Batch:</span> <?= htmlspecialchars($m['batch_name']) ?></div>
                            <?php if (!empty($m['instansi'])): ?>
                                <div class="text-[11px] truncate"><span class="text-slate-500">Asal:</span> <?= htmlspecialchars($m['instansi']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($m['jurusan'])): ?>
                                <div class="text-[11px] truncate"><span class="text-slate-500">Jurusan:</span> <?= htmlspecialchars($m['jurusan']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#1d2938] flex items-center justify-between text-xs">
                        <span class="text-[10px] text-slate-500">Urutan: <?= (int)$m['sort_order'] ?></span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick='editMember(<?= json_encode($m) ?>)' class="size-7 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <form method="POST" action="profil.php?tab=tim" onsubmit="return confirm('Hapus anggota ini?')" class="inline">
                                <input type="hidden" name="action" value="delete_member">
                                <input type="hidden" name="member_id" value="<?= $m['id'] ?>">
                                <button type="submit" class="size-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="memberModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="memberModalTitle" class="text-sm font-bold text-white">Tambah Anggota Tim / Magang</h3>
                    <button type="button" onclick="closeMemberModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="profil.php?tab=tim" enctype="multipart/form-data" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_member">
                    <input type="hidden" id="mem_id" name="member_id" value="0">
                    <input type="hidden" id="mem_existing_photo" name="existing_photo" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Kategori</label>
                        <select id="mem_section" name="section_id" onchange="filterBatches()" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= $sec['id'] ?>"><?= htmlspecialchars($sec['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Batch Yang Ada</label>
                            <select id="mem_batch" name="batch_id" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                                <?php foreach ($batches as $b): ?>
                                    <option value="<?= $b['id'] ?>" data-sec="<?= $b['section_id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Atau Input Batch Baru</label>
                            <input type="text" id="mem_new_batch" name="new_batch_name" placeholder="misal: Batch 3" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Anggota</label>
                        <input type="text" id="mem_name" name="name" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Asal Sekolah / Kampus</label>
                            <input type="text" id="mem_instansi" name="instansi" placeholder="SMKN 1 Maros..." class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jurusan / Keahlian</label>
                            <input type="text" id="mem_jurusan" name="jurusan" placeholder="DKV, TI..." class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Label Badge / Posisi</label>
                            <input type="text" id="mem_posisi" name="posisi" placeholder="Admin, Desain Grafis..." required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Urutan Tampil</label>
                            <input type="number" id="mem_sort" name="sort_order" value="1" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Upload Foto Anggota (Interaktif Crop)</label>
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded-full overflow-hidden bg-[#0b1017] border border-[#1f2d3d] shrink-0 flex items-center justify-center">
                                <img id="mem_photo_preview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                                <span id="mem_photo_placeholder" class="material-symbols-outlined text-slate-500 text-xl">account_circle</span>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photo" accept="image/*" data-crop="1:1" data-crop-shape="circle" data-crop-title="Sesuaikan Foto Anggota Tim (1:1)" data-preview-id="mem_photo_preview" class="w-full text-[11px] text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-primary/20 file:text-blue-300 file:text-xs hover:file:bg-primary hover:file:text-white cursor-pointer">
                                <p class="text-[10px] text-slate-500 mt-1">Pilih foto, modal cropper akan muncul otomatis untuk atur posisi & zoom.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="mem_status" name="status" value="1" checked class="rounded border-slate-700 text-primary">
                        <label for="mem_status" class="text-xs text-white font-semibold">Tampilkan Anggota Ini</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeMemberModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Anggota</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function openTimelineModal() {
        document.getElementById('timelineModalTitle').innerText = 'Tambah Milestone Timeline';
        document.getElementById('tl_id').value = '0';
        document.getElementById('tl_year').value = '';
        document.getElementById('tl_icon').value = 'timeline';
        document.getElementById('tl_title').value = '';
        document.getElementById('tl_desc').value = '';
        document.getElementById('tl_sort').value = '1';
        document.getElementById('timelineModal').classList.remove('hidden');
    }

    function editTimeline(data) {
        document.getElementById('timelineModalTitle').innerText = 'Edit Milestone Timeline';
        document.getElementById('tl_id').value = data.id;
        document.getElementById('tl_year').value = data.year_text;
        document.getElementById('tl_icon').value = data.icon;
        document.getElementById('tl_title').value = data.title;
        document.getElementById('tl_desc').value = data.description;
        document.getElementById('tl_sort').value = data.sort_order;
        document.getElementById('timelineModal').classList.remove('hidden');
    }

    function closeTimelineModal() {
        document.getElementById('timelineModal').classList.add('hidden');
    }

    function openMemberModal() {
        document.getElementById('memberModalTitle').innerText = 'Tambah Anggota Tim / Magang';
        document.getElementById('mem_id').value = '0';
        document.getElementById('mem_existing_photo').value = '';
        document.getElementById('mem_name').value = '';
        document.getElementById('mem_instansi').value = '';
        document.getElementById('mem_jurusan').value = '';
        document.getElementById('mem_posisi').value = '';
        document.getElementById('mem_new_batch').value = '';
        document.getElementById('mem_sort').value = '1';
        document.getElementById('mem_status').checked = true;
        const prev = document.getElementById('mem_photo_preview');
        const ph = document.getElementById('mem_photo_placeholder');
        if (prev) { prev.src = ''; prev.classList.add('hidden'); }
        if (ph) ph.classList.remove('hidden');
        filterBatches();
        document.getElementById('memberModal').classList.remove('hidden');
    }

    function editMember(data) {
        document.getElementById('memberModalTitle').innerText = 'Edit Data Anggota';
        document.getElementById('mem_id').value = data.id;
        document.getElementById('mem_existing_photo').value = data.photo || '';
        document.getElementById('mem_section').value = data.sec_id;
        filterBatches();
        document.getElementById('mem_batch').value = data.batch_id;
        document.getElementById('mem_name').value = data.name;
        document.getElementById('mem_instansi').value = data.instansi || '';
        document.getElementById('mem_jurusan').value = data.jurusan || '';
        document.getElementById('mem_posisi').value = data.posisi || '';
        document.getElementById('mem_sort').value = data.sort_order;
        document.getElementById('mem_status').checked = data.status == 1;

        const prev = document.getElementById('mem_photo_preview');
        const ph = document.getElementById('mem_photo_placeholder');
        if (data.photo) {
            if (prev) {
                prev.src = (data.photo.startsWith('http') || data.photo.startsWith('/') ? '' : '../') + data.photo;
                prev.classList.remove('hidden');
            }
            if (ph) ph.classList.add('hidden');
        } else {
            if (prev) { prev.src = ''; prev.classList.add('hidden'); }
            if (ph) ph.classList.remove('hidden');
        }

        document.getElementById('memberModal').classList.remove('hidden');
    }

    function closeMemberModal() {
        document.getElementById('memberModal').classList.add('hidden');
    }

    function filterBatches() {
        const secSelect = document.getElementById('mem_section');
        const secId = secSelect ? secSelect.value : 1;
        const batchSelect = document.getElementById('mem_batch');
        if (!batchSelect) return;
        Array.from(batchSelect.options).forEach(opt => {
            const optSec = opt.getAttribute('data-sec');
            if (optSec == secId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        const firstVisible = Array.from(batchSelect.options).find(opt => opt.style.display !== 'none');
        if (firstVisible) {
            batchSelect.value = firstVisible.value;
        }
    }
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
