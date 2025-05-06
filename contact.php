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
	<title>AEB Conecta</title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
	<!--
			CSS
			============================================= -->
	<link rel="stylesheet" href="css/linearicons.css">
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

		<!--<div class="header-top">
	  			<div class="container">
			  		<div class="row">
			  			<div class="col-lg-6 col-sm-6 col-8 header-top-left no-padding">
			  				<ul>
								<li><a href="#"><i class="fa fa-facebook"></i></a></li>
								<li><a href="#"><i class="fa fa-twitter"></i></a></li>
								<li><a href="#"><i class="fa fa-dribbble"></i></a></li>
								<li><a href="#"><i class="fa fa-behance"></i></a></li>
			  				</ul>			
			  			</div>
			  			<div class="col-lg-6 col-sm-6 col-4 header-top-right no-padding">
			  				<a href="tel:+953 012 3654 896"><span class="lnr lnr-phone-handset"></span> <span class="text">+953 012 3654 896</span></a>
			  				<a href="mailto:support@colorlib.com"><span class="lnr lnr-envelope"></span> <span class="text">support@colorlib.com</span></a>			
			  			</div>
			  		</div>			  					
	  			</div>-->

		</div>
		<div class="container main-menu">
			<div class="row align-items-center justify-content-between d-flex">
				<div class="logo">
					<a href="index.php"><img src="img/logo1AEBConecta.png" alt="logo" title="" /></a>
				</div>
				<nav id="nav-menu-container">
					<ul class="nav-menu">
						<li><a href="index.php">Início</a></li>
						<li><a href="FAQs-home.html">FAQ's</a>
						</li>
						<li class="menu-has-children"><a href="">Ligações úteis</a>
							<ul>
								<li><a href="http://193.236.85.189/">GIAE</a></li>
								<li><a href="https://moodle.agbatalha.pt/">Moodle</a></li>
								<li><a href="https://agbatalha.pt/eusoupro/">Eu Sou Pro</a></li>
								<li><a href="https://www.facebook.com/aebatalha/?locale=pt_PT">Facebook do AEB</a></li>
							</ul>
						</li>
						<li><a href="Ajuda_index.php">Ajuda</a>
						</li>
						<li><a href="contact.html">Login</a></li>
					</ul>
				</nav><!-- #nav-menu-container -->
			</div>
		</div>
	</header><!-- #header -->

	<!-- start banner Area -->
	<section class="banner-area relative about-banner" id="home">
		<div class="overlay overlay-bg"></div>
		<div class="container">
			<div class="row d-flex align-items-center justify-content-center">
				<div class="about-content col-lg-12">
					<h1 class="text-white">
						Contacta nos
					</h1>
					<p class="text-white link-nav"><a href="index.php">Home </a> <span
							class="lnr lnr-arrow-right"></span> <a href="contact.html"> Contacta nos</a></p>
				</div>
			</div>
		</div>
	</section>
	<!-- End banner Area -->

	<!-- ================================= Start contact-page Area ================================= -->
	<section class="register-page-area section-gap">
		<div class="container py-5">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="card shadow-lg rounded-3 p-4">
						<h2 class="text-center mb-4 text-dark">Criar Conta</h2>
						<form class="form-area contact-form" id="registerForm" action="register.php" method="post">
							<div class="row">
								<!-- Nome -->
								<div class="col-lg-12 form-group mb-4">
									<label for="name" class="form-label text-dark fs-5">Nome</label>
									<input type="text" class="form-control border-dark shadow-sm" id="name" name="name"
										placeholder="Nome" required>
								</div>

								<!-- Número de Cartão -->
								<div class="col-lg-12 form-group mb-4">
									<label for="cardNumber" class="form-label text-dark fs-5">Número de Cartão</label>
									<input type="text" class="form-control border-dark shadow-sm" id="cardNumber"
										name="cardNumber" placeholder="Nº de cartão" required>
								</div>

								<!-- Email -->
								<div class="col-lg-12 form-group mb-4">
									<label for="email" class="form-label text-dark fs-5">Email</label>
									<input type="email" class="form-control border-dark shadow-sm" id="email"
										name="email" placeholder="Email" required>
								</div>

								<!-- Senha -->
								<div class="col-lg-12 form-group mb-4">
									<label for="password" class="form-label text-dark fs-5">Senha</label>
									<input type="password" class="form-control border-dark shadow-sm" id="password"
										name="password" placeholder="Password" required>
								</div>

								<!-- Data de Nascimento -->
								<div class="col-lg-12 form-group mb-4">
									<label for="dob" class="form-label text-dark fs-5">Data de Nascimento</label>
									<input type="date" class="form-control border-dark shadow-sm" id="dob" name="dob"
										required>
								</div>

								<!-- Checkbox Kit Digital -->
								<div class="col-lg-12 form-group mb-4">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="hasKit" name="hasKit">
										<label class="form-check-label" for="hasKit">
											Possuo o Kit Digital
										</label>
									</div>
								</div>

								<!-- Campos do Kit Digital (inicialmente ocultos) -->
								<div id="kitInfo" style="display: none;">
									<!-- Número de Série do Computador -->
									<div class="col-lg-12 form-group mb-4">
										<label for="computerSerial" class="form-label text-dark fs-5">Número de Série do
											Computador</label>
										<input type="text" class="form-control border-dark shadow-sm"
											id="computerSerial" name="computerSerial"
											placeholder="Nº de série do Computador">
									</div>

									<!-- Número de Série do Router -->
									<div class="col-lg-12 form-group mb-4">
										<label for="routerSerial" class="form-label text-dark fs-5">Número de Série do
											Router</label>
										<input type="text" class="form-control border-dark shadow-sm" id="routerSerial"
											name="routerSerial" placeholder="Nº de série do router">
									</div>

									<!-- Data de Aquisição -->
									<div class="col-lg-12 form-group mb-4">
										<label for="acquisitionDate" class="form-label text-dark fs-5">Data de
											Aquisição</label>
										<input type="date" class="form-control border-dark shadow-sm"
											id="acquisitionDate" name="acquisitionDate">
									</div>
								</div>

								<!-- Botão de Registo -->
								<div class="col-lg-12 text-center">
									<button type="submit"
										class="btn btn-dark w-100 shadow-lg rounded-pill py-2 text-uppercase">Criar
										Conta</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--  ------------------------------------------ End contact-page Area ------------------------------------------ -->

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
					<a target="_blank" href="<?php echo $footerData['link1']; ?>"><i class="<?php echo $footerData['icon1']; ?>"></i></a>
					<a target="_blank" href="<?php echo $footerData['link2']; ?>"><img class="favinsta" src="<?php echo $footerData['icon2']; ?>"></a>
					<a target="_blank" href="<?php echo $footerData['link3']; ?>"><i class="<?php echo $footerData['icon3']; ?>"></i></a>
					<a target="_blank" href="<?php echo $footerData['link4']; ?>"><i class="<?php echo $footerData['icon4']; ?>"></i></a>
				</div>
			</div>
		</div>
	</footer>


	<script src="js/vendor/jquery-2.2.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
	<script src="js/vendor/bootstrap.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script>
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
	<script src="js/extra.js"></script>
	<script>
		// Função para mostrar/esconder campos do Kit Digital com base na checkbox
		document.getElementById('hasKit').addEventListener('change', function () {
			var kitInfo = document.getElementById('kitInfo');
			if (this.checked) {
				kitInfo.style.display = 'block';
			} else {
				kitInfo.style.display = 'none';
			}
		});
	</script>
</body>

</html>