<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['logo_header']) && $_FILES['logo_header']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_file($_FILES['logo_header'], 'assets/images/', ['png', 'jpg', 'jpeg', 'webp', 'svg']);
        if ($uploaded) {
            set_setting_val('site_logo_header', $uploaded);
        }
    }

    if (isset($_FILES['logo_footer']) && $_FILES['logo_footer']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_file($_FILES['logo_footer'], 'assets/images/', ['png', 'jpg', 'jpeg', 'webp', 'svg']);
        if ($uploaded) {
            set_setting_val('site_logo_footer', $uploaded);
        }
    }

    set_setting_val('company_name', trim($_POST['company_name'] ?? ''));
    set_setting_val('footer_description', trim($_POST['footer_description'] ?? ''));
    set_setting_val('footer_copyright_text', trim($_POST['footer_copyright_text'] ?? ''));
    set_setting_val('footer_services_title', trim($_POST['footer_services_title'] ?? 'Layanan'));
    set_setting_val('footer_services_links', trim($_POST['footer_services_links'] ?? ''));
    set_setting_val('footer_info_title', trim($_POST['footer_info_title'] ?? 'Informasi'));
    set_setting_val('footer_info_links', trim($_POST['footer_info_links'] ?? ''));
    set_setting_val('contact_phone_1', trim($_POST['contact_phone_1'] ?? ''));
    set_setting_val('contact_phone_2', trim($_POST['contact_phone_2'] ?? ''));
    set_setting_val('contact_phone_3', trim($_POST['contact_phone_3'] ?? ''));
    set_setting_val('working_hours', trim($_POST['working_hours'] ?? ''));
    set_setting_val('workshop_address', trim($_POST['workshop_address'] ?? ''));
    set_setting_val('social_tiktok', trim($_POST['social_tiktok'] ?? ''));
    set_setting_val('social_instagram', trim($_POST['social_instagram'] ?? ''));
    set_setting_val('social_facebook', trim($_POST['social_facebook'] ?? ''));
    set_setting_val('gmaps_embed_url', trim($_POST['gmaps_embed_url'] ?? ''));
    set_setting_val('floating_wa_phone', trim($_POST['floating_wa_phone'] ?? ''));
    set_setting_val('floating_wa_template', trim($_POST['floating_wa_template'] ?? ''));

    set_flash('success', 'Pengaturan global berhasil diperbarui.');
    header('Location: pengaturan.php');
    exit;
}

$pageKey = 'pengaturan';
$pageTitle = 'Pengaturan Global';
$pageSubtitle = 'Kelola identitas website, seluruh elemen footer, kontak, sosial media dan floating widget';

require_once __DIR__ . '/inc/header.php';

$logoHeader = get_setting_val('site_logo_header', 'assets/images/Logo Biru-TurikalePrint.png');
$logoFooter = get_setting_val('site_logo_footer', 'assets/images/Logo Putih-TurikalePrint.png');
$companyName = get_setting_val('company_name', 'CV. Opu Barakati Jaya');
$footerDesc = get_setting_val('footer_description', 'Pusat percetakan digital profesional dengan layanan berkualitas tinggi, cepat, dan terpercaya. Kami melayani segala kebutuhan cetak Anda dengan teknologi terkini.');
$footerCopyright = get_setting_val('footer_copyright_text', '© 2026 Turikale Print. Semua hak dilindungi.');
$footerServicesTitle = get_setting_val('footer_services_title', 'Layanan');
$defaultServices = "Spanduk | /#produk-section\nStiker | /#produk-section\nKartu Nama | /#produk-section\nStempel | /#produk-section\nBanner | /#produk-section\nOutdoor Media | /#produk-section";
$footerServicesLinks = get_setting_val('footer_services_links', $defaultServices);
$footerInfoTitle = get_setting_val('footer_info_title', 'Informasi');
$defaultInfo = "Tentang Kami | /profil\nAset Logo | /aset-logo\nHubungi Kami | https://wa.me/6282188579988?text=Halo%20saya%20ingin%20bertanya";
$footerInfoLinks = get_setting_val('footer_info_links', $defaultInfo);

$phone1 = get_setting_val('contact_phone_1', '+62 821-8857-9988');
$phone2 = get_setting_val('contact_phone_2', '+62 815-2401-1136');
$phone3 = get_setting_val('contact_phone_3', '+62 896-0149-3996');
$hours = get_setting_val('working_hours', 'Senin – Minggu: 08.00 – 21.00 WITA');
$address = get_setting_val('workshop_address', "Jl. Nasrun Amrulllah No. 5, Pettuadae,\nKec. Turikale, Kabupaten Maros,\nSulawesi Selatan 90511");
$tiktok = get_setting_val('social_tiktok', 'https://www.tiktok.com/@turikale_digitalprinting');
$instagram = get_setting_val('social_instagram', 'https://www.instagram.com/turikale.print');
$facebook = get_setting_val('social_facebook', '');
$gmaps = get_setting_val('gmaps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12399.668396465226!2d119.56715520000002!3d-5.0193679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbef92997e8dd0b%3A0xf34aa0af5aa85dd9!2sTurikale%20Print!5e1!3m2!1sid!2sid!4v1772575540497!5m2!1sid!2sid');
$waPhone = get_setting_val('floating_wa_phone', '6282188579988');
$waTemplate = get_setting_val('floating_wa_template', 'Halo Turikale Print, saya ingin bertanya tentang layanan cetak.');
?>

<form method="POST" action="pengaturan.php" enctype="multipart/form-data" class="space-y-8 max-w-5xl">
    <div class="flex items-center justify-between pb-2 border-b border-[#1e2d3d]">
        <div>
            <h2 class="text-lg font-bold text-white">Konfigurasi Sistem Global</h2>
            <p class="text-xs text-slate-400">Seluruh perubahan akan langsung diterapkan pada seluruh halaman publik</p>
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-blue-600 hover:from-primary-dark hover:to-blue-700 text-white font-bold text-xs shadow-lg shadow-primary/30 flex items-center gap-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-lg">save</span>
            <span>Simpan Perubahan</span>
        </button>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
        <div class="flex items-center gap-3 pb-4 border-b border-[#1b2633]">
            <span class="material-symbols-outlined text-primary text-2xl">badge</span>
            <div>
                <h3 class="text-base font-bold text-white">Identitas Web (Logo)</h3>
                <p class="text-xs text-slate-400">Kelola logo pada navbar header dan background gelap footer</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-5 rounded-2xl bg-[#0f1722] border border-[#1d2a3a] space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-300">Logo Header (Warna)</label>
                    <span class="text-[10px] text-slate-400">Format PNG / WebP</span>
                </div>
                <div class="h-20 bg-white/95 rounded-xl flex items-center justify-center p-3 border border-slate-300">
                    <img src="<?= htmlspecialchars(cms_asset_url($logoHeader)) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($logoHeader), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($logoHeader), '/') ?>';}" alt="Preview Logo Header" class="max-h-full object-contain">
                </div>
                <div>
                    <input type="file" name="logo_header" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer">
                    <p class="text-[10px] text-slate-500 mt-1.5">File saat ini: <?= htmlspecialchars($logoHeader) ?></p>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-[#0f1722] border border-[#1d2a3a] space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-300">Logo Footer (Putih)</label>
                    <span class="text-[10px] text-slate-400">Format PNG / WebP</span>
                </div>
                <div class="h-20 bg-[#101822] rounded-xl flex items-center justify-center p-3 border border-[#233346]">
                    <img src="<?= htmlspecialchars(cms_asset_url($logoFooter)) ?>" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src='https://turikaleprint.space/<?= ltrim(htmlspecialchars($logoFooter), '/') ?>';}else if(this.dataset.tried=='1'){this.dataset.tried=2;this.src='asset.php?p=<?= ltrim(htmlspecialchars($logoFooter), '/') ?>';}" alt="Preview Logo Footer" class="max-h-full object-contain">
                </div>
                <div>
                    <input type="file" name="logo_footer" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer">
                    <p class="text-[10px] text-slate-500 mt-1.5">File saat ini: <?= htmlspecialchars($logoFooter) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
        <div class="flex items-center gap-3 pb-4 border-b border-[#1b2633]">
            <span class="material-symbols-outlined text-amber-400 text-2xl">dock_to_bottom</span>
            <div>
                <h3 class="text-base font-bold text-white">Elemen Navigasi & Teks Footer</h3>
                <p class="text-xs text-slate-400">Pengaturan seluruh kolom navigasi, judul, link, hak cipta, dan badan usaha di footer</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nama Badan Usaha / Toko</label>
                <input type="text" name="company_name" value="<?= htmlspecialchars($companyName) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Teks Hak Cipta (Copyright)</label>
                <input type="text" name="footer_copyright_text" value="<?= htmlspecialchars($footerCopyright) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Deskripsi Footer (Di Bawah Logo)</label>
            <textarea name="footer_description" rows="3" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($footerDesc) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="p-5 rounded-2xl bg-[#0b1017] border border-[#1f2d3d] space-y-3">
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">Judul Kolom Navigasi 1</label>
                <input type="text" name="footer_services_title" value="<?= htmlspecialchars($footerServicesTitle) ?>" placeholder="Layanan" class="w-full h-10 px-3 rounded-xl bg-[#121c27] border border-[#223347] text-white text-xs focus:outline-none focus:border-primary">

                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mt-3">Daftar Link Kolom 1</label>
                <textarea name="footer_services_links" rows="6" class="w-full p-3 rounded-xl bg-[#121c27] border border-[#223347] text-white font-mono text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($footerServicesLinks) ?></textarea>
                <p class="text-[11px] text-slate-500">Format per baris: <code>Nama Menu | URL</code> (Contoh: <code>Spanduk | /#produk-section</code>)</p>
            </div>

            <div class="p-5 rounded-2xl bg-[#0b1017] border border-[#1f2d3d] space-y-3">
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">Judul Kolom Navigasi 2</label>
                <input type="text" name="footer_info_title" value="<?= htmlspecialchars($footerInfoTitle) ?>" placeholder="Informasi" class="w-full h-10 px-3 rounded-xl bg-[#121c27] border border-[#223347] text-white text-xs focus:outline-none focus:border-primary">

                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mt-3">Daftar Link Kolom 2</label>
                <textarea name="footer_info_links" rows="6" class="w-full p-3 rounded-xl bg-[#121c27] border border-[#223347] text-white font-mono text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($footerInfoLinks) ?></textarea>
                <p class="text-[11px] text-slate-500">Format per baris: <code>Nama Menu | URL</code> (Contoh: <code>Tentang Kami | /profil</code>)</p>
            </div>
        </div>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
        <div class="flex items-center gap-3 pb-4 border-b border-[#1b2633]">
            <span class="material-symbols-outlined text-emerald-400 text-2xl">call</span>
            <div>
                <h3 class="text-base font-bold text-white">Kontak, Alamat & Jam Operasional</h3>
                <p class="text-xs text-slate-400">Informasi yang tampil di footer dan halaman beranda</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">No. Telepon / WA 1 (Footer)</label>
                <input type="text" name="contact_phone_1" value="<?= htmlspecialchars($phone1) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">No. Telepon / WA 2</label>
                <input type="text" name="contact_phone_2" value="<?= htmlspecialchars($phone2) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">No. Telepon / WA 3</label>
                <input type="text" name="contact_phone_3" value="<?= htmlspecialchars($phone3) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Jam Operasional</label>
                <input type="text" name="working_hours" value="<?= htmlspecialchars($hours) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Alamat Workshop</label>
                <textarea name="workshop_address" rows="3" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary"><?= htmlspecialchars($address) ?></textarea>
            </div>
        </div>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
        <div class="flex items-center gap-3 pb-4 border-b border-[#1b2633]">
            <span class="material-symbols-outlined text-blue-400 text-2xl">share</span>
            <div>
                <h3 class="text-base font-bold text-white">Tautan Sosial Media & Integrasi Maps</h3>
                <p class="text-xs text-slate-400">Profil media sosial footer dan peta workshop Google Maps</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">URL Profil TikTok</label>
                <input type="url" name="social_tiktok" value="<?= htmlspecialchars($tiktok) ?>" placeholder="https://www.tiktok.com/@..." class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">URL Profil Instagram</label>
                <input type="url" name="social_instagram" value="<?= htmlspecialchars($instagram) ?>" placeholder="https://www.instagram.com/..." class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">URL Halaman Facebook (Opsional)</label>
                <input type="url" name="social_facebook" value="<?= htmlspecialchars($facebook) ?>" placeholder="https://facebook.com/..." class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">URL Embed Google Maps (iframe src)</label>
            <textarea name="gmaps_embed_url" rows="3" class="w-full p-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary"><?= htmlspecialchars($gmaps) ?></textarea>
            <p class="text-[10px] text-slate-500 mt-1">Masukkan URL atribut <code>src</code> dari embed Google Maps.</p>
        </div>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl p-6 lg:p-8 space-y-6 shadow-xl">
        <div class="flex items-center gap-3 pb-4 border-b border-[#1b2633]">
            <span class="material-symbols-outlined text-teal-400 text-2xl">chat</span>
            <div>
                <h3 class="text-base font-bold text-white">Floating WhatsApp Widget</h3>
                <p class="text-xs text-slate-400">Tombol obrolan melayang di pojok kanan bawah website</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nomor WA Tujuan</label>
                <input type="text" name="floating_wa_phone" value="<?= htmlspecialchars($waPhone) ?>" placeholder="6282188579988" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs font-mono focus:outline-none focus:border-primary">
                <p class="text-[10px] text-slate-500 mt-1">Gunakan kode negara (contoh: 6282188579988 tanpa + atau spasi)</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Template Pesan Default</label>
                <input type="text" name="floating_wa_template" value="<?= htmlspecialchars($waTemplate) ?>" class="w-full h-11 px-3.5 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>
        </div>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-primary to-blue-600 hover:from-primary-dark hover:to-blue-700 text-white font-bold text-sm shadow-xl shadow-primary/30 flex items-center gap-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-xl">save</span>
            <span>Simpan Seluruh Pengaturan</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
