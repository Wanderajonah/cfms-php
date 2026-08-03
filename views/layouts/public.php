<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Security::e($title ?? 'Cafe Javas') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Playfair+Display:wght@400;700;900&family=Rubik:wght@700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/app.css?v=20260804-stargold" rel="stylesheet">
</head>
<body>

<div id="preloader">
    <div class="preloader-inner">
        <img src="/assets/uploads/restaurant/logo-white.png" alt="Cafe Javas" class="preloader-logo">
        <div class="preloader-spinner"></div>
    </div>
</div>

<div id="wrapper">
    <header class="header_style_wrapper">
        <div class="top_bar <?= $isHome ?? false ? 'hasbg' : '' ?>">
            <div class="top_bar_container">
                <div class="menu_buttons_container">
                    <div class="menu_buttons_content">
                        <div id="menu_wrapper">
                            <div class="nav_wrapper_inner">
                                <ul class="nav nav-left">
                                    <li><a href="/" class="<?= basename($_SERVER['REQUEST_URI']) === '' || basename($_SERVER['REQUEST_URI']) === '/' ? 'current' : '' ?>">Home</a></li>
                                    <li><a href="/our-menus">Our Menus</a></li>
                                    <li><a href="/about">About</a></li>
                                </ul>
                                <div class="logo_container">
                                    <div class="logo_align">
                                        <div class="logo_wrapper">
                                            <a href="/" aria-label="Cafe Javas home">
                                                <img class="logo-white" src="/assets/uploads/restaurant/logo-white.png" alt="Cafe Javas">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-right">
                                    <li><a href="/order-online">Order Online</a></li>
                                    <li><a href="/feedback/track">Track</a></li>
                                    <li><a href="/feedback/submit">Give Feedback</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="mobile_logo">
                            <a href="/" aria-label="Cafe Javas home">
                                <img class="logo-white" src="/assets/uploads/restaurant/logo-white.png" alt="Cafe Javas">
                            </a>
                        </div>
                        <div id="mobile_nav_icon"><span></span></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer class="footer_bar">
        <div id="footer">
            <div class="standard_wrapper">
                <ul class="sidebar_widget four">
                    <li>
                        <h2 class="widgettitle">About Cafe Javas</h2>
                        <p>Cafe Javas serves generous meals, fresh coffee, desserts, and warm hospitality across Kampala. Your feedback helps every visit get better.</p>
                        <div class="social-icons">
                            <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </li>
                    <li>
                        <h2 class="widgettitle">Our Menu</h2>
                        <ul>
                            <li><a href="/our-menus">Signature Plates</a></li>
                            <li><a href="/our-menus">Coffee</a></li>
                            <li><a href="/our-menus">Fresh Juices</a></li>
                            <li><a href="/our-menus">Desserts</a></li>
                        </ul>
                    </li>
                    <li>
                        <h2 class="widgettitle">Quick Links</h2>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="/about">About Us</a></li>
                            <li><a href="/feedback/submit">Give Feedback</a></li>
                            <li><a href="/feedback/track">Track Ticket</a></li>
                        </ul>
                    </li>
                    <li>
                        <h2 class="widgettitle">Get In Touch</h2>
                        <ul class="address">
                            <li><i class="bi bi-geo-alt"></i> Kampala Road, Uganda</li>
                            <li><i class="bi bi-telephone"></i> +256 (0) 76-890-214</li>
                            <li><i class="bi bi-envelope"></i> feedback@cafejavas.com</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer_bar_wrapper">
            <div id="copyright">&copy; 2026 Cafe Javas. All rights reserved.</div>
            <div class="footer_links">
                <a href="/about">About</a>
                <a href="/our-menus">Menus</a>
                <a href="/order-online">Order Online</a>
                <a href="/feedback/submit">Feedback</a>
            </div>
            <div class="footer_social">
                <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            </div>
        </div>
    </footer>
</div>

<a id="toTop" href="#"><i class="bi bi-chevron-up"></i></a>

<?php require __DIR__ . '/../partials/toasts.php'; ?>
<?php require __DIR__ . '/../partials/thanks_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
<script>
(function () {
    const modal = document.getElementById('thanksModal');
    if (!modal) return;
    document.body.classList.add('thanks-modal-open');
    function close() {
        modal.classList.add('thanks-modal-hidden');
        document.body.classList.remove('thanks-modal-open');
    }
    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });
    modal.querySelectorAll('[data-thanks-close]').forEach(function (btn) {
        btn.addEventListener('click', close);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
})();
</script>
</body>
</html>
