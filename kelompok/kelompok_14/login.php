<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FixTrack</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Background Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="glass-card">
            <div class="brand-logo">
                <h1>FixTrack</h1>
                <p>Sistem Informasi Tracking Servis</p>
            </div>

            <form id="loginForm" action="" method="POST">
                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off">
                    </div>
                    <div class="invalid-feedback">Username tidak boleh kosong</div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    <div class="invalid-feedback">Password tidak boleh kosong</div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>

                <div class="form-footer">
                    <p>Lupa password? <a href="#">Hubungi Admin</a></p>
                    <p style="margin-top: 0.5rem;"><a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 (Optional for future use) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="assets/js/login.js"></script>
</body>
</html>
