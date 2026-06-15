<?php
require_once 'cek_sesi.php';
require_once '../config/koneksi.php';

// Jika kosong, otomatis mengarah ke id=1
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: cetak_kartu.php?id=1");
    exit;
}

$id_siswa = (int)$_GET['id'];

// ========================================================
// PROSES SIMPAN PERUBAHAN DATA KE DATABASE (POST REQUEST)
// ========================================================
$pesan_sukses = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_database'])) {
    $nama_siswa = $mysqli->real_escape_string($_POST['nama_siswa']);
    $kelas = $mysqli->real_escape_string($_POST['kelas']);
    $nis_input = $mysqli->real_escape_string($_POST['nis']); 
    $barcode_siswa = $mysqli->real_escape_string($_POST['barcode_siswa']);
    
    // Ambil data lama untuk mengecek foto lama
    $siswa_lama = $mysqli->query("SELECT foto FROM siswa WHERE id_siswa = $id_siswa")->fetch_assoc();
    $nama_foto_baru = $siswa_lama['foto'] ?? '';

    // Logika upload file foto baru jika ada file yang dimasukkan
    if (!empty($_FILES['foto_upload']['name'])) {
        $target_dir = "../assets/img/siswa/";
        $nama_foto_baru = time() . '_' . basename($_FILES['foto_upload']['name']);
        
        if (!is_dir($target_dir)) { 
            mkdir($target_dir, 0777, true); 
        }

        if (!empty($siswa_lama['foto']) && file_exists($target_dir . $siswa_lama['foto'])) {
            unlink($target_dir . $siswa_lama['foto']);
        }

        move_uploaded_file($_FILES['foto_upload']['tmp_name'], $target_dir . $nama_foto_baru);
    }

    // Eksekusi query update data ke tabel siswa
    $query_update = "UPDATE siswa SET 
                        nama_siswa = '$nama_siswa', 
                        kelas = '$kelas', 
                        nis = '$nis_input', 
                        barcode_siswa = '$barcode_siswa', 
                        foto = '$nama_foto_baru' 
                     WHERE id_siswa = $id_siswa";
    
    if ($mysqli->query($query_update)) {
        $pesan_sukses = true;
    }
}

// Ambil data terbaru dari database untuk ditampilkan di halaman
$query = $mysqli->query("SELECT * FROM siswa WHERE id_siswa = $id_siswa LIMIT 1");
if ($query->num_rows == 0) {
    die("Siswa tidak ditemukan!");
}

$s = $query->fetch_assoc();

// ========================================================
// REKOMENDASI KODE BARU: GENERATOR KODE UNIK OTOMATIS BERIKUTNYA
// ========================================================
$prefix_kartu = "KTS003";
$nomor_increment = 1;
$rekomendasi_barcode_baru = "";

while (empty($rekomendasi_barcode_baru)) {
    $format_digit = str_pad($nomor_increment, 4, "0", STR_PAD_LEFT);
    $kode_uji_baru = $prefix_kartu . $format_digit; // Menghasilkan KTS0030001, KTS0030002, dst

    // Cek apakah kode ini sudah pernah terpakai di database?
    $cek_terpakai = $mysqli->query("SELECT id_siswa FROM siswa WHERE barcode_siswa = '$kode_uji_baru' LIMIT 1");

    if ($cek_terpakai && $cek_terpakai->num_rows > 0) {
        $nomor_increment++; // Jika sudah ada yang pakai, naikkan angka urutan
    } else {
        $rekomendasi_barcode_baru = $kode_uji_baru; // Jika kosong, jadikan ini rekomendasi kode baru
    }
}

// Penentu nilai input utama di formulir
$barcode_otomatis = !empty($s['barcode_siswa']) ? $s['barcode_siswa'] : $rekomendasi_barcode_baru;

// Cek foto siswa terbaru
$foto_siswa = !empty($s['foto']) ? '../assets/img/siswa/' . $s['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($s['nama_siswa']) . '&background=B84E32&color=fff&size=150&bold=true';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generator & Update KTS - SMKN 1 Kismantoro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container-generator { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); max-width: 950px; width: 100%; }
        .form-control-kustom { border-radius: 10px; padding: 10px 15px; border: 1px solid #e2e8f0; background-color: #f8f9fa; }
        .form-control-kustom:focus { background-color: #fff; border-color: #B84E32; box-shadow: 0 0 0 0.25rem rgba(184, 78, 50, 0.15); }
        .kartu-kts { width: 85.6mm; height: 53.98mm; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; border-radius: 4mm; padding: 4mm; box-sizing: border-box; position: relative; overflow: hidden; border: 1px solid #333; text-align: left; }
        .kartu-kts::before { content: ''; position: absolute; top: 0; right: 0; width: 38mm; height: 100%; background: #B84E32; transform: skewX(-20deg) translateX(10mm); z-index: 1; opacity: 0.85; }
        .header-kartu { position: relative; z-index: 2; border-bottom: 0.5mm solid rgba(255,255,255,0.2); padding-bottom: 1.5mm; margin-bottom: 3.5mm; }
        .header-kartu h1 { font-size: 3.2mm; margin: 0; font-weight: 700; color: #ffc107; letter-spacing: 0.5px; }
        .header-kartu p { font-size: 1.8mm; margin: 0; color: #ccc; text-transform: uppercase; }
        .konten-kartu { display: flex; position: relative; z-index: 2; height: calc(100% - 10mm); }
        .box-foto { width: 19mm; height: 25mm; border: 0.5mm solid #fff; border-radius: 1.5mm; overflow: hidden; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .box-foto img { width: 100%; height: 100%; object-fit: cover; }
        .box-biodata { flex: 1; padding-left: 4mm; display: flex; flex-direction: column; justify-content: center; }
        .bio-row { margin-bottom: 1.2mm; }
        .bio-label { font-size: 1.5mm; color: #aaa; text-transform: uppercase; font-weight: 500; }
        .bio-value { font-size: 2.3mm; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 35mm; }
        .box-barcode { position: absolute; bottom: 2mm; right: 2mm; z-index: 3; background: white; padding: 1.5mm 3mm 1mm 3mm; border-radius: 1mm; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .barcode-img { max-height: 7mm; max-width: 30mm; }
        .barcode-text { font-size: 1.6mm; color: #222; font-weight: 700; margin-top: 0.5mm; display: block; font-family: monospace; }
        .btn-download-kts { background-color: #0d6efd !important; color: white !important; font-weight: bold; border-radius: 10px; padding: 12px; border: none; }
        .btn-simpan-database { background-color: #B84E32 !important; color: white !important; font-weight: bold; border-radius: 10px; padding: 12px; border: none; }
        .btn-kembali-admin { background-color: #ffffff !important; color: #4a5568 !important; font-weight: bold; border-radius: 10px; padding: 12px; border: 1px solid #cbd5e1; }
    </style>
</head>
<body>

    <div class="container-generator">
        
        <?php if($pesan_sukses): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 mb-4 rounded-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> 
                <strong>Berhasil!</strong> Data siswa telah diperbarui di database. Kartu siap digunakan untuk scan sirkulasi.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="formKartuSiswa" class="row align-items-center g-5">
            
            <div class="col-lg-5">
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check me-2" style="color: #B84E32;"></i>Kelola & Simpan KTS</h4>
                <p class="text-muted small mb-4">Perubahan di bawah ini akan tersimpan permanen di database setelah tombol simpan ditekan.</p>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nama Siswa</label>
                    <input type="text" name="nama_siswa" id="input_nama" class="form-control form-control-kustom" value="<?= htmlspecialchars($s['nama_siswa']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Kelas / Jurusan</label>
                    <input type="text" name="kelas" id="input_kelas" class="form-control form-control-kustom" value="<?= htmlspecialchars($s['kelas']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nomor Induk Siswa (NIS)</label>
                    <input type="text" name="nis" id="input_nis" class="form-control form-control-kustom" value="<?= htmlspecialchars($s['nis'] ?? '') ?>" required placeholder="Masukkan nomor induk siswa...">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Upload Foto Baru</label>
                    <input type="file" name="foto_upload" id="upload_foto" class="form-control form-control-kustom" accept="image/*">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Barcode Kartu (Format KTS003)</label>
                    <div class="input-group">
                        <input type="text" name="barcode_siswa" id="input_barcode" class="form-control form-control-kustom fw-bold text-primary" value="<?= htmlspecialchars($barcode_otomatis) ?>" readonly required>
                        <button class="btn btn-dark fw-bold px-3 shadow-sm text-warning border-0" type="button" id="btn_kode_baru" data-rekomendasi="<?= $rekomendasi_barcode_baru ?>" title="Ambil Kode Unik Kosong Berikutnya">
                            <i class="bi bi-arrow-clockwise me-1"></i> Baru
                        </button>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <button type="submit" name="simpan_database" class="btn btn-simpan-database w-100 shadow-sm">
                            <i class="bi bi-save me-2"></i>Simpan Ke DB
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" id="btnDownloadKTS" class="btn btn-download-kts w-100 shadow-sm">
                            <i class="bi bi-download me-2"></i>Unduh Kartu
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="index.php" class="btn btn-kembali-admin w-100 shadow-sm d-flex align-items-center justify-content-center text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 d-flex flex-column align-items-center justify-content-center">
                <div style="background: #f8f9fa; padding: 25px; border-radius: 20px; border: 1px dashed #e2e8f0;">
                    
                    <div id="areaKartu" class="kartu-kts">
                        <div class="header-kartu">
                            <h1>PERPUSTAKAAN DIGITAL</h1>
                            <p>SMK Negeri 1 Kismantoro</p>
                        </div>
                        
                        <div class="konten-kartu">
                            <div class="box-foto">
                                <img id="view_foto" src="<?= $foto_siswa ?>" alt="Foto Siswa">
                            </div>
                            
                            <div class="box-biodata">
                                <div class="bio-row">
                                    <div class="bio-label">Nama Siswa</div>
                                    <div id="view_nama" class="bio-value"><?= htmlspecialchars($s['nama_siswa']) ?></div>
                                </div>
                                <div class="bio-row">
                                    <div class="bio-label">NIS / ID SISWA</div>
                                    <div id="view_nis" class="bio-value"><?= htmlspecialchars($s['nis'] ?? '') ?></div>
                                </div>
                                <div class="bio-row">
                                    <div class="bio-label">Kelas / Jurusan</div>
                                    <div id="view_kelas" class="bio-value"><?= htmlspecialchars($s['kelas']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="box-barcode">
                            <img id="view_barcode_img" class="barcode-img" src="https://bwipjs-api.metafloor.com/?bcid=code128&text=<?= urlencode($barcode_otomatis) ?>&scale=2&rotate=N&crossOrigin=anonymous" alt="Barcode">
                            <span id="view_barcode_text" class="barcode-text"><?= htmlspecialchars($barcode_otomatis) ?></span>
                        </div>
                    </div>
                    
                </div>
            </div>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // 1. EVENT DETEKSI LIVE INPUT PREVIEW
        $('#input_nama').on('input', function() { $('#view_nama').text($(this).val()); });
        $('#input_kelas').on('input', function() { $('#view_kelas').text($(this).val()); });
        $('#input_nis').on('input', function() { $('#view_nis').text($(this).val()); }); 
        
        // 🔥 JAVASCRIPT FITUR BARU: Mengubah live preview barcode ketika tombol 'Baru' ditekan
        $('#btn_kode_baru').on('click', function() {
            let rekomendasiKode = $(this).data('rekomendasi');
            $('#input_barcode').val(rekomendasiKode);
            $('#view_barcode_text').text(rekomendasiKode);
            
            // Ubah gambar barcode di dalam kartu secara real-time
            let urlApiBarcode = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" + encodeURIComponent(rekomendasiKode) + "&scale=2&rotate=N&crossOrigin=anonymous";
            $('#view_barcode_img').attr('src', urlApiBarcode);
        });

        // Baca file gambar lokal instan sebelum diunggah ke server
        $('#upload_foto').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#view_foto').attr('src', event.target.result);
                };
                reader.readAsDataURL(file);
            }
        });
        
        // 2. PROSES DOWNLOAD GAMBAR KARTU SEBAGAI BERKAS PNG
        $('#btnDownloadKTS').on('click', function() {
            const areaKartu = document.getElementById('areaKartu');
            const namaSiswa = $('#input_nama').val().trim().replace(/\s+/g, '_');
            const namaFile = "KTS_" + (namaSiswa !== "" ? namaSiswa : "Siswa") + ".png";

            html2canvas(areaKartu, {
                useCORS: true, 
                scale: 4, 
                backgroundColor: null
            }).then(function(canvas) {
                let link = document.createElement('a');
                link.download = namaFile;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });
    });
    </script>
</body>
</html>