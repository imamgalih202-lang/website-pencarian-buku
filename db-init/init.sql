-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Jun 2026 pada 10.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `websiteskripsi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_posts`
--

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id_post` int(11) NOT NULL AUTO_INCREMENT,
  `judul_post` varchar(255) NOT NULL,
  `isi_post` text NOT NULL,
  `tanggal_publikasi` date NOT NULL,
  `gambar_post` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `blog_posts`
--

INSERT INTO `blog_posts` (`id_post`, `judul_post`, `isi_post`, `tanggal_publikasi`, `gambar_post`) VALUES
(1, 'Pentingnya Membaca di Era Digital', 'Di era yang serba cepat ini, kemampuan literasi justru semakin dibutuhkan. Membaca bukan lagi sekadar mengeja kata, melainkan memahami konteks, menyaring informasi hoaks, dan memperluas wawasan.\n\nPerpustakaan sekolah kini hadir lebih dekat melalui genggaman Anda. Manfaatkan fitur pencarian cerdas yang telah disediakan untuk menemukan buku yang sesuai dengan minat Anda.', '2026-05-01', 'default_blog.jpg'),
(2, 'Tips Sukses Menyusun Skripsi IT', 'Menyusun laporan skripsi atau tugas akhir membutuhkan dedikasi. Hal pertama yang harus dilakukan adalah menentukan metodologi yang tepat, seperti Waterfall, dan membuat rancangan diagram DFD atau ERD yang solid.\n\nJangan lupa untuk selalu melakukan backup pada database dan kode program Anda secara rutin!', '2026-05-05', 'default_blog.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode_buku` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `rak` varchar(50) DEFAULT NULL,
  `description` text NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode_buku` (`barcode_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `books`
--

INSERT INTO `books` (`id`, `barcode_buku`, `title`, `author`, `category`, `rak`, `description`, `cover`) VALUES
(1, 'BK001', 'Pemrograman Web PHP untuk Pemula', 'Budi Raharjo', 'Teknologi', 'Rak TIK 1', 'Buku panduan lengkap belajar PHP dari dasar hingga mahir untuk membuat website dinamis dan interaktif.', 'default.jpg'),
(2, 'BK002', 'Mastering PHP dan MySQL', 'Budi Raharjo', 'Teknologi', 'Rak TIK 1', 'Buku lanjutan tingkat mahir untuk membuat aplikasi web kompleks menggunakan PHP native dan database MySQL secara terstruktur.', 'default.jpg'),
(4, 'BK004', 'Sang Pemimpi', 'Andrea Hirata', 'Novel Fiksi', 'Rak Sastra A', 'Lanjutan kisah Laskar Pelangi, menceritakan masa SMA Ikal dan Arai yang merantau untuk mengejar mimpi ke Eropa.', '1778571211_Sang_Pemimpi_sampul.jpg'),
(6, 'BK006', 'Laskar Pelangi', 'Andrea Hirata', 'Novel Fiksi', 'Rak Sastra A', 'kisah inspiratif tentang perjuangan 10 anak dari keluarga miskin di Belitung pada 1970-200-an untuk mendapatkan pendidikan di sekolah Muhammadiyah yang penuh keterbatasan', '1778570043_Laskar_pelangi_sampul.jpg'),
(7, 'BK007', 'Pengantar machine learning teori dan praktik', ' Rifkie Primartha', 'Pendidikan', 'Rak TIK 1', 'Buku ini membahas konsep dasar mechine learning dan algoritma machin learning yang populer, pada setiap bab yang terkait dengan algoritma machine learning telah disertakan conroh latihan menggunakan R aplikasi R merupakan aplikasi statistika yang akhir-akhir ini semakin populer Buku ini sengaja dibuat berbagai kalangan mulai kalangan umum, mahasiwa, dosen atau kalangan lain yang tertarik untuk belajar machine learning', '1778570847_Buku_Pengantar_Machine_Learning.jpg'),
(8, 'BK008', '	 Machine learning : tingkat dasar dan lanjut', 'Suyanto', 'Pendidikan', 'Rak TIK 2', 'Perusahaan raksasa berbasis IT telah menunjukkkan berbagai kesuksesan dalam penggunaan artificial intelligence khususnya machine learning kesuksesan mereka tentu saja tidak instan dalam sekejap, mereka telah bertahun-tahun mermbangun sistem itu menggunakan berbagai terknik machine learning mulai dari teknik tingkat dasar yang simple lalu secara saat ini telah menggunakan teknik machine learning tingkat lanjut khusunya deep learning yang memberikan performasi tinggi Buku ini memberi gambaran holistik dan simple mengenai konsep dasar machine learning teknik-teknik dan metode tingkat dasar dan tingkat lanjut serta aplikasinya dalam bidang terkini', '1778571011_Machine_Learning.jpg'),
(9, 'BK009', 'Teknik Pengelasan', 'Suhermanto', 'Pendidikan', 'Rak teknik 1', 'gas acetylena yang kemudian dikenal sebagai las karbit Waktu itu sudah dikembangkan las listrik namun masih mulai langka. Penulis viii Teknik Pengelasangas acetylena yang kemudian dikenal sebagai las karbit. Waktu itu sudah dikembangkan las listrik namun masih mulai langka. Penulis viii Teknik Pengelasan', '1778571473_Teknik_Pengelasan.jpeg'),
(10, 'BK2026010', 'solo leveling', 'chugon', 'Novel', 'Rak Novel 1', 'Sung Jin-woo, seorang pemburu (Hunter) kelas E berkekuatan sangat lemah yang mendapatkan kemampuan misterius untuk meningkatkan kekuatannya tanpa batas setelah berhasil bertahan hidup dari insiden mematikan di dalam gerbang bawah tanah (Double Dungeon). ', '1778851240_solo_leveling.jpeg'),
(11, 'BK2026011', 'Ranah 3 Warna', 'Ahmad Fuadi', 'Novel', 'novel 1', 'Ranah 3 Warna adalah novel kedua karya Ahmad Fuadi yang diterbitkan oleh Gramedia pada tahun 2009. Novel ini merupakan kedua dari trilogi Negeri 5 Menara bercerita tentang Alif yang baru selesai menamatkan sekolah di Pondok Madani (PM) Ponorogo Jawa Timur dan perjalanannya mewujudkan mimpi menjadi Habibie di Teknologi Tinggi Bandung, lalu merantau untuk menggapai jendela dunia sampai ke Amerika.', '1781360489_Sampul_buku_Ranah_3_Warna.jpg'),
(12, 'BK2026012', 'Atlas Indonesia Dan Dunia', 'TIM Kartografi BIP', 'Pendidikan', 'sosial 2', 'Atlas Indonesia & Dunia berisi informasi terkini tentang Indonesia dan dunia, membuatnya sempurna untuk siswa sekolah dasar (SD), menengah pertama (SMP), dan menengah atas (SMA). Buku ini juga sangat membantu untuk pelajaran Geografi dan Ilmu Pengetahuan Sosial (IPS), karena menyediakan berbagai informasi penting dan menarik yang akan memperkaya pengetahuan siswa.', '1781360701_Atlas_Dunia.jpg'),
(13, 'BK2026013', 'Sejarah X', 'Sari OKtafiana', 'Pendidikan', 'sosial 2', 'Buku Sejarah Kelas X dirancang untuk membangun kesadaran berpikir kritis dan historis siswa melalui kajian manusia, ruang, dan waktu. Materi utamanya berfokus pada konsep dasar ilmu sejarah serta perkembangan peradaban dan masyarakat Indonesia dari masa praaksara hingga masuknya pengaruh agama Hindu-Buddha', '1781361330_sejarah X.jpeg'),
(14, 'BK2026014', 'Sejarah XI', 'Martina Dkk', 'Pendidikan', 'sosial 2', 'Buku Sejarah Kelas XI SMK (Kurikulum Merdeka) membekali siswa dengan pemahaman kritis mengenai dimensi ruang-waktu perjalanan sejarah Indonesia dan pengaruhnya secara global. Buku ini mengasah karakter profil pelajar Pancasila dan keterampilan abad ke-21 melalui asesmen serta proyek kolaboratif', '1781361544_SEJARAH XI.jpeg'),
(15, 'BK2026015', 'Koding dan Kecerdasan Artifisial (KA) X', 'Rudy Setiawan', 'Pendidikan', 'TIK 1', 'Mata pelajaran Koding dan Kecerdasan Artifisial (KA) merupakan program dari Kemendikbudristek untuk membekali siswa SMK Kelas X dengan literasi digital, berpikir komputasional, algoritma pemrograman, hingga penggunaan AI generatif. Fokusnya adalah mencetak lulusan yang tidak hanya pengguna, melainkan inovator teknologi', '1781365189_koding KA.jpeg'),
(16, 'BK2026016', 'Koding dan Kecerdasan Artifisial (KA) XI', 'Rudy Setiawan', 'Pendidikan', 'Rak TIK 2', 'Mata pelajaran Koding dan Kecerdasan Artifisial (KA) merupakan program dari Kementerian Pendidikan Dasar dan Menengah yang dirancang untuk membekali siswa dengan literasi digital dan berpikir komputasional. Untuk siswa SMK kelas XI, materi berfokus pada dasar-dasar machine learning, algoritma pemrograman tingkat lanjut, serta integrasi etika digital.', '1781365305_KA (XI).jpeg'),
(17, 'BK2026017', 'Teknik Pemesinan Bubut', 'RatnaPutra', 'Pendidikan', 'mesin 1', 'Teknik pemesinan bubut adalah proses manufaktur subtraktif yang membentuk material dengan cara memutar benda kerja dan menyayatnya menggunakan alat potong (pahat). Teknik ini berfungsi utama untuk menghasilkan komponen presisi berbentuk silindris, tirus, maupun ulir.', '1781365646_TEKNIK MESIN BUBUT.jpeg'),
(18, 'BK2026018', 'Teknik Sepeda Motor Elemen Perawatan dan Perbaikan Sasis Sepeda Motor XI', 'Heri Majid Dkk', 'Pendidikan', 'mesin 2', 'Mata pelajaran ini membekali peserta didik dengan keterampilan teknis operasional dalam merawat dan memperbaiki komponen penopang serta pengendali sepeda motor. Melalui kombinasi teori dan praktik berbasis Prosedur Operasional Standar (SOP), siswa belajar mendiagnosis gejala kerusakan, membongkar, mengukur, serta merakit kembali komponen sasis. Fokus utama pembelajaran diarahkan pada penguasaan kompetensi sistem mekanis dan hidrolis, seperti sistem pengereman, peredam kejut, geometri kemudi, serta keselarasan roda dan rangka. Dengan mengutamakan keselamatan dan kesehatan kerja (K3), lulusan mata pelajaran ini dipersiapkan menjadi teknisi yang mampu menjamin performa kendaraan tetap stabil, responsif, dan aman digunakan dalam berbagai kondisi jalan.', '1781365979_sepeda motor.jpeg'),
(19, 'BK2026019', 'Teknik Sepeda Motor Elemen Perawatan dan Perbaikan Sasis Sepeda Motor XII', 'airlangga putra', 'Pendidikan', 'mesin 1', 'Mata pelajaran ini membekali peserta didik kelas XII Teknik Sepeda Motor dengan keahlian profesional dalam merawat, mendiagnosis gejala kerusakan, dan memperbaiki seluruh sistem sasis kendaraan. Pembelajaran dirancang untuk mentransformasi siswa dari tingkat dasar menuju pemahaman tingkat lanjut yang berorientasi pada standar kerja industri bengkel resmi.', '1781366234_sepeda motor xii.jpeg'),
(20, 'BK2026020', 'Teknik Pengelasan Elemen Gambar Teknik Fase F', 'Agus N', 'pendidikan', 'teknik 1', 'Elemen Gambar Teknik pada mata pelajaran Teknik Pengelasan Fase F (kelas XI dan XII SMK) berfokus pada kemampuan siswa merancang dan menerjemahkan desain fabrikasi. Ruang lingkup utamanya meliputi pembuatan gambar kerja, gambar bentangan, desain 2D/3D menggunakan CAD, serta penerapan standar simbol las internasional', '1781370028_teknik pengelasan.jpeg'),
(21, 'BK2026021', 'Ilmu Pengetahuan Sosial Sejarah X', 'slamet riyandi', 'Pendidikan', 'sosial 2', 'Mata pelajaran Ilmu Pengetahuan Sosial (IPS) Sejarah Kelas X (Fase E) mempelajari hakikat sejarah sebagai ilmu, manusia, ruang, dan waktu. Kurikulum ini berfokus pada sumber daya riset dasar, penelitian, dan akar pemikiran keindonesiaan serta peristiwa penting pembentuk peradaban.', '1781370312_ips sejarag X.jpeg'),
(22, 'BK2026022', 'Pendidikan Pancasila X', 'Istiana nen  Arienti', 'Pendidikan', 'bahasa 11', 'Pendidikan Pancasila Kelas X berfokus pada pembentukan karakter pelajar agar menjadi warga negara yang beriman, bertakwa kepada Tuhan Yang Maha Esa, berakhlak mulia, dan memiliki kesadaran berbangsa dan bernegara berdasarkan nilai-nilai Pancasila', '1781370815_pancasila.jpeg'),
(23, 'BK2026023', 'Pendidikan Pancasila XI', 'Istiana nen  Arienti', 'Pendidikan', 'bahasa 11', 'Buku Pendidikan Pancasila Kelas XI  disusun untuk membentuk pelajar berkarakter kuat, berakhlak mulia, dan memahami nilai-nilai luhur ideologi negara. Mata pelajaran ini membekali siswa dengan pemahaman kritis mengenai penerapan Pancasila dalam kehidupan berbangsa, bernegara, dan bermasyarakat di era modern.', '1781370984_pancasila xii.jpeg'),
(24, 'BK2026024', 'Pendidikan Pancasila XII', 'Istiana nen  Arienti', 'Pendidikan', 'bahasa 11', 'Mata pelajaran Pendidikan Pancasila kelas XII berfokus pada pembentukan karakter peserta didik agar menjadi warga negara yang berakhlak mulia, bernalar kritis, dan taat hukum. Materi utamanya mengkaji hak dan kewajiban, menangani ancaman terhadap ideologi, serta penerapan nilai-nilai Pancasila dalam kehidupan bermasyarakat dan global', '1781371092_pancasila xiii.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konfigurasi`
--

CREATE TABLE IF NOT EXISTS `konfigurasi` (
  `id` int(1) NOT NULL DEFAULT 1,
  `batas_hari_pinjam` int(3) NOT NULL DEFAULT 7,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `konfigurasi`
--

INSERT INTO `konfigurasi` (`id`, `batas_hari_pinjam`) VALUES
(1, 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_buku` int(11) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Dipinjam',
  PRIMARY KEY (`id`),
  KEY `id_buku` (`id_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `id_buku`, `nama_peminjam`, `kelas`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(2, 1, 'Siti Aminah', 'XI TKJ 2', '2026-05-10', '2026-05-17', 'Dikembalikan'),
(4, 8, 'Yahyo noerden', '10 TKJ 2', '2026-05-12', '2026-05-19', 'Dikembalikan'),
(5, 8, 'Yahyo noerden', '10 TKJ 2', '2026-05-13', '2026-05-20', 'Dipinjam'),
(6, 2, 'imam', '10 tkj 3', '2026-05-15', '2026-05-22', 'Dipinjam'),
(7, 10, 'imam gp', 'XI TKJ 2', '2026-05-24', '2026-05-31', 'Dikembalikan'),
(8, 10, 'imam gp', 'XI TKJ 2', '2026-05-24', '2026-05-31', 'Dikembalikan'),
(9, 10, 'imam gp', 'XI TKJ 2', '2026-05-24', '2026-05-31', 'Dikembalikan'),
(14, 9, 'imam gp', 'XI TKJ 2', '2026-05-24', '2026-05-31', 'Dikembalikan'),
(15, 9, 'imam gp', 'XI TKJ 2', '2026-06-02', '2026-06-09', 'Dikembalikan'),
(16, 9, 'imam gp', 'XI TKJ 2', '2026-06-03', '2026-06-10', 'Dikembalikan'),
(17, 9, 'imam gp', 'XI TKJ 2', '2026-06-03', '2026-06-10', 'Dikembalikan'),
(18, 9, 'imam gp', 'XI TKJ 2', '2026-06-03', '2026-06-10', 'Dikembalikan'),
(19, 9, 'imam gp', 'XI TKJ 2', '2026-06-03', '2026-06-03', 'Dikembalikan'),
(20, 9, 'imam gp', 'XI TKJ 2', '2026-06-03', '2026-06-03', 'Dikembalikan'),
(21, 9, 'imam gp', 'XI TKJ 2', '2026-06-08', '2026-06-08', 'Dikembalikan'),
(22, 9, 'imam gp', 'XI TKJ 2', '2026-06-08', '2026-06-08', 'Dikembalikan'),
(23, 9, 'imam gp', 'XI TKJ 2', '2026-06-08', '2026-06-15', 'Dikembalikan'),
(24, 9, 'imam gp', 'XI TKJ 2', '2026-06-08', '2026-06-08', 'Dikembalikan'),
(26, 10, 'imam GP', 'XI TKJ 2', '2026-06-08', '2026-06-11', 'Dikembalikan'),
(27, 9, 'imam GP', 'XI TKJ 2', '2026-06-08', '2026-06-11', 'Dikembalikan'),
(28, 10, 'imam GP', 'XI TKJ 2', '2026-06-08', '2026-06-11', 'Dipinjam'),
(29, 9, 'imam GP', 'XI TKJ 2', '2026-06-08', '2026-06-08', 'Dikembalikan'),
(30, 9, 'imam GP', 'XI TKJ 2', '2026-06-09', '2026-06-12', 'Dipinjam'),
(31, 9, 'reyhan', 'XI TP 1', '2026-06-09', '2026-06-12', 'Dipinjam');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pencarian`
--

CREATE TABLE IF NOT EXISTS `pencarian` (
  `Id_Cari` int(100) NOT NULL AUTO_INCREMENT,
  `Kata_Kunci` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_Cari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE IF NOT EXISTS `pengembalian` (
  `Id_Buku` int(10) NOT NULL,
  `Nama_Siswa` varchar(50) NOT NULL,
  `Barcode_Buku` varchar(255) NOT NULL,
  `Barcode_Siswa` varchar(255) NOT NULL,
  `Tgl_Kembali` varchar(200) NOT NULL,
  `Denda` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_Buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengunjung`
--

CREATE TABLE IF NOT EXISTS `pengunjung` (
  `id_kunjung` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(50) DEFAULT NULL,
  `nama_pengunjung` varchar(100) DEFAULT NULL,
  `tgl_kunjung` datetime NOT NULL,
  PRIMARY KEY (`id_kunjung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengunjung`
--

INSERT INTO `pengunjung` (`id_kunjung`, `nis`, `nama_pengunjung`, `tgl_kunjung`) VALUES
(1, 'KTS0898', 'imam GP', '2026-06-09 00:55:39'),
(2, 'KTS0030001', 'reyhan', '2026-06-09 01:16:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE IF NOT EXISTS `siswa` (
  `id_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `barcode_siswa` varchar(50) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `kelas` varchar(50) NOT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `barcode_siswa` (`barcode_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `barcode_siswa`, `nama_siswa`, `nis`, `kelas`, `Foto`) VALUES
(1, 'KTS0030001', 'reyhan', '0002', 'XI TP 1', ''),
(2, 'KTS002', 'Siti Aminah', NULL, 'XI TKJ 2', NULL),
(3, 'KTS003', 'Andi Prasetyo', NULL, 'XII RPL 1', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'password123', 'admin');

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_buku_pinjam` FOREIGN KEY (`id_buku`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
