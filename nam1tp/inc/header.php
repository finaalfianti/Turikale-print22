<?php
require_once __DIR__ . '/auth.php';
require_admin_auth();

$user = current_admin();
$currentPage = $pageKey ?? 'dashboard';

$unreadCount = 0;
try {
    $unreadStmt = db()->query("SELECT COUNT(*) AS c FROM layouting_feedbacks WHERE status = 'unread'");
    if ($unreadStmt) {
        $unreadCount = (int)($unreadStmt->fetch()['c'] ?? 0);
    }
} catch (Throwable $e) {}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Panel CMS') ?> — Turikale Print</title>
    <link rel="icon" type="image/jpeg" href="<?= htmlspecialchars(cms_asset_url('assets/images/Logo-Icon-TurikalePrint.jpg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>if (typeof Cropper === 'undefined') { document.write('<script src="inc/cropper/cropper.min.js"><\/script>'); }</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#136dec',
                        'primary-dark': '#0d55bc',
                        secondary: '#10b981',
                        dark: '#101822',
                        'dark-card': '#15202b',
                        'dark-border': '#1e2d3d'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(16, 24, 34, 0.4); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e2d3d; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #136dec; }
    </style>
</head>
<body class="h-full bg-[#0d131a] text-slate-100 font-sans antialiased overflow-hidden flex">

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-[#101822] border-r border-[#1c2836] flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0">
        <div class="h-20 flex items-center justify-between px-6 border-b border-[#1c2836]">
            <a href="index.php" class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-gradient-to-tr from-primary to-emerald-400 p-0.5 shadow-lg shadow-primary/20">
                    <div class="w-full h-full bg-[#101822] rounded-[10px] flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl font-bold">layers</span>
                    </div>
                </div>
                <div>
                    <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">Turikale CMS</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-emerald-400">Admin Control</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden size-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 custom-scrollbar">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-300">Menu Utama</div>

            <a href="index.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'dashboard' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'dashboard' ? 'text-white' : 'text-primary' ?>">dashboard</span>
                <span>Dashboard</span>
            </a>

            <a href="pengaturan.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'pengaturan' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'pengaturan' ? 'text-white' : 'text-blue-400' ?>">tune</span>
                <span>Pengaturan Global</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-300">Pengelolaan Halaman</div>

            <a href="beranda.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'beranda' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'beranda' ? 'text-white' : 'text-emerald-400' ?>">home_repair_service</span>
                <span>Halaman Beranda</span>
            </a>

            <a href="profil.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'profil' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'profil' ? 'text-white' : 'text-indigo-400' ?>">badge</span>
                <span>Halaman Profil</span>
            </a>

            <a href="aset-logo.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'aset-logo' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'aset-logo' ? 'text-white' : 'text-amber-400' ?>">palette</span>
                <span>Katalog Aset Logo</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-300">Inbox & Pengunjung</div>

            <a href="saran.php" class="flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'saran' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'saran' ? 'text-white' : 'text-rose-400' ?>">forward_to_inbox</span>
                    <span>Saran Masuk</span>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <span class="px-2 py-0.5 text-[11px] font-extrabold rounded-full bg-rose-500 text-white animate-pulse"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>

            <a href="visitor-logs.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'visitor-logs' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'visitor-logs' ? 'text-white' : 'text-teal-400' ?>">troubleshoot</span>
                <span>Log Visitor</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-300">Sistem</div>

            <a href="users.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all <?= $currentPage === 'users' ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'text-slate-200 hover:bg-[#15202b] hover:text-white' ?>">
                <span class="material-symbols-outlined text-[20px] <?= $currentPage === 'users' ? 'text-white' : 'text-sky-400' ?>">admin_panel_settings</span>
                <span>Manajemen Admin</span>
            </a>
        </nav>

        <div class="p-4 border-t border-[#1c2836] bg-[#0d141d]/70">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-full bg-gradient-to-tr from-primary to-emerald-500 flex items-center justify-center text-xs font-bold text-white uppercase shadow-md">
                        <?= substr($user['username'], 0, 2) ?>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white truncate max-w-[120px]"><?= htmlspecialchars($user['fullname']) ?></div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider"><?= htmlspecialchars($user['role']) ?></div>
                    </div>
                </div>
                <a href="logout.php" title="Keluar dari CMS" class="size-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span>
                </a>
            </div>
            <a href="/" target="_blank" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-[#192433] hover:bg-[#202f42] text-xs font-semibold text-slate-300 hover:text-white border border-[#233346] transition-all">
                <span class="material-symbols-outlined text-sm text-emerald-400">open_in_new</span>
                <span>Lihat Website Publik</span>
            </a>
        </div>
    </aside>

    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden md:hidden"></div>

    <div class="flex-1 flex flex-col md:pl-72 h-full min-w-0">
        <header class="h-20 bg-[#101822]/90 backdrop-blur-md border-b border-[#1c2836] px-6 lg:px-10 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden size-10 rounded-xl bg-[#172230] text-slate-300 hover:text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
                <div>
                    <h1 class="text-lg lg:text-xl font-extrabold text-white flex items-center gap-2">
                        <span><?= htmlspecialchars($pageTitle ?? 'Panel CMS') ?></span>
                    </h1>
                    <p class="text-xs text-slate-400 hidden sm:block"><?= htmlspecialchars($pageSubtitle ?? 'Kelola konten website Turikale Print') ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#15202b] border border-[#1e2d3d] text-xs text-slate-300">
                    <span class="size-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Database Online</span>
                </div>
                <a href="saran.php" class="relative size-10 rounded-xl bg-[#15202b] hover:bg-[#1a2938] border border-[#1e2d3d] flex items-center justify-center text-slate-300 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-xl">notifications</span>
                    <?php if ($unreadCount > 0): ?>
                        <span class="absolute -top-1 -right-1 size-4 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 custom-scrollbar">
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 border shadow-xl <?= $flash['type'] === 'success' ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-200' : 'bg-rose-950/40 border-rose-500/40 text-rose-200' ?>">
                    <span class="material-symbols-outlined text-2xl shrink-0"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                    <div class="text-sm font-semibold flex-1"><?= htmlspecialchars($flash['text']) ?></div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            <?php endif; ?>
