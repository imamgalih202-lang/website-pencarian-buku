<footer class="footer-complex mt-auto">
  <div class="container">
    <div class="row g-4 g-lg-5">
      
      <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
        <h5 class="text-white mb-3">
            <i class="bi bi-book-half me-2" style="color: var(--primary-terra);"></i>
            Perpus SMKN 1
        </h5>
        <p class="text-muted small mb-4" style="line-height: 1.8;">
            Membangun generasi cerdas melalui literasi digital. Temukan, pinjam, dan baca buku referensi terbaik dengan mudah dan cepat.
        </p>
        <div class="social-icons d-flex">
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
        <h5>Jelajahi</h5>
        <ul class="list-unstyled d-flex flex-column gap-2 mt-3">
          <li><a href="#"><i class="bi bi-chevron-right small me-1 opacity-50"></i> Beranda</a></li>
          <li><a href="#"><i class="bi bi-chevron-right small me-1 opacity-50"></i> Katalog Buku</a></li>
          <li><a href="#"><i class="bi bi-chevron-right small me-1 opacity-50"></i> Tentang Kami</a></li>
          <li><a href="#"><i class="bi bi-chevron-right small me-1 opacity-50"></i> Tata Tertib</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5>Layanan Bantuan</h5>
        <div class="mt-3">
            <div class="d-flex align-items-start mb-3">
                <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 text-white">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h6 class="text-white mb-1" style="font-size: 0.9rem;">Jam Operasional</h6>
                    <p class="small text-muted mb-0">Senin - Jum'at<br>08.00 - 15.00 WIB</p>
                </div>
            </div>
            <div class="d-flex align-items-start">
                <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3 text-white">
                    <i class="bi bi-headset"></i>
                </div>
                <div>
                    <h6 class="text-white mb-1" style="font-size: 0.9rem;">Live Chat Admin</h6>
                    <a href="auth/login.php" class="small text-decoration-none" style="color: var(--primary-terra);"><i class="bi bi-shield-lock me-1"></i> Login Petugas</a>
                </div>
            </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5>Berlangganan</h5>
        <p class="small text-muted mb-3">Dapatkan info buku terbaru langsung di emailmu.</p>
        <form class="d-flex flex-column gap-2 mt-3">
          <div class="input-group">
            <span class="input-group-text footer-input border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control footer-input border-start-0 ps-0 shadow-none" placeholder="Alamat Email..." required>
          </div>
          <button class="btn btn-submit-footer w-100 py-2 mt-2 shadow-sm" type="button">
              <i class="bi bi-send me-1"></i> Subscribe
          </button>
        </form>
      </div>

    </div>

    <div class="text-center mt-5 pt-4 border-top border-secondary border-opacity-25 text-muted" style="font-size: 0.85rem;">
      &copy; <?= date('Y') ?> Perpustakaan Digital SMKN 1 Kismantoro. <br class="d-md-none">
      Dirancang oleh <span class="text-white fw-semibold">Imam Galih Prayitno</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/script.js?v=<?= time(); ?>"></script>

</body>
</html>