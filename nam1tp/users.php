<?php
require_once __DIR__ . '/inc/auth.php';
require_admin_auth();

$db = db();
$currentAdmin = current_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_user') {
        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $role = trim($_POST['role'] ?? 'admin');
        $password = (string)($_POST['password'] ?? '');

        if ($username === '') {
            set_flash('error', 'Username wajib diisi.');
        } else {
            if ($id > 0) {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("UPDATE admin_users SET username = ?, fullname = ?, role = ?, password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$username, $fullname, $role, $hash, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE admin_users SET username = ?, fullname = ?, role = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$username, $fullname, $role, $id]);
                }
                set_flash('success', 'Akun admin berhasil diperbarui.');
            } else {
                if ($password === '') {
                    set_flash('error', 'Password wajib diisi untuk pengguna baru.');
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("INSERT INTO admin_users (username, password, fullname, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$username, $hash, $fullname, $role]);
                    set_flash('success', 'Akun admin baru berhasil dibuat.');
                }
            }
        }
        header('Location: users.php');
        exit;
    }

    if ($action === 'delete_user') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$currentAdmin['id']) {
            set_flash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        } elseif ($id > 0) {
            $stmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Akun admin berhasil dihapus.');
        }
        header('Location: users.php');
        exit;
    }
}

$pageKey = 'users';
$pageTitle = 'Manajemen Admin';
$pageSubtitle = 'Kelola akun administrator untuk akses Panel CMS';

require_once __DIR__ . '/inc/header.php';

$admins = [];
try {
    $admins = $db->query("SELECT id, username, fullname, role, created_at, updated_at FROM admin_users ORDER BY id ASC")->fetchAll();
} catch (Throwable $e) {}
?>

<div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-white">Daftar Akun Administrator</h2>
            <p class="text-xs text-slate-400">Kredensial juga dapat diedit secara manual langsung melalui database MySQL/phpMyAdmin</p>
        </div>
        <button type="button" onclick="openUserModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold flex items-center gap-1.5 shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-base">person_add</span>
            <span>Tambah Admin</span>
        </button>
    </div>

    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-[#1e2d3d] text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-[#101822]">
                    <th class="py-3.5 px-4">Nama Lengkap</th>
                    <th class="py-3.5 px-4">Username</th>
                    <th class="py-3.5 px-4">Role / Peran</th>
                    <th class="py-3.5 px-4">Dibuat Pada</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#182330]">
                <?php foreach ($admins as $a): ?>
                <tr class="hover:bg-[#162331]/50">
                    <td class="py-3 px-4 font-bold text-white flex items-center gap-2.5">
                        <div class="size-8 rounded-full bg-gradient-to-tr from-primary to-emerald-500 flex items-center justify-center text-xs font-bold text-white uppercase">
                            <?= substr($a['username'], 0, 2) ?>
                        </div>
                        <span><?= htmlspecialchars($a['fullname']) ?></span>
                        <?php if ((int)$a['id'] === (int)$currentAdmin['id']): ?>
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Anda</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 font-mono text-slate-300"><?= htmlspecialchars($a['username']) ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#192738] text-blue-300 uppercase">
                            <?= htmlspecialchars($a['role']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                        <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick='editUser(<?= json_encode($a) ?>)' class="size-7 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-colors" title="Edit Akun">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <?php if ((int)$a['id'] !== (int)$currentAdmin['id']): ?>
                                <form method="POST" action="users.php" onsubmit="return confirm('Hapus akun admin ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <button type="submit" class="size-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="userModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-[#121c27] border border-[#1e2d3d] rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-[#1e2d3d]">
            <h3 id="userModalTitle" class="text-sm font-bold text-white">Tambah Administrator</h3>
            <button type="button" onclick="closeUserModal()" class="text-slate-400 hover:text-white">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <form method="POST" action="users.php" class="space-y-3.5">
            <input type="hidden" name="action" value="save_user">
            <input type="hidden" id="user_id" name="id" value="0">

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Lengkap</label>
                <input type="text" id="user_fullname" name="fullname" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Username</label>
                <input type="text" id="user_username" name="username" required class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Role / Peran</label>
                <select id="user_role" name="role" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                    <option value="admin">Administrator</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="operator">Operator</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password</label>
                <input type="password" id="user_password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full h-10 px-3 rounded-xl bg-[#0b1017] border border-[#1f2d3d] text-white text-xs focus:outline-none focus:border-primary">
                <p class="text-[10px] text-slate-500 mt-1">Disimpan menggunakan PHP password_hash (Bcrypt).</p>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-[#1e2d3d]">
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold">Simpan Admin</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUserModal() {
        document.getElementById('userModalTitle').innerText = 'Tambah Administrator';
        document.getElementById('user_id').value = '0';
        document.getElementById('user_fullname').value = '';
        document.getElementById('user_username').value = '';
        document.getElementById('user_role').value = 'admin';
        document.getElementById('user_password').value = '';
        document.getElementById('user_password').required = true;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(data) {
        document.getElementById('userModalTitle').innerText = 'Edit Administrator';
        document.getElementById('user_id').value = data.id;
        document.getElementById('user_fullname').value = data.fullname || '';
        document.getElementById('user_username').value = data.username;
        document.getElementById('user_role').value = data.role || 'admin';
        document.getElementById('user_password').value = '';
        document.getElementById('user_password').required = false;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
