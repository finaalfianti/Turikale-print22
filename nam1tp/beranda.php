<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();
$activeTab = $_GET['tab'] ?? 'hero';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_slide') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $buttonText = trim($_POST['button_text'] ?? '');
        $buttonLink = trim($_POST['button_link'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = isset($_POST['status']) ? 1 : 0;
        $focalPoint = 'object-center';
        $objectFit = 'object-cover';

        $imageUrl = trim($_POST['existing_image'] ?? 'assets/images/hero-spanduk.webp');
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = upload_file($_FILES['image'], 'uploads/hero/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($up) $imageUrl = $up;
        }

        $imageMobileUrl = trim($_POST['existing_image_mobile'] ?? '');
        if (isset($_FILES['image_mobile']) && $_FILES['image_mobile']['error'] === UPLOAD_ERR_OK) {
            $upMob = upload_file($_FILES['image_mobile'], 'uploads/hero/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($upMob) $imageMobileUrl = $upMob;
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE tp_hero_slides SET title = ?, description = ?, image_url = ?, image_mobile_url = ?, button_text = ?, button_link = ?, sort_order = ?, status = ?, focal_point = ?, object_fit = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $description, $imageUrl, $imageMobileUrl, $buttonText, $buttonLink, $sortOrder, $status, $focalPoint, $objectFit, $id]);
            set_flash('success', 'Slide hero berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_hero_slides (title, description, image_url, image_mobile_url, button_text, button_link, sort_order, status, focal_point, object_fit, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$title, $description, $imageUrl, $imageMobileUrl, $buttonText, $buttonLink, $sortOrder, $status, $focalPoint, $objectFit]);
            set_flash('success', 'Slide hero baru berhasil ditambahkan.');
        }
        header('Location: beranda.php?tab=hero');
        exit;
    }

    if ($action === 'delete_slide') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM tp_hero_slides WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Slide hero berhasil dihapus.');
        }
        header('Location: beranda.php?tab=hero');
        exit;
    }

    if ($action === 'save_calc') {
        $basePrice = (int)preg_replace('/[^0-9]/', '', $_POST['base_price'] ?? '25000');
        $footnote = trim($_POST['calc_footnote'] ?? '* Harga belum termasuk ongkos jasa desain.');
        set_setting_val('calc_base_price', (string)$basePrice);
        set_setting_val('calc_footnote', $footnote);

        try {
            $stmtP = $db->prepare("UPDATE tp_product_prices SET price = ?, updated_at = NOW() WHERE product_name = 'Spanduk'");
            $stmtP->execute([$basePrice]);
        } catch (Throwable $e) {}

        set_flash('success', 'Pengaturan dasar kalkulator berhasil disimpan.');
        header('Location: beranda.php?tab=kalkulator');
        exit;
    }

    if ($action === 'save_rounding_rule') {
        $ruleId = (int)($_POST['rule_id'] ?? 0);
        $min = (float)($_POST['min_size'] ?? 0);
        $max = (float)($_POST['max_size'] ?? 0);
        $rounded = (float)($_POST['rounded_size'] ?? 0);
        $status = isset($_POST['rule_status']) ? 1 : 0;

        if ($ruleId > 0) {
            $stmt = $db->prepare("UPDATE tp_rounding_rules SET min_size = ?, max_size = ?, rounded_size = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$min, $max, $rounded, $status, $ruleId]);
            set_flash('success', 'Aturan pembulatan berhasil diperbarui.');
        } else {
            $stmt = $db->prepare("INSERT INTO tp_rounding_rules (min_size, max_size, rounded_size, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$min, $max, $rounded, $status]);
            set_flash('success', 'Aturan pembulatan baru berhasil ditambahkan.');
        }
        header('Location: beranda.php?tab=kalkulator');
        exit;
    }

    if ($action === 'delete_rounding_rule') {
        $ruleId = (int)($_POST['rule_id'] ?? 0);
        if ($ruleId > 0) {
            $stmt = $db->prepare("DELETE FROM tp_rounding_rules WHERE id = ?");
            $stmt->execute([$ruleId]);
            set_flash('success', 'Aturan pembulatan berhasil dihapus.');
        }
        header('Location: beranda.php?tab=kalkulator');
        exit;
    }

    if ($action === 'save_product') {
        $index = isset($_POST['product_index']) && $_POST['product_index'] !== '' ? (int)$_POST['product_index'] : -1;
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['desc'] ?? '');
        $priceNum = (int)preg_replace('/[^0-9]/', '', $_POST['price'] ?? '0');
        $oldNum = (int)preg_replace('/[^0-9]/', '', $_POST['old'] ?? '0');
        $wa = trim($_POST['wa'] ?? '6282188579988');
        $msg = trim($_POST['msg'] ?? 'Halo saya ingin order');
        $suffix = trim($_POST['suffix'] ?? '');

        $discountStr = '';
        if ($oldNum > $priceNum && $oldNum > 0) {
            $pct = round((($oldNum - $priceNum) / $oldNum) * 100);
            $discountStr = '-' . $pct . '%';
        }

        $imagePath = trim($_POST['existing_image'] ?? 'assets/images/spanduk-benefit.webp');
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = upload_file($_FILES['image'], 'assets/images/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($up) $imagePath = $up;
        }

        $currentProducts = json_decode(get_setting_val('home_products_json', '[]'), true) ?? [];

        $newProductItem = [
            'name'        => $name,
            'image'       => $imagePath,
            'desc'        => $desc,
            'price'       => 'Rp' . number_format($priceNum, 0, ',', '.'),
            'old'         => $oldNum > 0 ? 'Rp' . number_format($oldNum, 0, ',', '.') : '',
            'discount'    => $discountStr,
            'wa'          => $wa,
            'msg'         => rawurlencode($msg),
            'suffix'      => $suffix
        ];

        if ($index >= 0 && isset($currentProducts[$index])) {
            $currentProducts[$index] = $newProductItem;
            set_flash('success', 'Produk berhasil diperbarui.');
        } else {
            $currentProducts[] = $newProductItem;
            set_flash('success', 'Produk baru berhasil ditambahkan.');
        }

        set_setting_val('home_products_json', json_encode(array_values($currentProducts), JSON_UNESCAPED_UNICODE));
        header('Location: beranda.php?tab=produk');
        exit;
    }

    if ($action === 'delete_product') {
        $index = (int)($_POST['product_index'] ?? -1);
        $currentProducts = json_decode(get_setting_val('home_products_json', '[]'), true) ?? [];
        if (isset($currentProducts[$index])) {
            array_splice($currentProducts, $index, 1);
            set_setting_val('home_products_json', json_encode(array_values($currentProducts), JSON_UNESCAPED_UNICODE));
            set_flash('success', 'Produk berhasil dihapus.');
        }
        header('Location: beranda.php?tab=produk');
        exit;
    }

    if ($action === 'save_advantages') {
        $advCards = [];
        for ($i = 0; $i < 3; $i++) {
            $advCards[] = [
                'icon'  => trim($_POST["adv_icon_{$i}"] ?? 'verified'),
                'title' => trim($_POST["adv_title_{$i}"] ?? ''),
                'desc'  => trim($_POST["adv_desc_{$i}"] ?? '')
            ];
        }
        set_setting_val('home_advantages_json', json_encode($advCards, JSON_UNESCAPED_UNICODE));
        set_flash('success', '3 Kartu Keunggulan Layanan berhasil disimpan.');
        header('Location: beranda.php?tab=keunggulan');
        exit;
    }
}

$pageKey = 'beranda';
$pageTitle = 'Kelola Halaman Beranda';
$pageSubtitle = 'Kelola hero slider, kalkulator harga, produk unggulan dan keunggulan layanan';

require_once __DIR__ . '/inc/header.php';

$slides = [];
try {
    $stmt = $db->query("SELECT * FROM tp_hero_slides ORDER BY sort_order ASC, id ASC");
    $slides = $stmt->fetchAll();
} catch (Throwable $e) {}

$basePrice = get_setting_val('calc_base_price', '25000');
$calcFootnote = get_setting_val('calc_footnote', '* Harga belum termasuk ongkos jasa desain.');
$rules = [];
try {
    $stmt = $db->query("SELECT * FROM tp_rounding_rules ORDER BY min_size ASC");
    $rules = $stmt->fetchAll();
} catch (Throwable $e) {}

$products = json_decode(get_setting_val('home_products_json', '[]'), true);
if (empty($products)) {
    $products = [
        ['name' => 'Spanduk', 'image' => 'assets/images/spanduk-benefit.webp', 'desc' => 'Siap untuk promosi usaha, event sekolah, atau kegiatan komunitas.', 'price' => 'Rp25.000', 'old' => 'Rp40.000', 'discount' => '-37%', 'wa' => '6282188579988', 'msg' => 'Halo%20saya%20ingin%20order%20spanduk', 'suffix' => '/m²'],
        ['name' => 'Stiker', 'image' => 'assets/images/stiker-benefit.webp', 'desc' => 'Cocok untuk label produk, branding UMKM, dan kebutuhan kreatif.', 'price' => 'Rp5.000', 'old' => 'Rp10.000', 'discount' => '-50%', 'wa' => '6282188579988', 'msg' => 'Halo%20saya%20ingin%20order%20stiker', 'suffix' => '/lembar'],
        ['name' => 'Stempel', 'image' => 'assets/images/stempel-benefit.webp', 'desc' => 'Praktis untuk kebutuhan usaha dan administrasi sehari-hari.', 'price' => 'Rp80.000', 'old' => 'Rp100.000', 'discount' => '-20%', 'wa' => '6281524011136', 'msg' => 'Halo%20saya%20ingin%20order%20stempel', 'suffix' => ''],
        ['name' => 'Brosur', 'image' => 'assets/images/brosur-benefit.webp', 'desc' => 'Solusi promosi ringkas untuk mengenalkan usaha Anda.', 'price' => 'Rp1.250', 'old' => '', 'discount' => '', 'wa' => '6289601493996', 'msg' => 'Halo%20saya%20ingin%20order%20brosur', 'suffix' => '/lembar'],
    ];
}

$advantages = json_decode(get_setting_val('home_advantages_json', '[]'), true);
if (empty($advantages) || count($advantages) < 3) {
    $advantages = [
        ['icon' => 'high_quality', 'title' => 'Berkualitas', 'desc' => 'Hasil cetak tajam dan tahan lama, pas untuk promosi dan kebutuhan bisnis Anda.'],
        ['icon' => 'bolt', 'title' => 'Cepat', 'desc' => 'Pengerjaan responsif sesuai estimasi yang disepakati, tanpa perlu menunggu lama.'],
        ['icon' => 'sell', 'title' => 'Harga Terjangkau', 'desc' => 'Solusi cetak hemat tanpa biaya berlebihan, cocok untuk semua skala usaha.']
    ];
}
?>

<div class="space-y-6">
    <div class="flex items-center gap-2 border-b border-[#1e2d3d] pb-4 overflow-x-auto custom-scrollbar">
        <a href="beranda.php?tab=hero" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'hero' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">view_carousel</span>
            <span>Hero Slider</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($slides) ?></span>
        </a>
        <a href="beranda.php?tab=kalkulator" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'kalkulator' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">calculate</span>
            <span>Kalkulator Spanduk</span>
        </a>
        <a href="beranda.php?tab=produk" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'produk' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">shopping_bag</span>
            <span>Produk Unggulan</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]"><?= count($products) ?></span>
        </a>
        <a href="beranda.php?tab=keunggulan" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $activeTab === 'keunggulan' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-[#121c27] text-slate-400 hover:text-white' ?>">
            <span class="material-symbols-outlined text-base">verified</span>
            <span>Keunggulan Layanan</span>
            <span class="px-1.5 py-0.2 rounded-md bg-white/20 text-[10px]">3 Kartu</span>
        </a>
    </div>

    <?php if ($activeTab === 'hero'): ?>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white">Daftar Slide Banner Hero</h2>
                    <p class="text-xs text-slate-400">Slider utama di bagian paling atas halaman Beranda</p>
                </div>
                <button type="button" onclick="openSlideModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-base">add</span>
                    <span>Tambah Slide</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php foreach ($slides as $s): ?>
                <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden shadow-lg flex flex-col justify-between group">
                    <div class="h-44 bg-slate-950 relative overflow-hidden">
                        <img src="<?= htmlspecialchars(cms_asset_url($s['image_url'])) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($s['image_url']), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($s['image_url']), '/') ?>';}" alt="<?= htmlspecialchars($s['title']) ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#121c27] via-transparent to-transparent"></div>
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase <?= $s['status'] ? 'bg-emerald-500/90 text-white' : 'bg-slate-700 text-slate-300' ?>">
                                <?= $s['status'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#101822]/80 text-white">
                                Urutan: <?= (int)$s['sort_order'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-white mb-1.5"><?= htmlspecialchars($s['title']) ?></h3>
                            <p class="text-xs text-slate-400 leading-relaxed mb-4"><?= htmlspecialchars($s['description']) ?></p>
                        </div>
                        <div class="pt-3 border-t border-[#1d2938] flex items-center justify-between text-xs">
                            <div class="text-[11px] text-slate-400">
                                Tombol: <span class="text-white font-medium"><?= htmlspecialchars($s['button_text']) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick='editSlide(<?= json_encode($s) ?>)' class="size-8 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form method="POST" action="beranda.php?tab=hero" onsubmit="return confirm('Hapus slide ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_slide">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="size-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="slideModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="slideModalTitle" class="text-sm font-bold text-white">Tambah Slide Hero</h3>
                    <button type="button" onclick="closeSlideModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="beranda.php?tab=hero" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="save_slide">
                    <input type="hidden" id="slide_id" name="id" value="0">
                    <input type="hidden" id="slide_existing_image" name="existing_image" value="">
                    <input type="hidden" id="slide_existing_image_mobile" name="existing_image_mobile" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Judul Slide</label>
                        <input type="text" id="slide_title" name="title" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Subjudul / Deskripsi</label>
                        <textarea id="slide_desc" name="description" rows="2" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Teks Tombol</label>
                            <input type="text" id="slide_btn_text" name="button_text" value="Pesan Sekarang" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Urutan Tampil</label>
                            <input type="number" id="slide_order" name="sort_order" value="1" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Link Tombol (WA / URL)</label>
                        <input type="text" id="slide_btn_link" name="button_link" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Gambar Banner Desktop (16:9)</label>
                            <input type="file" id="slide_image" name="image" accept="image/*" data-crop="16:9" data-crop-shape="square" data-crop-title="Sesuaikan Banner Desktop (16:9)" class="w-full text-[11px] text-slate-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Gambar Banner Mobile (9:16 Opsional)</label>
                            <input type="file" id="slide_image_mobile" name="image_mobile" accept="image/*" data-crop="9:16" data-crop-shape="square" data-crop-title="Sesuaikan Banner Mobile (9:16)" class="w-full text-[11px] text-slate-400">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="slide_status" name="status" value="1" checked class="rounded border-slate-700 text-primary">
                        <label for="slide_status" class="text-xs text-white font-semibold">Tampilkan Slide Ini (Aktif)</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeSlideModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Slide</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'kalkulator'): ?>
        <div class="space-y-8">
            <form method="POST" action="beranda.php?tab=kalkulator" class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-5 shadow-xl">
                <input type="hidden" name="action" value="save_calc">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <div>
                        <h3 class="text-base font-bold text-white">Tarif Dasar & Catatan Kalkulator</h3>
                        <p class="text-xs text-slate-400">Nilai yang digunakan untuk kalkulasi otomatis ukuran spanduk</p>
                    </div>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Simpan Tarif</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Harga Dasar per Meter Persegi (Rp)</label>
                        <input type="number" name="base_price" value="<?= htmlspecialchars($basePrice) ?>" required class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-sm font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Teks Catatan Kaki</label>
                        <input type="text" name="calc_footnote" value="<?= htmlspecialchars($calcFootnote) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                </div>
            </form>

            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <div>
                        <h3 class="text-base font-bold text-white">Tabel Aturan Pembulatan Ukuran Spanduk</h3>
                        <p class="text-xs text-slate-400">Aturan pembulatan lebar roll cetak (dalam satuan centimeter)</p>
                    </div>
                    <button type="button" onclick="openRuleModal()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span>Tambah Aturan</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4">Min (cm)</th>
                                <th class="py-3 px-4">Max (cm)</th>
                                <th class="py-3 px-4">Dibulatkan Ke (cm)</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#182330]">
                            <?php foreach ($rules as $r): ?>
                            <tr class="hover:bg-[#162331]/50">
                                <td class="py-3 px-4 font-mono font-bold text-white"><?= number_format($r['min_size'], 0) ?> cm</td>
                                <td class="py-3 px-4 font-mono font-bold text-white"><?= number_format($r['max_size'], 0) ?> cm</td>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-400"><?= number_format($r['rounded_size'], 0) ?> cm</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $r['status'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-700 text-slate-300' ?>">
                                        <?= $r['status'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick='editRule(<?= json_encode($r) ?>)' class="size-7 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <form method="POST" action="beranda.php?tab=kalkulator" onsubmit="return confirm('Hapus aturan ini?')" class="inline">
                                            <input type="hidden" name="action" value="delete_rounding_rule">
                                            <input type="hidden" name="rule_id" value="<?= $r['id'] ?>">
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
        </div>

        <div id="ruleModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="ruleModalTitle" class="text-sm font-bold text-white">Tambah Aturan Pembulatan</h3>
                    <button type="button" onclick="closeRuleModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="beranda.php?tab=kalkulator" class="space-y-4">
                    <input type="hidden" name="action" value="save_rounding_rule">
                    <input type="hidden" id="rule_id" name="rule_id" value="0">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ukuran Minimum (cm)</label>
                        <input type="number" step="0.01" id="rule_min" name="min_size" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ukuran Maksimum (cm)</label>
                        <input type="number" step="0.01" id="rule_max" name="max_size" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Dibulatkan Ke (cm)</label>
                        <input type="number" step="0.01" id="rule_rounded" name="rounded_size" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="rule_status" name="rule_status" value="1" checked class="rounded border-slate-700 text-primary">
                        <label for="rule_status" class="text-xs text-white font-semibold">Aktifkan Aturan Ini</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeRuleModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Simpan Aturan</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'produk'): ?>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white">Daftar Produk Unggulan</h2>
                    <p class="text-xs text-slate-400">Produk yang ditampilkan pada grid beranda beserta badge diskon otomatis</p>
                </div>
                <button type="button" onclick="openProductModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-base">add</span>
                    <span>Tambah Produk</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php foreach ($products as $idx => $p): ?>
                <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden shadow-lg flex flex-col justify-between group">
                    <div class="aspect-square bg-slate-950 relative overflow-hidden">
                        <img src="<?= htmlspecialchars(cms_asset_url($p['image'])) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($p['image']), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($p['image']), '/') ?>';}" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover">
                        <?php if (!empty($p['discount'])): ?>
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white shadow-md">
                                <?= htmlspecialchars($p['discount']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-white mb-1"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="text-xs text-slate-400 line-clamp-2 mb-3"><?= htmlspecialchars($p['desc']) ?></p>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-sm font-extrabold text-emerald-400"><?= htmlspecialchars($p['price']) ?></span>
                                <?php if (!empty($p['old'])): ?>
                                    <span class="text-[11px] text-slate-500 line-through"><?= htmlspecialchars($p['old']) ?></span>
                                <?php endif; ?>
                                <span class="text-[10px] text-slate-400"><?= htmlspecialchars($p['suffix'] ?? '') ?></span>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-[#1d2938] flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-mono truncate max-w-[100px]"><?= htmlspecialchars($p['wa']) ?></span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick='editProduct(<?= $idx ?>, <?= json_encode($p) ?>)' class="size-7 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <form method="POST" action="beranda.php?tab=produk" onsubmit="return confirm('Hapus produk ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="product_index" value="<?= $idx ?>">
                                    <button type="submit" class="size-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="productModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
            <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                    <h3 id="productModalTitle" class="text-sm font-bold text-white">Tambah Produk Unggulan</h3>
                    <button type="button" onclick="closeProductModal()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <form method="POST" action="beranda.php?tab=produk" enctype="multipart/form-data" class="space-y-3.5">
                    <input type="hidden" name="action" value="save_product">
                    <input type="hidden" id="product_index" name="product_index" value="-1">
                    <input type="hidden" id="product_existing_image" name="existing_image" value="">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Produk</label>
                        <input type="text" id="prod_name" name="name" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi Singkat</label>
                        <textarea id="prod_desc" name="desc" rows="2" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Harga Diskon (Rp)</label>
                            <input type="number" id="prod_price" name="price" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary" placeholder="25000">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Harga Asli (Rp Coret)</label>
                            <input type="number" id="prod_old" name="old" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary" placeholder="40000">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Satuan Suffix</label>
                            <input type="text" id="prod_suffix" name="suffix" placeholder="/m², /lembar, /pcs" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">No. WhatsApp Order</label>
                            <input type="text" id="prod_wa" name="wa" value="6282188579988" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Draft Pesan WhatsApp</label>
                        <input type="text" id="prod_msg" name="msg" value="Halo saya ingin order" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Upload Foto Produk (1:1)</label>
                        <input type="file" id="prod_image" name="image" accept="image/*" data-crop="1:1" data-crop-shape="square" data-crop-title="Sesuaikan Foto Produk (1:1)" class="w-full text-[11px] text-slate-400">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                        <button type="button" onclick="closeProductModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($activeTab === 'keunggulan'): ?>
        <form method="POST" action="beranda.php?tab=keunggulan" class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
            <input type="hidden" name="action" value="save_advantages">
            <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
                <div>
                    <h3 class="text-base font-bold text-white">Kelola 3 Kartu Keunggulan Layanan</h3>
                    <p class="text-xs text-slate-400">Tampil di bagian 'Mengapa Memilih Turikale Print?'</p>
                </div>
                <button type="submit" class="px-6 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Simpan 3 Kartu</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php for ($k = 0; $k < 3; $k++): $c = $advantages[$k] ?? ['icon' => 'verified', 'title' => '', 'desc' => '']; ?>
                <div class="p-5 rounded-2xl bg-[#0f1722] border border-[#1f2d3d] space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-primary uppercase tracking-wider">Kartu #<?= $k + 1 ?></span>
                        <span class="material-symbols-outlined text-2xl text-emerald-400"><?= htmlspecialchars($c['icon'] ?: 'verified') ?></span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Icon Material Symbol</label>
                        <input type="text" name="adv_icon_<?= $k ?>" value="<?= htmlspecialchars($c['icon']) ?>" placeholder="high_quality, bolt, sell..." class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Judul Keunggulan</label>
                        <input type="text" name="adv_title_<?= $k ?>" value="<?= htmlspecialchars($c['title']) ?>" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Deskripsi</label>
                        <textarea name="adv_desc_<?= $k ?>" rows="3" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($c['desc']) ?></textarea>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function openSlideModal() {
        document.getElementById('slideModalTitle').innerText = 'Tambah Slide Hero';
        document.getElementById('slide_id').value = '0';
        document.getElementById('slide_title').value = '';
        document.getElementById('slide_desc').value = '';
        document.getElementById('slide_btn_text').value = 'Pesan Sekarang';
        document.getElementById('slide_btn_link').value = 'https://wa.me/6281524011136';
        document.getElementById('slide_order').value = '1';
        document.getElementById('slide_status').checked = true;
        document.getElementById('slide_existing_image').value = '';
        document.getElementById('slide_existing_image_mobile').value = '';
        document.getElementById('slideModal').classList.remove('hidden');
    }

    function editSlide(data) {
        document.getElementById('slideModalTitle').innerText = 'Edit Slide Hero';
        document.getElementById('slide_id').value = data.id;
        document.getElementById('slide_title').value = data.title;
        document.getElementById('slide_desc').value = data.description;
        document.getElementById('slide_btn_text').value = data.button_text;
        document.getElementById('slide_btn_link').value = data.button_link;
        document.getElementById('slide_order').value = data.sort_order;
        document.getElementById('slide_status').checked = data.status == 1;
        document.getElementById('slide_existing_image').value = data.image_url;
        document.getElementById('slide_existing_image_mobile').value = data.image_mobile_url || '';
        document.getElementById('slideModal').classList.remove('hidden');
    }

    function closeSlideModal() {
        document.getElementById('slideModal').classList.add('hidden');
    }

    function openRuleModal() {
        document.getElementById('ruleModalTitle').innerText = 'Tambah Aturan Pembulatan';
        document.getElementById('rule_id').value = '0';
        document.getElementById('rule_min').value = '';
        document.getElementById('rule_max').value = '';
        document.getElementById('rule_rounded').value = '';
        document.getElementById('rule_status').checked = true;
        document.getElementById('ruleModal').classList.remove('hidden');
    }

    function editRule(data) {
        document.getElementById('ruleModalTitle').innerText = 'Edit Aturan Pembulatan';
        document.getElementById('rule_id').value = data.id;
        document.getElementById('rule_min').value = parseFloat(data.min_size);
        document.getElementById('rule_max').value = parseFloat(data.max_size);
        document.getElementById('rule_rounded').value = parseFloat(data.rounded_size);
        document.getElementById('rule_status').checked = data.status == 1;
        document.getElementById('ruleModal').classList.remove('hidden');
    }

    function closeRuleModal() {
        document.getElementById('ruleModal').classList.add('hidden');
    }

    function openProductModal() {
        document.getElementById('productModalTitle').innerText = 'Tambah Produk Unggulan';
        document.getElementById('product_index').value = '-1';
        document.getElementById('prod_name').value = '';
        document.getElementById('prod_desc').value = '';
        document.getElementById('prod_price').value = '';
        document.getElementById('prod_old').value = '';
        document.getElementById('prod_suffix').value = '';
        document.getElementById('prod_wa').value = '6282188579988';
        document.getElementById('prod_msg').value = 'Halo saya ingin order';
        document.getElementById('product_existing_image').value = '';
        document.getElementById('productModal').classList.remove('hidden');
    }

    function editProduct(idx, p) {
        document.getElementById('productModalTitle').innerText = 'Edit Produk Unggulan';
        document.getElementById('product_index').value = idx;
        document.getElementById('prod_name').value = p.name;
        document.getElementById('prod_desc').value = p.desc;
        const rawPrice = parseInt(p.price.replace(/[^0-9]/g, '')) || 0;
        const rawOld = p.old ? (parseInt(p.old.replace(/[^0-9]/g, '')) || '') : '';
        document.getElementById('prod_price').value = rawPrice;
        document.getElementById('prod_old').value = rawOld;
        document.getElementById('prod_suffix').value = p.suffix || '';
        document.getElementById('prod_wa').value = p.wa || '6282188579988';
        document.getElementById('prod_msg').value = decodeURIComponent(p.msg || '');
        document.getElementById('product_existing_image').value = p.image || '';
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
