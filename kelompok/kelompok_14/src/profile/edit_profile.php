<?php
session_start();
include '../config.php';

// Dukung kedua nama session key untuk kompatibilitas
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id'] ?? $_SESSION['id_user']);

// Ambil data user
$stmt = $conn->prepare("SELECT id, username, nama, role, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows <= 0) {
    header("Location: ../login.php");
    exit();
}
$user = $res->fetch_assoc();
$stmt->close();

$errors = [];
$success = [];

// Handle Edit Profile (nama + username)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');

    if ($nama === '') $errors['profile'] = 'Nama lengkap wajib diisi.';
    if ($username === '') $errors['profile'] = 'Username wajib diisi.';

    if (!isset($errors['profile'])) {
        $upd = $conn->prepare("UPDATE users SET nama = ?, username = ? WHERE id = ?");
        $upd->bind_param("ssi", $nama, $username, $user_id);
        if ($upd->execute()) {
            $success['profile'] = 'Profil berhasil diperbarui.';
            $user['nama'] = $nama;
            $user['username'] = $username;
        } else {
            $errors['profile'] = 'Gagal menyimpan perubahan.';
        }
        $upd->close();
    }
}

// Handle Upload Foto (hanya simpan file)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_foto') {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = basename($_FILES['foto']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_ext)) {
            $errors['foto'] = 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.';
        } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) { // Max 5MB
            $errors['foto'] = 'Ukuran file terlalu besar (max 5MB).';
        } else {
            // Simpan file dengan nama unik
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
            $upload_path = '../assets/photos/' . $new_filename;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update database
                $upd = $conn->prepare("UPDATE users SET foto = ? WHERE id = ?");
                $upd->bind_param("si", $new_filename, $user_id);
                if ($upd->execute()) {
                    $success['foto'] = 'Foto profil berhasil diperbarui.';
                    $user['foto'] = $new_filename;
                } else {
                    $errors['foto'] = 'Gagal menyimpan foto ke database.';
                }
                $upd->close();
            } else {
                $errors['foto'] = 'Gagal mengunggah file.';
            }
        }
    } else {
        $errors['foto'] = 'Tidak ada file yang dipilih atau terjadi kesalahan upload.';
    }
}

// Handle Ganti Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($old_password === '') $errors['password'] = 'Password lama wajib diisi.';
    if ($new_password === '') $errors['password'] = 'Password baru wajib diisi.';
    if ($confirm_password === '') $errors['password'] = 'Konfirmasi password wajib diisi.';

    if (!isset($errors['password'])) {
        // Ambil password (bisa berupa hash atau, pada instalasi lama, plaintext)
        $stmt_pw = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pw->bind_param("i", $user_id);
        $stmt_pw->execute();
        $res_pw = $stmt_pw->get_result();
        $row_pw = $res_pw->fetch_assoc();
        $stmt_pw->close();

        if (!$row_pw) {
            $errors['password'] = 'Terjadi kesalahan: pengguna tidak ditemukan.';
        } else {
            $stored = $row_pw['password'];

            $old_matches = false;

            // Jika nilai tersimpan tampak seperti hash (bcrypt/argon), gunakan password_verify
            if (is_string($stored) && preg_match('/^\$2[aby]\$|^\$argon2/i', $stored)) {
                if (password_verify($old_password, $stored)) {
                    $old_matches = true;
                }
            } else {
                // Fallback: kemungkinan instalasi lama menyimpan password plaintext
                if ($old_password === $stored) {
                    $old_matches = true;
                }
            }

            if (!$old_matches) {
                $errors['password'] = 'Password lama tidak sesuai.';
            } elseif ($new_password !== $confirm_password) {
                $errors['password'] = 'Password baru tidak cocok dengan konfirmasi.';
            } elseif (strlen($new_password) < 6) {
                $errors['password'] = 'Password baru minimal 6 karakter.';
            } else {
                // Update password (simpan dalam bentuk hash)
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->bind_param("si", $hashed, $user_id);
                if ($upd->execute()) {
                    $success['password'] = 'Password berhasil diubah.';
                } else {
                    $errors['password'] = 'Gagal mengubah password.';
                }
                $upd->close();
            }
        }
    }
}

// Tentukan link dashboard berdasarkan role
if (isset($user['role']) && $user['role'] === 'admin') {
    $dashboard_link = '../halaman-admin/index.php';
} else {
    $dashboard_link = '../halaman-teknisi/index.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profil - FixTrack</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen font-sans">

    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="<?= $dashboard_link; ?>" class="flex items-center gap-3">
                        <img src="../assets/photos/logo.png" alt="FixTrack" class="h-10 w-10 object-contain">
                        <span class="font-semibold text-lg text-slate-800">FixTrack</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-sm text-slate-600 hidden sm:block">Halo, <?= htmlspecialchars($user['nama']); ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 py-10">
        <!-- Page Title -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Pengaturan Profil</h2>
            <p class="text-slate-500 text-sm mt-1">Kelola data pribadi dan keamanan akun Anda</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- SIDEBAR: Foto Profil & Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 text-center">
                    <!-- Foto Profil -->
                    <div class="mb-4">
                <!-- Foto Profil -->
                    <div class="mb-4">
                        <div class="w-24 h-24 mx-auto rounded-full border-4 border-slate-200 bg-slate-100 flex items-center justify-center text-3xl font-bold text-blue-800 overflow-hidden">
                            <?php if (!empty($user['foto'])): ?>
                                <img src="../assets/photos/<?= htmlspecialchars($user['foto']); ?>" alt="Foto" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    </div>

                    <h3 class="font-semibold text-slate-800"><?= htmlspecialchars($user['nama']); ?></h3>
                    <p class="text-xs text-slate-500 mt-1 uppercase"><?= ucfirst($user['role']); ?></p>

                    <!-- Upload Foto Form -->
                    <form method="POST" enctype="multipart/form-data" class="mt-4 space-y-2" id="fotoForm">
                        <input type="hidden" name="action" value="upload_foto">
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/gif" class="hidden" id="foto-input">
                        <button type="button" onclick="document.getElementById('foto-input').click()" class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-camera mr-1"></i> Ubah Foto
                        </button>
                    </form>

                    <?php if (isset($errors['foto'])): ?>
                        <div class="mt-3 p-2 bg-red-50 text-red-700 text-xs rounded-lg"><?= htmlspecialchars($errors['foto']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($success['foto'])): ?>
                        <div class="mt-3 p-2 bg-green-50 text-green-700 text-xs rounded-lg"><?= htmlspecialchars($success['foto']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MAIN: Edit Profile & Change Password -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Section: Edit Profil -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">
                        <i class="fas fa-user text-blue-600 mr-2"></i> Edit Profil
                    </h3>

                    <?php if (isset($errors['profile'])): ?>
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm rounded"><?= htmlspecialchars($errors['profile']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($success['profile'])): ?>
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-700 text-sm rounded"><?= htmlspecialchars($success['profile']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="edit_profile">

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Section: Ganti Password -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">
                        <i class="fas fa-lock text-red-600 mr-2"></i> Ganti Password
                    </h3>

                    <?php if (isset($errors['password'])): ?>
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm rounded"><?= htmlspecialchars($errors['password']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($success['password'])): ?>
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-700 text-sm rounded"><?= htmlspecialchars($success['password']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="change_password">

                        <!-- Password Lama -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password Lama</label>
                            <div class="relative">
                                <input type="password" name="old_password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" id="old_pass" required>
                                <button type="button" onclick="togglePassword('old_pass')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye toggle-icon" id="toggle-old_pass"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="new_password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" id="new_pass" required>
                                <button type="button" onclick="togglePassword('new_pass')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye toggle-icon" id="toggle-new_pass"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" id="confirm_pass" required>
                                <button type="button" onclick="togglePassword('confirm_pass')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye toggle-icon" id="toggle-confirm_pass"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-key mr-2"></i> Ubah Password
                        </button>
                    </form>
                </div>

            </div>

        </div>

        <!-- Back Button -->
        <div class="mt-8 flex justify-end">
            <a href="profile.php" class="text-slate-600 hover:text-blue-600 font-medium flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Profil
            </a>
        </div>

    </div>

    <!-- Modal Crop Foto -->
    <div id="cropModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Atur Foto Profil</h3>
            
            <!-- Preview Foto untuk Crop -->
            <div class="mb-4">
                <img id="imageToCrop" class="w-full max-h-96 object-contain">
            </div>

            <!-- Kontrol Crop -->
            <div class="flex gap-2 mb-4 flex-wrap">
                <button type="button" id="zoomInBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-search-plus mr-1"></i> Zoom In
                </button>
                <button type="button" id="zoomOutBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-search-minus mr-1"></i> Zoom Out
                </button>
                <button type="button" id="rotateLeftBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-redo mr-1"></i> Putar Kiri
                </button>
                <button type="button" id="rotateRightBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-undo mr-1"></i> Putar Kanan
                </button>
                <button type="button" id="flipHBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-arrows-alt-h mr-1"></i> Flip Horizontal
                </button>
                <button type="button" id="flipVBtn" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    <i class="fas fa-arrows-alt-v mr-1"></i> Flip Vertical
                </button>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex gap-2 justify-end">
                <button type="button" id="cancelCropBtn" class="px-4 py-2 bg-slate-300 text-slate-800 rounded hover:bg-slate-400">Batal</button>
                <button type="button" id="saveCropBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan Foto</button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('toggle-' + fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        let cropper = null;
        let originalImageSrc = null;

        // Buka modal crop saat file dipilih
        document.getElementById('foto-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    originalImageSrc = event.target.result;
                    const img = document.getElementById('imageToCrop');
                    img.src = originalImageSrc;
                    
                    // Destroy cropper lama jika ada
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    // Inisialisasi cropper baru
                    cropper = new Cropper(img, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        restore: true,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: true,
                    });
                    
                    // Tampilkan modal
                    document.getElementById('cropModal').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Kontrol Zoom
        document.getElementById('zoomInBtn').addEventListener('click', () => cropper.zoom(0.1));
        document.getElementById('zoomOutBtn').addEventListener('click', () => cropper.zoom(-0.1));

        // Kontrol Rotasi
        document.getElementById('rotateLeftBtn').addEventListener('click', () => cropper.rotate(-45));
        document.getElementById('rotateRightBtn').addEventListener('click', () => cropper.rotate(45));

        // Kontrol Flip
        document.getElementById('flipHBtn').addEventListener('click', () => cropper.scaleX(-cropper.getData().scaleX || -1));
        document.getElementById('flipVBtn').addEventListener('click', () => cropper.scaleY(-cropper.getData().scaleY || -1));

        // Batal crop
        document.getElementById('cancelCropBtn').addEventListener('click', () => {
            document.getElementById('cropModal').classList.add('hidden');
            document.getElementById('foto-input').value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Simpan crop dan upload
        document.getElementById('saveCropBtn').addEventListener('click', () => {
            const canvas = cropper.getCroppedCanvas({
                maxWidth: 4096,
                maxHeight: 4096,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            canvas.toBlob((blob) => {
                const dataTransfer = new DataTransfer();
                const file = new File([blob], 'profile_foto.jpg', { type: 'image/jpeg' });
                dataTransfer.items.add(file);
                document.getElementById('foto-input').files = dataTransfer.files;
                
                // Submit form
                document.getElementById('fotoForm').submit();
            }, 'image/jpeg', 0.9);

            document.getElementById('cropModal').classList.add('hidden');
        });
    </script>

</body>
</html>