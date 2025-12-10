<?php
session_start();
require_once 'config.php';

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        // Cek ke database
        $stmt = $conn->prepare("SELECT id, username, password, nama, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Cek password — dukung hash (password_verify) dan fallback ke plaintext
            $stored = $user['password'];
            $login_ok = false;

            if (is_string($stored) && preg_match('/^\$2[aby]\$|^\$argon2/i', $stored)) {
                if (password_verify($password, $stored)) {
                    $login_ok = true;
                }
            } else {
                // Fallback: password disimpan plaintext di DB (instalasi lama)
                if ($password === $stored) {
                    $login_ok = true;
                    // Re-hash password secara aman dan simpan kembali
                    $rehash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upd->bind_param("si", $rehash, $user['id']);
                    $upd->execute();
                    $upd->close();
                }
            }

            if ($login_ok) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: halaman-admin/index.php");
                    exit();
                } elseif ($user['role'] === 'superadmin') {
                    header("Location: super-admin/superadmin_dashboard.php");
                    exit();
                } else {
                    header("Location: halaman-teknisi/index.php");
                    exit();
                }
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FixTrack</title>
    
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1e3a8a',
                        primaryHover: '#172554',
                        accent: '#3b82f6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white min-h-screen flex overflow-hidden">

    <!-- Bagian Kiri (Gambar) -->
    <div class="hidden lg:block lg:w-1/2 relative">
        <img src="assets/photos/foto_komponen.jpeg" alt="Komponen Elektronik" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-primary/40 mix-blend-multiply"></div>
        <div class="absolute bottom-0 left-0 p-12 text-white z-10">
            <h2 class="text-4xl font-bold mb-4">FixTrack System</h2>
            <p class="text-lg text-blue-100 max-w-md">Solusi terpercaya untuk manajemen servis elektronik Anda. Pantau status perbaikan secara real-time.</p>
        </div>
    </div>

    <!-- Bagian Kanan (Form) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 relative">
        
        <!-- Hiasan Background -->
        <div class="absolute top-0 right-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse"></div>
        </div>

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-10 border border-slate-100">
            
            <!-- Logo -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-primary mb-2 tracking-tight">Selamat Datang</h1>
                <p class="text-slate-500 font-medium">Silakan login untuk melanjutkan</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="" method="POST" class="space-y-6">
                
                <!-- Input Username -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-slate-400 group-focus-within:text-primary transition-colors duration-300"></i>
                    </div>
                    <input type="text" id="username" name="username" 
                        class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-300 font-medium" 
                        placeholder="Username" autocomplete="off" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <!-- Input Password -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-400 group-focus-within:text-primary transition-colors duration-300"></i>
                    </div>
                    <input type="password" id="password" name="password" 
                        class="block w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-300 font-medium" 
                        placeholder="Password">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer">
                        <i class="fas fa-eye text-slate-400 hover:text-primary transition-colors duration-300" id="togglePassword"></i>
                    </div>
                </div>

                <!-- Tombol Masuk -->
                <button type="submit" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 flex justify-center items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>

                <!-- Link Tambahan -->
                <div class="flex flex-col items-center space-y-4 mt-6 pt-6 border-t border-slate-100">
                    <a href="#" class="text-slate-500 hover:text-primary font-medium transition-colors text-sm">
                        Lupa password? <span class="text-primary font-semibold">Hubungi Admin</span>
                    </a>
                    <a href="index.php" class="flex items-center gap-2 text-slate-500 hover:text-primary font-medium transition-colors text-sm group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Copyright (HP) -->
        <div class="absolute bottom-4 text-center w-full lg:hidden">
            <p class="text-slate-400 text-xs">&copy; 2025 FixTrack System.</p>
        </div>
    </div>

    <!-- Script JS -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
