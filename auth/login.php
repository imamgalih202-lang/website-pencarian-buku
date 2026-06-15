<?php
session_start();
require_once '../config/koneksi.php';

// Jika admin sudah login, cegah mereka membuka halaman login lagi 
// dan langsung arahkan ke dashboard admin
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === 'admin') {
    header("Location: ../admin/index.php");
    exit;
}

$error = '';

// Proses ketika tombol login ditekan
if (isset($_POST['login'])) {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cek kecocokan username dan password di tabel users
    $query = $mysqli->query("SELECT * FROM users WHERE username='$username' AND password='$password' AND role='admin'");

    if ($query->num_rows > 0) {
        $data = $query->fetch_assoc();
        
        // Buat Sesi (Session) sebagai tanda pengenal bahwa admin berhasil login
        $_SESSION['status_login'] = 'admin';
        $_SESSION['username'] = $data['username'];
        
        // Arahkan ke halaman Admin
        header("Location: ../admin/index.php");
        exit;
    } else {
        $error = 'Username atau Password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F9F6F0; /* Krem terang */
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            color: #B84E32;
        }
        .btn-orange {
            background-color: #B84E32;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px;
        }
        .btn-orange:hover {
            background-color: #9a3f26;
            color: white;
        }
        .form-control:focus {
            border-color: #B84E32;
            box-shadow: 0 0 0 0.2rem rgba(184, 78, 50, 0.15);
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-shield-lock-fill"></i>
            <h4 class="fw-bold mt-2 mb-0">Panel Admin</h4>
            <p class="text-muted small mb-0">Perpustakaan SMKN 1 Kismantoro</p>
        </div>
        
        <div class="p-4">
            <?php if($error != ''): ?>
                <div class="alert alert-danger py-2 small text-center fw-bold">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'akses_ditolak'): ?>
                <div class="alert alert-warning py-2 small text-center fw-bold">
                    <i class="bi bi-lock-fill"></i> Anda harus login terlebih dahulu!
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'logout'): ?>
                <div class="alert alert-success py-2 small text-center fw-bold">
                    <i class="bi bi-check-circle-fill"></i> Anda berhasil keluar sistem.
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn btn-orange w-100 mb-3">MASUK <i class="bi bi-box-arrow-in-right"></i></button>
                
                <div class="text-center">
                    <a href="../index.php" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>