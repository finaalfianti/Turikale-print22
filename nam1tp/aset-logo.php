<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();
$activeTab = $_GET['tab'] ?? 'repositori';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_logo') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $provinceId = (int)($_POST['province_id'] ?? 0);
        $cityId = (int)($_POST['city_id'] ?? 0);
        $status = isset($_POST['status']) ? 1 : 0;

        $slug = create_slug($title) . '-' . substr(md5(uniqid()), 0, 6);

        $convertedFilename = trim($_POST['existing_file'] ?? '');
        $originalFilename = trim($_POST['existing_orig_file'] ?? '');
        $fileSize = 0;
        $width = 0;
        $height = 0;
        $fileHash = '0';

        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_file($_FILES['logo_file'], 'uploads/logos/', ['png', 'webp', 'svg']);
            if ($uploaded) {
                $filenameOnly = basename($uploaded);
                $convertedFilename = $filenameOnly;
                $originalFilename = $_FILES['logo_file']['name'];
                $fullPath = dirname(__DIR__, 2) . '/' . $uploaded;
                if (file_exists($fullPath)) {
                    $fileSize = (int)filesize($fullPath);
                    $fileHash = hash_file('sha256', $fullPath);
                    $imgSize = @getimagesize($fullPath);
                    if ($imgSize) {
                        $width = $imgSize[0];
                        $height = $imgSize[1];
                    }
                }
            }
        }

        if ($id > 0) {
            if ($convertedFilename !== '' && $convertedFilename !== $_POST['existing_file']) {
                $stmt = $db->prepare("UPDATE tp_logos SET title = ?, category_id = ?, province_id = ?, city_id = ?, original_filename = ?, converted_filename = ?, file_hash = ?, file_size = ?, image_width = ?, image_height = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $categoryId ?: null, $provinceId ?: null, $cityId ?: null, $originalFilename, $convertedFilename, $fileHash, $fileSize, $width, $height, $status, $id]);
            } else {
                $stmt = $db->prepare("UPDATE tp_logos SET title = ?, category_id = ?, province_id = ?, city_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $categoryId ?: null, $provinceId ?: null, $cityId ?: null, $status, $id]);
            }
            set_flash('success', 'Data logo berhasil diperbarui.');
        } else {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $stmt = $db->prepare("INSERT INTO tp_logos (uuid, title, slug, category_id, province_id, city_id, original_filename, converted_filename, file_hash, file_size, mime_type, image_width, image_height, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'image/png', ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$uuid, $title, $slug, $categoryId ?: null, $provinceId ?: null, $cityId ?: null, $originalFilename ?: $convertedFilename, $convertedFilename, $fileHash, $fileSize, $width, $height, $status]);
            set_flash('success', 'Logo baru berhasil ditambahkan.');
        }

        header('Location: aset-logo.php?tab=repositori');
        exit;
    }

    if ($action === 'delete_logo') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_logos WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Logo berhasil dihapus.');
        }
        header('Location: aset-logo.php?tab=repositori');
        exit;
    }

    if ($action === 'save_category') {
        $id = (int)($_POST['cat_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'folder');
        $desc = trim($_POST['description'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $slug = create_slug($name);

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_categories SET name = ?, slug = ?, icon = ?, description = ?, sort_order = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $slug, $icon, $desc, $sort, $id]);
            set_flash('success', 'Kategori berhasil diperbarui.');
        } else {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $stmt = $db->prepare("INSERT INTO tp_categories (uuid, name, slug, icon, description, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
            $stmt->execute([$uuid, $name, $slug, $icon, $desc, $sort]);
            set_flash('success', 'Kategori baru berhasil ditambahkan.');
        }
        header('Location: aset-logo.php?tab=kategori');
        exit;
    }

    if ($action === 'delete_category') {
        $id = (int)($_POST['cat_id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_categories WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Kategori berhasil dihapus.');
        }
        header('Location: aset-logo.php?tab=kategori');
        exit;
    }

    if ($action === 'save_province') {
        $id = (int)($_POST['prov_id'] ?? 0);
        $code = trim($_POST['province_code'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_provinces SET province_code = ?, name = ? WHERE id = ?");
            $stmt->execute([$code, $name, $id]);
            set_flash('success', 'Provinsi berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_provinces (province_code, name) VALUES (?, ?)");
            $stmt->execute([$code, $name]);
            set_flash('success', 'Provinsi baru berhasil ditambahkan.');
        }
        header('Location: aset-logo.php?tab=wilayah');
        exit;
    }

    if ($action === 'delete_province') {
        $id = (int)($_POST['prov_id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_provinces WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Provinsi berhasil dihapus.');
        }
        header('Location: aset-logo.php?tab=wilayah');
        exit;
    }

    if ($action === 'save_city') {
        $id = (int)($_POST['city_id'] ?? 0);
        $provId = (int)($_POST['province_id'] ?? 0);
        $code = trim($_POST['city_code'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_cities SET province_id = ?, city_code = ?, name = ? WHERE id = ?");
            $stmt->execute([$provId, $code, $name, $id]);
            set_flash('success', 'Kota/Kabupaten berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_cities (province_id, city_code, name) VALUES (?, ?, ?)");
            $stmt->execute([$provId, $code, $name]);
            set_flash('success', 'Kota/Kabupaten baru berhasil ditambahkan.');
        }
        header('Location: aset-logo.php?tab=wilayah');
        exit;
    }

    if ($action === 'delete_city') {
        $id = (int)($_POST['city_id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_cities WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Kota/Kabupaten berhasil dihapus.');
        }
        header('Location: aset-logo.php?tab=wilayah');
        exit;
    }
}

$pageKey = 'aset-logo';
$pageTitle = 'Katalog Aset Logo';
$pageSubtitle = 'Kelola repositori logo PNG, master kategori logo, dan master wilayah (provinsi/kota)';

require_once __DIR__ . '/inc/header.php';

$categories = [];
try {
    $categories = $db->query("SELECT * FROM tp_categories ORDER BY sort_order ASC, name ASC")->fetchAll();
} catch (Throwable $e) {}

$provinces = [];
try {
    $provinces = $db->query("SELECT * FROM tp_provinces ORDER BY name ASC")->fetchAll();
} catch (Throwable $e) {}

$cities = [];
try {
    $cities = $db->query("SELECT c.*, p.name AS prov_name FROM tp_cities c JOIN tp_provinces p ON c.province_id = p.id ORDER BY p.name ASC, c.name ASC LIMIT 100")->fetchAll();
} catch (Throwable $e) {}

$logos = [];
$searchQuery = trim($_GET['q'] ?? '');
$filterCat = (int)($_GET['cat'] ?? 0);

try {
    $where = ["1=1"];
    $params = [];

    if ($searchQuery !== '') {
        $where[] = "l.title LIKE ?";
        $params[] = "%{$searchQuery}%";
    }
    if ($filterCat > 0) {
        $where[] = "l.category_id = ?";
        $params[] = $filterCat;
    }

    $whereSql = implode(' AND ', $where);
    $stmt = $db->prepare("
        SELECT l.*, c.name AS category_name, p.name AS province_name, ci.name AS city_name
        FROM tp_logos l
        LEFT JOIN tp_categories c ON l.category_id = c.id
        LEFT JOIN tp_provinces p ON l.province_id = p.id
        LEFT JOIN tp_cities ci ON l.city_id = ci.id
        WHERE {$whereSql}
        ORDER BY l.id DESC
        LIMIT 60
    ");
    $stmt->execute($params);
    $logos = $stmt->fetchAll();
} catch (Throwable $e) {}
?>

<div class="space-y-6">
    <div class="flex items-center gap-2 border-b border-[#1e2d3d] pb-4 overflow-x-auto custom-scrollbar">
        <a href="aset-logo.php?tab=repositori" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'repositori' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">collections</span>
            <span>Repositori Logo</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($logos) ?></span>
        </a>
        <a href="aset-logo.php?tab=kategori" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'kategori' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">category</span>
            <span>Master Kategori</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($categories) ?></span>
        </a>
        <a href="aset-logo.php?tab=wilayah" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'wilayah' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">map</span>
            <span>Master Wilayah (Provinsi & Kota)</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($provinces) ?> Prov</span>
        </a>
    </div>

    <?php if ($activeTab === 'repositori'): ?>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="aset-logo.php" class="flex items-center gap-2 flex-1 max-w-lg">
                    <input type="hidden" name="tab" value="repositori">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-lg">search</span>
                        <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Cari judul logo..." class="w-full h-10 pl-10 pr-3 rounded-xl bg-[#121c27] border border-[#1e2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <select name="cat" class="h-10 px-3 rounded-xl bg-[#121c27] border border-[#1e2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        <option value="0">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $filterCat === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">Filter</button>
                </form>

                <button type="button" onclick="openLogoModal()" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-primary/20 shrink-0">
                    <span class="material-symbols-outlined text-base">upload_file</span>
                    <span>Upload Logo Baru</span>
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php if (empty($logos)): ?>
                    <div class="col-span-full py-16 text-center text-slate-500 text-sm">Tidak ada logo yang ditemukan.</div>
                <?php else: ?>
                    <?php foreach ($logos as $l): ?>
                    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl p-3 shadow-md flex flex-col justify-between group hover:border-primary/50 transition-all">
                        <div>
                            <div class="aspect-square bg-[#0b1017] rounded-xl flex items-center justify-center p-3 relative overflow-hidden border border-[#1a2533]">
                                <?php
                                $imgFile = !empty($l['converted_filename']) ? $l['converted_filename'] : $l['original_filename'];
                                ?>
                                <img src="<?= htmlspecialchars(cms_asset_url('uploads/logos/' . $imgFile)) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/uploads/logos/<?= htmlspecialchars($imgFile) ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=uploads/logos/<?= htmlspecialchars($imgFile) ?>';}" alt="<?= htmlspecialchars($l['title']) ?>" class="max-h-full max-w-full object-contain">
                                <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-bold <?= $l['status'] ? 'bg-emerald-500/80 text-white' : 'bg-slate-700 text-slate-300' ?>">
                                    <?= $l['status'] ? 'Aktif' : 'Draft' ?>
                                </span>
                            </div>
                            <div class="mt-2.5">
                                <h4 class="text-xs font-bold text-white truncate" title="<?= htmlspecialchars($l['title']) ?>"><?= htmlspecialchars($l['title']) ?></h4>
                                <div class="text-[10px] text-slate-400 mt-0.5 truncate"><?= htmlspecialchars($l['category_name'] ?: 'Umum') ?></div>
                                <div class="text-[9px] text-slate-500 truncate"><?= htmlspecialchars($l['city_name'] ?: ($l['province_name'] ?: 'Nasional')) ?></div>
                            </div>
                        </div>

                        <div class="pt-2.5 mt-2 border-t border-[#1a2636] flex items-center justify-between">
                            <span class="text-[9px] font-mono text-slate-500"><?= number_format(($l['file_size'] ?? 0) / 1024, 0) ?> KB</span>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick='editLogo(<?= json_encode($l) ?>)' class="size-6 rounded-md bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                </button>
                                <form method="POST" action="aset-logo.php?tab=repositori" onsubmit="return confirm('Hapus logo ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_logo">
                                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                    <button type="submit" class="size-6 rounded-md bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="logoModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="logoModalTitle" class="text-sm font-bold text-white">Upload / Edit Logo</h3>
                    <button type="button" onclick="closeLogoModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="aset-logo.php?tab=repositori" enctype="multipart/form-data" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_logo">
                    <input type="hidden" id="logo_id" name="id" value="0">
                    <input type="hidden" id="logo_existing_file" name="existing_file" value="">
                    <input type="hidden" id="logo_existing_orig_file" name="existing_orig_file" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Judul Logo</label>
                        <input type="text" id="logo_title" name="title" placeholder="misal: Logo Kabupaten Maros" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Kategori Logo</label>
                        <select id="logo_cat" name="category_id" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Provinsi</label>
                            <select id="logo_province" name="province_id" onchange="loadCities(this.value)" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                                <option value="">-- Pilih Provinsi --</option>
                                <?php foreach ($provinces as $prov): ?>
                                    <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Kota / Kabupaten</label>
                            <select id="logo_city" name="city_id" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                                <option value="">-- Pilih Kota --</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Upload File Logo (Format PNG Transparan)</label>
                        <input type="file" name="logo_file" accept=".png,.webp,.svg" class="w-full text-[11px] text-slate-400">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="logo_status" name="status" value="1" checked class="rounded border-slate-700 text-primary">
                        <label for="logo_status" class="text-xs text-white font-semibold">Tampilkan di Katalog Publik (Status Aktif)</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeLogoModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Logo</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'kategori'): ?>
        <div class="space-y-6 max-w-4xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white">Master Kategori Logo</h2>
                    <p class="text-xs text-slate-400">Kelola kelompok kategori untuk filter aset logo</p>
                </div>
                <button type="button" onclick="openCatModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-base">add</span>
                    <span>Tambah Kategori</span>
                </button>
            </div>

            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden shadow-xl">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3.5 px-4">Ikon</th>
                            <th class="py-3.5 px-4">Nama Kategori</th>
                            <th class="py-3.5 px-4">Slug</th>
                            <th class="py-3.5 px-4">Urutan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#182330]">
                        <?php foreach ($categories as $c): ?>
                        <tr class="hover:bg-[#162331]/50">
                            <td class="py-3 px-4">
                                <span class="material-symbols-outlined text-primary text-xl"><?= htmlspecialchars($c['icon'] ?: 'folder') ?></span>
                            </td>
                            <td class="py-3 px-4 font-bold text-white"><?= htmlspecialchars($c['name']) ?></td>
                            <td class="py-3 px-4 font-mono text-slate-400"><?= htmlspecialchars($c['slug']) ?></td>
                            <td class="py-3 px-4 text-slate-300"><?= (int)$c['sort_order'] ?></td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick='editCat(<?= json_encode($c) ?>)' class="size-7 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <form method="POST" action="aset-logo.php?tab=kategori" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                                        <input type="hidden" name="action" value="delete_category">
                                        <input type="hidden" name="cat_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="size-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="catModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="catModalTitle" class="text-sm font-bold text-white">Tambah Kategori Logo</h3>
                    <button type="button" onclick="closeCatModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="aset-logo.php?tab=kategori" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_category">
                    <input type="hidden" id="cat_id" name="cat_id" value="0">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Kategori</label>
                        <input type="text" id="cat_name" name="name" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ikon Material</label>
                        <input type="text" id="cat_icon" name="icon" placeholder="school, business..." value="folder" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Urutan</label>
                        <input type="number" id="cat_sort" name="sort_order" value="1" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeCatModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'wilayah'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white">Master Provinsi (<?= count($provinces) ?>)</h3>
                        <p class="text-xs text-slate-400">Daftar provinsi Indonesia</p>
                    </div>
                    <button type="button" onclick="openProvModal()" class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>Tambah</span>
                    </button>
                </div>
                <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left text-xs">
                        <thead class="sticky top-0 bg-[#15202c]">
                            <tr class="text-slate-400 font-bold uppercase text-[10px] border-b border-[#1e2d3d]">
                                <th class="py-2.5 px-3">Kode</th>
                                <th class="py-2.5 px-3">Nama Provinsi</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#182330]">
                            <?php foreach ($provinces as $pr): ?>
                            <tr class="hover:bg-[#162331]/50">
                                <td class="py-2 px-3 font-mono text-slate-400"><?= htmlspecialchars($pr['province_code']) ?></td>
                                <td class="py-2 px-3 font-semibold text-white"><?= htmlspecialchars($pr['name']) ?></td>
                                <td class="py-2 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick='editProv(<?= json_encode($pr) ?>)' class="size-6 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white flex items-center justify-center">
                                            <span class="material-symbols-outlined text-xs">edit</span>
                                        </button>
                                        <form method="POST" action="aset-logo.php?tab=wilayah" onsubmit="return confirm('Hapus provinsi?')" class="inline">
                                            <input type="hidden" name="action" value="delete_province">
                                            <input type="hidden" name="prov_id" value="<?= $pr['id'] ?>">
                                            <button type="submit" class="size-6 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center">
                                                <span class="material-symbols-outlined text-xs">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white">Master Kota / Kabupaten</h3>
                        <p class="text-xs text-slate-400">Daftar kota/kabupaten per provinsi</p>
                    </div>
                    <button type="button" onclick="openCityModal()" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>Tambah</span>
                    </button>
                </div>
                <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left text-xs">
                        <thead class="sticky top-0 bg-[#15202c]">
                            <tr class="text-slate-400 font-bold uppercase text-[10px] border-b border-[#1e2d3d]">
                                <th class="py-2.5 px-3">Kode</th>
                                <th class="py-2.5 px-3">Nama Kota/Kabupaten</th>
                                <th class="py-2.5 px-3">Provinsi</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#182330]">
                            <?php foreach ($cities as $ct): ?>
                            <tr class="hover:bg-[#162331]/50">
                                <td class="py-2 px-3 font-mono text-slate-400"><?= htmlspecialchars($ct['city_code']) ?></td>
                                <td class="py-2 px-3 font-semibold text-white"><?= htmlspecialchars($ct['name']) ?></td>
                                <td class="py-2 px-3 text-slate-400"><?= htmlspecialchars($ct['prov_name']) ?></td>
                                <td class="py-2 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick='editCity(<?= json_encode($ct) ?>)' class="size-6 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white flex items-center justify-center">
                                            <span class="material-symbols-outlined text-xs">edit</span>
                                        </button>
                                        <form method="POST" action="aset-logo.php?tab=wilayah" onsubmit="return confirm('Hapus kota?')" class="inline">
                                            <input type="hidden" name="action" value="delete_city">
                                            <input type="hidden" name="city_id" value="<?= $ct['id'] ?>">
                                            <button type="submit" class="size-6 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center">
                                                <span class="material-symbols-outlined text-xs">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="provModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="provModalTitle" class="text-sm font-bold text-white">Tambah Provinsi</h3>
                    <button type="button" onclick="closeProvModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="aset-logo.php?tab=wilayah" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_province">
                    <input type="hidden" id="prov_id" name="prov_id" value="0">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Kode Provinsi (2 digit)</label>
                        <input type="text" id="prov_code" name="province_code" placeholder="73, 31, 32..." required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Provinsi</label>
                        <input type="text" id="prov_name" name="name" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeProvModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="cityModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="cityModalTitle" class="text-sm font-bold text-white">Tambah Kota / Kabupaten</h3>
                    <button type="button" onclick="closeCityModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="aset-logo.php?tab=wilayah" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_city">
                    <input type="hidden" id="city_id" name="city_id" value="0">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Provinsi</label>
                        <select id="city_prov_id" name="province_id" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                            <?php foreach ($provinces as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Kode Kota (4 digit)</label>
                        <input type="text" id="city_code" name="city_code" placeholder="7308..." required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Kota / Kabupaten</label>
                        <input type="text" id="city_name" name="name" placeholder="Kab. Maros..." required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeCityModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function loadCities(provId, selectedCityId = 0) {
        const citySelect = document.getElementById('logo_city');
        if (!citySelect) return;
        citySelect.innerHTML = '<option value="">Memuat kota...</option>';
        if (!provId) {
            citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            return;
        }
        fetch('../api/get-cities.php?province_id=' + provId)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Kota --</option>';
                data.forEach(c => {
                    const sel = (selectedCityId && c.id == selectedCityId) ? 'selected' : '';
                    html += `<option value="${c.id}" ${sel}>${c.name}</option>`;
                });
                citySelect.innerHTML = html;
            })
            .catch(() => {
                citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
            });
    }

    function openLogoModal() {
        document.getElementById('logoModalTitle').innerText = 'Upload Logo Baru';
        document.getElementById('logo_id').value = '0';
        document.getElementById('logo_existing_file').value = '';
        document.getElementById('logo_existing_orig_file').value = '';
        document.getElementById('logo_title').value = '';
        document.getElementById('logo_cat').value = '';
        document.getElementById('logo_province').value = '';
        document.getElementById('logo_city').innerHTML = '<option value="">-- Pilih Kota --</option>';
        document.getElementById('logo_status').checked = true;
        document.getElementById('logoModal').classList.remove('hidden');
    }

    function editLogo(data) {
        document.getElementById('logoModalTitle').innerText = 'Edit Data Logo';
        document.getElementById('logo_id').value = data.id;
        document.getElementById('logo_existing_file').value = data.converted_filename || '';
        document.getElementById('logo_existing_orig_file').value = data.original_filename || '';
        document.getElementById('logo_title').value = data.title;
        document.getElementById('logo_cat').value = data.category_id || '';
        document.getElementById('logo_province').value = data.province_id || '';
        loadCities(data.province_id, data.city_id);
        document.getElementById('logo_status').checked = data.status == 1;
        document.getElementById('logoModal').classList.remove('hidden');
    }

    function closeLogoModal() {
        document.getElementById('logoModal').classList.add('hidden');
    }

    function openCatModal() {
        document.getElementById('catModalTitle').innerText = 'Tambah Kategori Logo';
        document.getElementById('cat_id').value = '0';
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_icon').value = 'folder';
        document.getElementById('cat_sort').value = '1';
        document.getElementById('catModal').classList.remove('hidden');
    }

    function editCat(data) {
        document.getElementById('catModalTitle').innerText = 'Edit Kategori Logo';
        document.getElementById('cat_id').value = data.id;
        document.getElementById('cat_name').value = data.name;
        document.getElementById('cat_icon').value = data.icon;
        document.getElementById('cat_sort').value = data.sort_order;
        document.getElementById('catModal').classList.remove('hidden');
    }

    function closeCatModal() {
        document.getElementById('catModal').classList.add('hidden');
    }

    function openProvModal() {
        document.getElementById('provModalTitle').innerText = 'Tambah Provinsi';
        document.getElementById('prov_id').value = '0';
        document.getElementById('prov_code').value = '';
        document.getElementById('prov_name').value = '';
        document.getElementById('provModal').classList.remove('hidden');
    }

    function editProv(data) {
        document.getElementById('provModalTitle').innerText = 'Edit Provinsi';
        document.getElementById('prov_id').value = data.id;
        document.getElementById('prov_code').value = data.province_code;
        document.getElementById('prov_name').value = data.name;
        document.getElementById('provModal').classList.remove('hidden');
    }

    function closeProvModal() {
        document.getElementById('provModal').classList.add('hidden');
    }

    function openCityModal() {
        document.getElementById('cityModalTitle').innerText = 'Tambah Kota / Kabupaten';
        document.getElementById('city_id').value = '0';
        document.getElementById('city_code').value = '';
        document.getElementById('city_name').value = '';
        document.getElementById('cityModal').classList.remove('hidden');
    }

    function editCity(data) {
        document.getElementById('cityModalTitle').innerText = 'Edit Kota / Kabupaten';
        document.getElementById('city_id').value = data.id;
        document.getElementById('city_prov_id').value = data.province_id;
        document.getElementById('city_code').value = data.city_code;
        document.getElementById('city_name').value = data.name;
        document.getElementById('cityModal').classList.remove('hidden');
    }

    function closeCityModal() {
        document.getElementById('cityModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
