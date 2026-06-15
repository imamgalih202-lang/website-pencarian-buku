<?php
// Logika untuk mengambil urutan terakhir di database
$query_last = $mysqli->query("SELECT MAX(id) as last_id FROM books");
$row_last = $query_last->fetch_assoc();
$next_id = ($row_last['last_id'] ?? 0) + 1;

// Format Barcode: BK + Tahun + ID Unik (Contoh: BK2026001)
$auto_barcode = "BK" . date('Y') . str_pad($next_id, 3, "0", STR_PAD_LEFT);
?>

<div class="mb-3">
    <label class="fw-bold small mb-2">BARCODE BUKU (OTOMATIS)</label>
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-dark text-white"><i class="bi bi-upc"></i></span>
        <input type="text" name="barcode_buku" class="form-control bg-light fw-bold" 
               value="<?= $auto_barcode ?>" readonly style="letter-spacing: 2px; color: #B84E32;">
    </div>
    <small class="text-muted">Barcode ini dihasilkan otomatis oleh sistem untuk efisiensi.</small>
</div>