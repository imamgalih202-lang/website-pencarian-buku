<?php
/**
 * ========================================================================
 * ENGINE REKOMENDASI BUKU (CONTENT-BASED FILTERING)
 * Menggunakan Algoritma TF-IDF & Cosine Similarity
 * ========================================================================
 */

// 1. Fungsi Pembersihan Teks (Text Preprocessing)
// Menghilangkan tanda baca dan mengubah ke huruf kecil agar perhitungan kata akurat.
function bersihkanTeks($teks) {
    $teks = strtolower($teks); // Ubah ke huruf kecil
    $teks = preg_replace('/[^a-z0-9\s]/', '', $teks); // Buang karakter selain huruf & angka
    return $teks;
}

// 2. Fungsi Utama untuk Mendapatkan Rekomendasi
function getRekomendasiBuku($mysqli, $id_buku_aktif, $limit = 4) {
    // A. AMBIL SEMUA DATA BUKU DARI DATABASE
    $query = $mysqli->query("SELECT id, title, author, category, description, cover FROM books");
    $semua_buku = [];
    $dokumen_teks = [];
    
    while ($row = $query->fetch_assoc()) {
        $semua_buku[$row['id']] = $row;
        
        // Gabungkan elemen yang menentukan "isi/konten" buku menjadi satu kalimat panjang
        // Penulis dan Kategori diberi bobot lebih dengan mengulangnya (opsional tapi bagus untuk akurasi)
        $teks_gabungan = $row['category'] . " " . $row['category'] . " " . 
                         $row['author'] . " " . 
                         $row['description'];
                         
        $dokumen_teks[$row['id']] = bersihkanTeks($teks_gabungan);
    }

    // Jika buku kurang dari 2, tidak bisa dihitung kemiripannya
    if (count($semua_buku) < 2 || !isset($dokumen_teks[$id_buku_aktif])) {
        return [];
    }

    // B. MENGHITUNG TF (Term Frequency) & MENDATA SEMUA KATA UNIK
    $tf = [];
    $df = []; // Document Frequency (Berapa banyak dokumen yang mengandung kata X)
    $total_dokumen = count($dokumen_teks);

    foreach ($dokumen_teks as $id_buku => $teks) {
        $kata_kata = explode(" ", $teks);
        $tf[$id_buku] = [];
        $kata_unik_per_dokumen = [];

        foreach ($kata_kata as $kata) {
            $kata = trim($kata);
            if ($kata == "") continue;

            // Hitung frekuensi kemunculan kata dalam satu dokumen (TF)
            if (!isset($tf[$id_buku][$kata])) {
                $tf[$id_buku][$kata] = 0;
            }
            $tf[$id_buku][$kata]++;

            // Tandai kata ini muncul di dokumen ini untuk perhitungan DF
            if (!isset($kata_unik_per_dokumen[$kata])) {
                $kata_unik_per_dokumen[$kata] = true;
                
                if (!isset($df[$kata])) {
                    $df[$kata] = 0;
                }
                $df[$kata]++;
            }
        }
    }

    // C. MENGHITUNG IDF (Inverse Document Frequency)
    $idf = [];
    foreach ($df as $kata => $jumlah_dokumen_mengandung_kata) {
        // Rumus IDF: Logaritma (Total Dokumen / Jumlah Dokumen Mengandung Kata)
        $idf[$kata] = log10($total_dokumen / $jumlah_dokumen_mengandung_kata);
    }

    // D. MENGHITUNG BOBOT TF-IDF (TF * IDF) UNTUK SETIAP BUKU
    $tfidf = [];
    foreach ($tf as $id_buku => $data_kata) {
        $tfidf[$id_buku] = [];
        foreach ($data_kata as $kata => $nilai_tf) {
            $tfidf[$id_buku][$kata] = $nilai_tf * $idf[$kata];
        }
    }

    // E. MENGHITUNG COSINE SIMILARITY ANTARA BUKU AKTIF DENGAN BUKU LAIN
    $vektor_aktif = $tfidf[$id_buku_aktif];
    $skor_kemiripan = [];

    foreach ($tfidf as $id_buku_lain => $vektor_lain) {
        // Jangan bandingkan dengan dirinya sendiri
        if ($id_buku_lain == $id_buku_aktif) continue;

        $dot_product = 0;
        $panjang_vektor_aktif = 0;
        $panjang_vektor_lain = 0;

        // Kumpulkan semua kata unik dari kedua vektor
        $semua_kata_unik = array_unique(array_merge(array_keys($vektor_aktif), array_keys($vektor_lain)));

        foreach ($semua_kata_unik as $kata) {
            $bobot_aktif = isset($vektor_aktif[$kata]) ? $vektor_aktif[$kata] : 0;
            $bobot_lain = isset($vektor_lain[$kata]) ? $vektor_lain[$kata] : 0;

            // Rumus Pembilang (A . B)
            $dot_product += ($bobot_aktif * $bobot_lain);

            // Rumus Penyebut (Panjang Vektor)
            $panjang_vektor_aktif += pow($bobot_aktif, 2);
            $panjang_vektor_lain += pow($bobot_lain, 2);
        }

        $panjang_vektor_aktif = sqrt($panjang_vektor_aktif);
        $panjang_vektor_lain = sqrt($panjang_vektor_lain);
        $pembagi = $panjang_vektor_aktif * $panjang_vektor_lain;

        // Hasil Cosine Similarity (Nilai 0 sampai 1)
        if ($pembagi == 0) {
            $skor_kemiripan[$id_buku_lain] = 0;
        } else {
            $skor_kemiripan[$id_buku_lain] = $dot_product / $pembagi;
        }
    }

    // F. URUTKAN BERDASARKAN SKOR TERTINGGI (Paling Mirip)
    arsort($skor_kemiripan);

    // G. AMBIL BUKU REKOMENDASI SESUAI LIMIT
    $hasil_rekomendasi = [];
    $hitung = 0;
    foreach ($skor_kemiripan as $id_buku => $skor) {
        if ($hitung >= $limit) break;
        
        // Hanya masukkan yang memiliki sedikit kemiripan (skor > 0)
        if ($skor > 0) {
            $buku_rekomendasi = $semua_buku[$id_buku];
            $buku_rekomendasi['skor_kemiripan'] = $skor; // Simpan skor jika sewaktu-waktu ingin ditampilkan
            $hasil_rekomendasi[] = $buku_rekomendasi;
            $hitung++;
        }
    }

    return $hasil_rekomendasi;
}
?>