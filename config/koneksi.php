<?php
// ==========================================
// PENGATURAN DATABASE (Mendukung Docker/Environment Variables)
// ==========================================
$host     = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
$database = getenv('DB_NAME') ?: "websiteskripsi";

// Membuat koneksi menggunakan MySQLi (Object-Oriented)
$mysqli = new mysqli($host, $username, $password, $database);

// Mengecek apakah koneksi berhasil atau gagal
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Mengatur charset utf8mb4 agar mendukung karakter khusus/emoji dengan aman
$mysqli->set_charset("utf8mb4");


// ==========================================
// PENGATURAN BASE_URL (ALAMAT ABSOLUT)
// ==========================================
if (!defined('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/websiteskripsi/');
}
?>