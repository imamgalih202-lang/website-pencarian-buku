/* ==============================================================
   INTERACTIVE SCRIPT
   PROJECT: PERPUSTAKAAN DIGITAL SMKN 1 KISMANTORO
   ============================================================== */

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Efek Bayangan pada Navbar saat Scroll
    const navbar = document.querySelector('.navbar-custom');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. Cegah Form Pencarian Kosong Disubmit
    const searchForms = document.querySelectorAll('form[action*="pencarian.php"]');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const input = this.querySelector('input[type="search"]');
            if (input && input.value.trim() === '') {
                e.preventDefault(); // Hentikan proses submit
                alert('Silakan ketik kata kunci pencarian terlebih dahulu!');
                input.focus();
            }
        });
    });

});