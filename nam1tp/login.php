<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/auth.php';

if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Silakan masukkan username dan password.';
    } else {
        if (attempt_admin_login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password tidak sesuai. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b1017]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CMS — Turikale Print</title>
    <link rel="icon" type="image/jpeg" href="<?= htmlspecialchars(cms_asset_url('assets/images/Logo-Icon-TurikalePrint.jpg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#136dec',
                        secondary: '#10b981',
                        dark: '#101822',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 relative overflow-hidden bg-[#0a0f16]">

    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-secondary/15 blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-[#121b26]/90 backdrop-blur-xl border border-[#1e2d3d] rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/80">
            <div class="text-center mb-8">
                <div class="inline-flex size-14 rounded-2xl bg-gradient-to-tr from-primary to-emerald-400 p-0.5 shadow-xl shadow-primary/25 mb-4">
                    <div class="w-full h-full bg-[#101822] rounded-[14px] flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-2xl font-bold">admin_panel_settings</span>
                    </div>
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Turikale Print CMS</h1>
                <p class="text-xs text-slate-400 mt-1.5">Masuk untuk mengelola seluruh konten website</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center gap-3 text-rose-300 text-xs font-semibold">
                    <span class="material-symbols-outlined text-lg shrink-0">error</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Username</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xl">person</span>
                        <input
                            type="text"
                            name="username"
                            required
                            autofocus
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            placeholder="Username admin"
                            class="w-full h-12 pl-12 pr-4 rounded-xl bg-[#0a0f16] border border-[#223347] text-white placeholder-slate-500 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xl">lock</span>
                        <input
                            type="password"
                            id="passwordInput"
                            name="password"
                            required
                            placeholder="Password admin"
                            class="w-full h-12 pl-12 pr-12 rounded-xl bg-[#0a0f16] border border-[#223347] text-white placeholder-slate-500 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 focus:outline-none"
                            title="Tampilkan password"
                        >
                            <span id="eyeIcon" class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full h-12 mt-2 bg-gradient-to-r from-primary to-blue-600 hover:from-primary-dark hover:to-blue-700 text-white font-bold rounded-xl shadow-lg shadow-primary/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm"
                >
                    <span>Masuk ke Panel</span>
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-[#1c2938] text-center">
                <a href="/" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Kembali ke Website Utama</span>
                </a>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500 mt-6">
            &copy; 2026 Turikale Print &bull; CV. Opu Barakati Jaya
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
