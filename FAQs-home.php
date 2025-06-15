<?php
require_once 'functions.php';
require_once 'functions_posts.php';

// Get current page from URL parameter, default to 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$posts_per_page = 4;

// Get category from URL parameter
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Get posts for current page
if ($category) {
    $posts = getPostsByTag($category);
    $total_posts = count($posts);
} else {
    $posts = getPaginatedPosts($current_page, $posts_per_page);
    $total_posts = getTotalPosts();
}
$total_pages = ceil($total_posts / $posts_per_page);

?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/logo2AEBConecta.png">
    <!-- Author Meta -->
    <meta name="author" content="colorlib">
    <!-- Meta Description -->
    <meta name="description" content="">
    <!-- Meta Keyword -->
    <meta name="keywords" content="">
    <!-- meta character set -->
    <meta charset="UTF-8">
    <!-- Site Title -->
    <title>FAQ's</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
    <!--
			CSS
			============================================= -->
    <link rel="stylesheet" href="css/linearicons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/extra.css">
</head>

<body>
    <header id="header" id="home">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
                <a class="navbar-brand d-flex align-items-center logo" href="index.php">
                    <img src="<?php echo $inicioData['LogoPrincipal']; ?>" alt="logo" class="me-2"
                        style="height: 40px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php foreach ($separadores as $separador): ?>
                        <?php if ($separador['separador'] === 'Ligações úteis'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $separador['separador']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                                <?php foreach ($ligacoesUteis as $ligacao): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $ligacao['link']; ?>" target="_blank">
                                        <?php echo $ligacao['texto']; ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <?php if ($separador['separador'] === 'Login'): ?>
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" style="cursor: pointer;">
                                    <?php echo $separador['separador']; ?>
                                </a>
                            <?php else: ?>
                                <a class="nav-link" href="<?php echo $separador['link']; ?>">
                                <?php echo $separador['separador']; ?>
                            </a>
                            <?php endif; ?>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header><!-- #header -->

    <!-- ------------------------------ start banner Area ------------------------------ -->
    <section class="banner-area relative blog-home-banner" id="home">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="about-content blog-header-content col-lg-12">
                    <h1 class="text-white">
                        FAQ'S
                    </h1>
                    <p class="text-white">
                        Acesse respostas para as dúvidas mais comuns sobre suporte técnico e o uso dos kits da escola
                        digital.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- End banner Area -->
    <!-- ------------------------------ Start Variantes FAQs Area ------------------------------ -->
    <section class="top-category-widget-area pt-90 pb-90 ">
        <div class="container">
            <div class="row">
                <?php 
						$variedadeFAQs = getVariedadeFAQs();
						foreach ($variedadeFAQs as $faq): 
						?>
                <div class="col-lg-3">
                    <div class="single-cat-widget">
                        <div class="content relative">
                            <div class="overlay overlay-bg"></div>
                            <a href="?category=<?php echo urlencode($faq['nome']); ?>">
                                <div class="thumb">
                                    <img class="content-image img-fluid d-block mx-auto"
                                        src="<?php echo $faq['imagem']; ?>" alt="">
                                </div>
                                <div class="content-details">
                                    <h4 class="content-title mx-auto text-uppercase"><?php echo $faq['nome']; ?></h4>
                                    <span></span>
                                    <p><?php echo $faq['texto']; ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- End top-category-widget Area -->

    <!-- ------------------------------ Start post-content Area ------------------------------ -->
    <section class="post-content-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 posts-list">
                    <?php if ($category): ?>
                    <div class="mb-4">
                        <h3>Posts da categoria: <?php echo htmlspecialchars($category); ?></h3>
                        <a href="FAQs-home.php" class="btn btn-outline-primary">Ver todos os posts</a>
                    </div>
                    <?php endif; ?>

                    <?php 
                    if (empty($posts)): 
                    ?>
                    <div class="alert alert-info">
                        Nenhum post encontrado para esta categoria.
                    </div>
                    <?php else: 
                        foreach ($posts as $post): 
                    ?>
                    <div class="single-post row">
                        <div class="col-lg-3 col-md-3 meta-details">
                            <ul class="tags">
                                <?php foreach ($post['tags'] as $tag): ?>
                                <li><a
                                        href="?tag=<?php echo urlencode($tag); ?>"><?php echo htmlspecialchars($tag); ?>,</a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="user-details row">
                                <p class="user-name col-lg-12 col-md-12 col-6">
                                    <a href="#"><?php echo htmlspecialchars($post['autor_nome'] ?? ''); ?></a>
                                    <span class="lnr lnr-user"></span>
                                </p>
                                <p class="date col-lg-12 col-md-12 col-6">
                                    <a href="#"><?php echo formatDate($post['data_criacao']); ?></a>
                                    <span class="lnr lnr-calendar-full"></span>
                                </p>
                                <p class="comments col-lg-12 col-md-12 col-6">
                                    <a href="#"><?php echo $post['num_comentarios']; ?> Comments</a>
                                    <span class="lnr lnr-bubble"></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9">
                            <div class="feature-img">
                                <img class="img-fluid" src="<?php echo htmlspecialchars($post['img_principal']); ?>"
                                    alt="">
                            </div>
                            <a class="posts-title" href="blog-single.php?id=<?php echo $post['id']; ?>">
                                <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                            </a>
                            <p class="excert">
                                <?php
                                    $texto = strip_tags($post['texto']); // Remove HTML
                                    $resumo = mb_substr($texto, 0, 250); // Limita a 250 caracteres
                                    if (mb_strlen($texto) > 250) {
                                        $resumo .= '...';
                                    }
                                    echo htmlspecialchars($resumo);
                                ?>
                            </p>
                            <a href="blog-single.php?id=<?php echo $post['id']; ?>" class="primary-btn">View More</a>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>

                    <?php if (!$category): ?>
                    <nav class="blog-pagination justify-content-center d-flex">
                        <ul class="pagination">
                            <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a href="?page=<?php echo $current_page - 1; ?>" class="page-link"
                                    aria-label="Previous">
                                    <span aria-hidden="true">
                                        <span class="lnr lnr-chevron-left"></span>
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a href="?page=<?php echo $current_page + 1; ?>" class="page-link" aria-label="Next">
                                    <span aria-hidden="true">
                                        <span class="lnr lnr-chevron-right"></span>
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 sidebar-widgets">
                    <div class="widget-wrap">
                        <div class="single-sidebar-widget search-widget">
                            <form class="search-form" action="#">
                                <input placeholder="Search Posts" name="search" type="text"
                                    onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Posts'">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <!--
                        <div class="single-sidebar-widget user-info-widget">
                            <img src="img/blog/user-info.png" alt="">
                            <a href="#">
                                <h4>Charlie Barber</h4>
                            </a>
                            <p>
                                Senior blog writer
                            </p>
                            <ul class="social-links">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-github"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                            </ul>
                            <p>
                                Boot camps have its supporters andit sdetractors. Some people do not understand why you
                                should have to spend money on boot camp when you can get. Boot camps have itssuppor ters
                                andits detractors.
                            </p>
                        </div>-->
                        <div class="single-sidebar-widget popular-post-widget">
                            <h4 class="popular-title">Popular Posts</h4>
                            <div class="popular-post-list">
                                <div class="single-post-list d-flex flex-row align-items-center">
                                    <div class="thumb">
                                        <img class="img-fluid" src="img/blog/pp1.jpg" alt="">
                                    </div>
                                    <div class="details">
                                        <a href="blog-single.html">
                                            <h6>Space The Final Frontier</h6>
                                        </a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="single-post-list d-flex flex-row align-items-center">
                                    <div class="thumb">
                                        <img class="img-fluid" src="img/blog/pp2.jpg" alt="">
                                    </div>
                                    <div class="details">
                                        <a href="blog-single.html">
                                            <h6>The Amazing Hubble</h6>
                                        </a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="single-post-list d-flex flex-row align-items-center">
                                    <div class="thumb">
                                        <img class="img-fluid" src="img/blog/pp3.jpg" alt="">
                                    </div>
                                    <div class="details">
                                        <a href="blog-single.html">
                                            <h6>Astronomy Or Astrology</h6>
                                        </a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="single-post-list d-flex flex-row align-items-center">
                                    <div class="thumb">
                                        <img class="img-fluid" src="img/blog/pp4.jpg" alt="">
                                    </div>
                                    <div class="details">
                                        <a href="blog-single.html">
                                            <h6>Asteroids telescope</h6>
                                        </a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-sidebar-widget ads-widget">
                            <a href="#"><img class="img-fluid" src="img/blog/ads-banner.jpg" alt=""></a>
                        </div>
                        <div class="single-sidebar-widget post-category-widget">
                            <h4 class="category-title">Post Catgories</h4>
                            <ul class="cat-list">
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Technology</p>
                                        <p>37</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Lifestyle</p>
                                        <p>24</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Fashion</p>
                                        <p>59</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Art</p>
                                        <p>29</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Food</p>
                                        <p>15</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Architecture</p>
                                        <p>09</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="d-flex justify-content-between">
                                        <p>Adventure</p>
                                        <p>44</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="single-sidebar-widget newsletter-widget">
                            <h4 class="newsletter-title">Newsletter</h4>
                            <p>
                                Here, I focus on a range of items and features that we use in life without
                                giving them a second thought.
                            </p>
                            <div class="form-group d-flex flex-row">
                                <div class="col-autos">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-envelope"
                                                    aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" id="inlineFormInputGroup"
                                            placeholder="Enter email" onfocus="this.placeholder = ''"
                                            onblur="this.placeholder = 'Enter email'">
                                    </div>
                                </div>
                                <a href="#" class="bbtns">Subcribe</a>
                            </div>
                            <p class="text-bottom">
                                You can unsubscribe at any time
                            </p>
                        </div>
                        <div class="single-sidebar-widget tag-cloud-widget">
                            <h4 class="tagcloud-title">Tag Clouds</h4>
                            <ul>
                                <?php 
										$tags = getAllTags();
										foreach ($tags as $tag): 
										?>
                                <li><a href="?tag=<?php echo $tag['id']; ?>"><?php echo $tag['nome']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End post-content Area -->

    <button onclick="topFunction()" id="backToTopBtn" title="Voltar ao topo">⬆</button>

    <!-- ------------------------------------------ start FOOTER Area ------------------------------------------ -->
    <footer class="footer-area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h4>Ligações úteis</h4>
                        <ul>
                            <?php foreach (getFooterLinks('LigacoesUteis') as $link): ?>
                            <li><a href="<?php echo $link['link']; ?>"><?php echo $link['nome']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h4>Contactos</h4>
                        <ul>
                            <?php foreach (getContactos() as $contacto): ?>
                            <li><?php echo $contacto; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h4>FAQ's</h4>
                        <ul>
                            <?php foreach (getFooterLinks('Faqs') as $link): ?>
                            <li><a href="<?php echo $link['link']; ?>"><?php echo $link['nome']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h4>Tickets</h4>
                        <ul>
                            <?php foreach (getFooterLinks('Tickets') as $link): ?>
                            <li><a href="<?php echo $link['link']; ?>"><?php echo $link['nome']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom row align-items-center justify-content-between">
                <div class="col-lg-8 col-md-12">
                    <p class="footer-text m-0">
                        <?php echo $footerData['copyright_prefix']; ?>
                        <?php echo $footerData['copyright_year_script']; ?>
                        <?php echo $footerData['copyright_suffix']; ?>
                        <?php echo $footerData['created_by']; ?>
                    </p>
                </div>
                <div class="col-lg-4 col-sm-12 footer-social">
                    <a target="_blank" href="<?php echo $footerData['link1']; ?>"><i
                            class="<?php echo $footerData['icon1']; ?>"></i></a>
                    <a target="_blank" href="<?php echo $footerData['link2']; ?>"><img class="favinsta"
                            src="<?php echo $footerData['icon2']; ?>"></a>
                    <a target="_blank" href="<?php echo $footerData['link3']; ?>"><i
                            class="<?php echo $footerData['icon3']; ?>"></i></a>
                    <a target="_blank" href="<?php echo $footerData['link4']; ?>"><i
                            class="<?php echo $footerData['icon4']; ?>"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/extra.js" type="module"></script>
    <script src="js/google-login.js" type="module"></script>
    <script src="js/vendor/jquery-2.2.4.min.js"></script>
    <script src="js/easing.min.js"></script>
    <script src="js/hoverIntent.js"></script>
    <script src="js/superfish.min.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.tabs.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/mail-script.js"></script>
    <script src="js/main.js"></script>

    <?php include 'modals.php'; ?>
</body>

</html>