<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - SMKN 1 Kismantoro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time(); ?>">
</head>

<body class="d-flex flex-column h-100" style="padding-top: 100px; background-color: #FDFBF7;">

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom py-3">
      <div class="container">
        
        <a class="navbar-brand d-flex align-items-center fs-4" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-book-half me-2"></i> 
            <span>Perpus<span class="text-dark">Digital</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPerpus" aria-controls="navbarPerpus" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list fs-1 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarPerpus">
            
          <form class="d-flex mx-auto my-3 my-lg-0 position-relative w-100" style="max-width: 500px;" action="<?= BASE_URL ?>pages/pencarian.php" method="GET">
            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
            <input class="form-control search-bar ps-5 shadow-sm" type="search" name="q" placeholder="Cari judul buku atau penulis..." aria-label="Search" required>
          </form>

          <ul class="navbar-nav align-items-lg-center gap-2">
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="<?= BASE_URL ?>index.php">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="#">Katalog</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="#">Kategori</a>
            </li>
            
            <li class="nav-item d-none d-lg-block mx-2">
                <span class="text-muted opacity-25">|</span>
            </li>

            <li class="nav-item mt-3 mt-lg-0 text-center">
              <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-orange shadow-sm px-4 py-2 fw-bold">
                  <i class="bi bi-shield-lock me-1"></i> Login Admin
              </a>
            </li>
          </ul>

        </div>
      </div>
    </nav><!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - SMKN 1 Kismantoro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time(); ?>">
</head>

<body class="d-flex flex-column h-100" style="padding-top: 100px; background-color: #FDFBF7;">

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom py-3">
      <div class="container">
        
        <a class="navbar-brand d-flex align-items-center fs-4" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-book-half me-2"></i> 
            <span>Perpus<span class="text-dark">Digital</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPerpus" aria-controls="navbarPerpus" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list fs-1 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarPerpus">
            
          <form class="d-flex mx-auto my-3 my-lg-0 position-relative w-100" style="max-width: 500px;" action="<?= BASE_URL ?>pages/pencarian.php" method="GET">
            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
            <input class="form-control search-bar ps-5 shadow-sm" type="search" name="q" placeholder="Cari judul buku atau penulis..." aria-label="Search" required>
          </form>

          <ul class="navbar-nav align-items-lg-center gap-2">
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="<?= BASE_URL ?>index.php">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="#">Katalog</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold text-center" href="#">Kategori</a>
            </li>
            
            <li class="nav-item d-none d-lg-block mx-2">
                <span class="text-muted opacity-25">|</span>
            </li>

            <li class="nav-item mt-3 mt-lg-0 text-center">
              <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-orange shadow-sm px-4 py-2 fw-bold">
                  <i class="bi bi-shield-lock me-1"></i> Login Admin
              </a>
            </li>
          </ul>

        </div>
      </div>
    </nav>