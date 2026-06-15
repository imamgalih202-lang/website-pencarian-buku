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
    $env_base_url = getenv('BASE_URL');
    if ($env_base_url && strpos($env_base_url, 'localhost') === false) {
        define('BASE_URL', $env_base_url);
    } else {
        // Deteksi otomatis protokol dan host
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Jika di dalam Docker dan env BASE_URL diset ke localhost:3500, gunakan itu sebagai fallback
        // tapi prioritaskan host dari browser (IP VPS)
        if ($host === 'localhost' || $host === '127.0.0.1') {
            define('BASE_URL', $env_base_url ?: $protocol . $host . '/websiteskripsi/');
        } else {
            define('BASE_URL', $protocol . $host . '/');
        }
    }
}
?>