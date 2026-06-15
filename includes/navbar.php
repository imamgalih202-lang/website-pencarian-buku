<nav class="navbar navbar-expand-lg fixed-top navbar-custom py-3">
  <div class="container">
    
    <a class="navbar-brand d-flex align-items-center fs-4" href="<?= BASE_URL ?>index.php">
        <div class="bg-orange-soft p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style="background-color: rgba(184, 78, 50, 0.1); width: 40px; height: 40px;">
            <i class="bi bi-book-half" style="color: #B84E32;"></i>
        </div>
        <span class="fw-bold">Perpus<span style="color: #B84E32;">Digital</span></span>
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPerpus" aria-controls="navbarPerpus" aria-expanded="false" aria-label="Toggle navigation">
      <i class="bi bi-list fs-1 text-dark"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarPerpus">
        
      <form class="d-flex mx-auto my-3 my-lg-0 position-relative w-100" style="max-width: 500px;" action="<?= BASE_URL ?>pages/pencarian.php" method="GET">
        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
        <input class="form-control search-bar ps-5 shadow-sm" type="search" name="q" placeholder="Cari judul buku atau nama penulis..." aria-label="Search" required 
               style="border-radius: 50rem; background-color: #f4f1ea; border: 2px solid transparent; padding-top: 0.6rem; padding-bottom: 0.6rem;">
      </form>

      <ul class="navbar-nav align-items-lg-center gap-2">
        <li class="nav-item">
          <a class="nav-link fw-semibold text-center px-3" href="<?= BASE_URL ?>index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold text-center px-3" href="#">Katalog</a>
        </li>
        
        <li class="nav-item d-none d-lg-block mx-2">
            <span class="text-muted opacity-25">|</span>
        </li>

        <li class="nav-item mt-3 mt-lg-0 text-center">
          <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-orange shadow-sm px-4 py-2 fw-bold rounded-pill">
              <i class="bi bi-shield-lock-fill me-1"></i> Login Admin
          </a>
        </li>
      </ul>

    </div>
  </div>
</nav>

<script>
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar-custom');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled', 'shadow-sm');
        } else {
            navbar.classList.remove('scrolled', 'shadow-sm');
        }
    });
</script>