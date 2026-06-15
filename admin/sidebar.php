<?php
// Mendapatkan nama file yang sedang dibuka (misal: books.php atau index.php)
// Ini berfungsi agar menu di sidebar bisa menyala otomatis (active)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* CSS Global untuk Admin (dipanggil di semua halaman yang pakai sidebar) */
    body { 
        background-color: #f4f6f9; 
        font-family: 'Poppins', sans-serif; 
        overflow-x: hidden; 
    }
    
    /* --- STYLING UNTUK SIDEBAR --- */
    .sidebar {
        height: 100vh; 
        width: 250px;
        position: fixed; 
        top: 0;
        left: 0;
        background-color: #1a1a1a;
        padding-top: 20px;
        z-index: 1000;
        display: flex;
        flex-direction: column; /* Agar isi sidebar berbaris ke bawah */
    }
    .sidebar-brand {
        color: #ffc107; 
        font-weight: 700;
        font-size: 1.2rem;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }
    .sidebar .nav {
        flex-grow: 1; /* Mendorong bagian logout ke paling bawah */
    }
    .sidebar .nav-link {
        color: #adb5bd;
        font-weight: 500;
        padding: 12px 20px;
        margin: 4px 15px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    .sidebar .nav-link.active {
        background-color: #B84E32; /* Warna Terakota andalan kita */
        color: #fff;
        box-shadow: 0 4px 6px rgba(184, 78, 50, 0.3);
    }
    .sidebar .nav-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }
    .logout-btn {
        padding: 20px 15px;
        margin-top: auto; /* Memastikan posisi selalu di bawah */
    }

    /* --- STYLING UNTUK KONTEN UTAMA (KANAN) --- */
    .main-content {
        margin-left: 250px; /* Jarak selebar sidebar agar konten tidak tertutup */
        padding: 30px;
        min-height: 100vh;
    }
</style>

<div class="sidebar shadow">
    <a href="index.php" class="sidebar-brand">
        <i class="bi bi-shield-lock-fill fs-4 d-block mb-1"></i>
        PANEL ADMIN
    </a>
    
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="pengunjung.php" class="nav-link <?= ($current_page == 'pengunjung.php') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Daftar Pengunjung
            </a>
        </li>

        <li class="nav-item">
            <a href="books.php" class="nav-link <?= ($current_page == 'books.php') ? 'active' : '' ?>">
                <i class="bi bi-book-fill"></i> Katalog Buku
            </a>
        </li>
        <li class="nav-item">
            <a href="peminjaman.php" class="nav-link <?= ($current_page == 'peminjaman.php') ? 'active' : '' ?>">
                <i class="bi bi-arrow-up-right-square-fill"></i> Peminjaman
            </a>
        </li>
        <li class="nav-item">
            <a href="pengembalian.php" class="nav-link <?= ($current_page == 'pengembalian.php') ? 'active' : '' ?>">
                <i class="bi bi-arrow-down-left-square-fill"></i> Pengembalian
            </a>
        </li>
        <li class="nav-item">
            <a href="laporan.php" class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
            </a>
        </li>
    </ul>

    <div class="logout-btn">
        <a href="../auth/logout.php" class="btn btn-danger w-100 fw-bold shadow-sm" onclick="return confirm('Yakin ingin keluar dari panel admin?')">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</div>