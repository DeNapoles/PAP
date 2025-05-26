<?php
require_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" href="<?php echo $inicioData['LogoSeparador']; ?>">
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
					<img src="<?php echo $inicioData['LogoPrincipal']; ?>" alt="logo" class="me-2" style="height: 40px;">
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
									<a class="nav-link" href="<?php echo $separador['link']; ?>" 
									   <?php echo ($separador['separador'] === 'Login') ? 'data-bs-toggle="modal" data-bs-target="#loginModal"' : ''; ?>>
										<?php echo $separador['separador']; ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			</nav>
		</div>
	</header>

	<!-- ----------------------------------- Modal de Login ----------------------------------- -->
	<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="loginModalLabel">Iniciar Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="loginError" style="display: none;"></div>
                <form id="loginForm">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                        <label for="email" class="fw-bold">Email</label>
                    </div>
                    <div class="form-floating mb-3" id="login-password-container" style="position: relative;">
                        <input type="password" class="form-control" id="password" placeholder="Password" required>
                        <label for="password" class="fw-bold">Palavra-passe</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Entrar</button>
                </form>
                <hr>
                <button id="google-login-b" class="btn btn-danger w-100 fw-bold">
                    <i class="fa fa-google me-2"></i> Entrar com o Google
                </button>
            </div>
        </div>
    </div>
	</div>
	<!-- ----------------------------------- Modal de Registo ----------------------------------- -->
	<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content shadow">
				<div class="modal-header">
					<h5 class="modal-title fw-bold" id="registerModalLabel">Criar conta</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger" id="registerError" style="display: none;"></div>
					<div class="alert alert-success" id="registerSuccess" style="display: none;"></div>
					<form id="registerForm">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="reg_nome" placeholder="Nome" required>
							<label for="reg_nome" class="fw-bold">Nome</label>
						</div>
						<div class="form-floating mb-3">
							<input type="email" class="form-control" id="reg_email" placeholder="Email" required>
							<label for="reg_email" class="fw-bold">Email</label>
						</div>
						<div class="form-floating mb-3" id="register-password-container" style="position: relative;">
							<input type="password" class="form-control" id="reg_password" placeholder="Palavra-passe" required>
							<label for="reg_password" class="fw-bold">Palavra-passe</label>
						</div>
						<div class="form-floating mb-3">
							<select class="form-select" id="reg_tipo" required>
								<option value="">Selecione o tipo de utilizador</option>
								<option value="Aluno">Aluno</option>
								<option value="Professor">Professor</option>
								<option value="Encarregado de Educação">Encarregado de Educação</option>
								<option value="Admin">Admin</option>
							</select>
							<label for="reg_tipo" class="fw-bold">Tipo de Utilizador</label>
						</div>
						<button type="submit" class="btn btn-primary w-100 fw-bold">Registar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- ----------------------------------- start Texto Incial Area ----------------------------------- -->
	<section class="banner-area relative" id="home">
		<div class="overlay overlay-bg"></div>
		<div class="container">
			<div class="row fullscreen d-flex align-items-center justify-content-between">
				<div class="banner-content col-lg-9 col-md-12">
					<h2 class="text-uppercase" style="font-size: 32px; margin-bottom: 10px; color: white;">
						<?php echo $inicioData['TextoInicial']; ?>
					</h2>
					<h1 class="text-uppercase" style="font-size: 23px;">
						<?php echo $inicioData['TextoBemvindo']; ?>
					</h1>
					<p class="pt-10 pb-10" style="font-size: 15px;">
						<?php echo $inicioData['TextoInicial2']; ?>
					</p>
					<a href="#" class="primary-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#registerModal">
						<?php echo $inicioData['BotaoInicial']; ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- ----------------------------------- End Texto Incial Area ----------------------------------- -->

	</section>
	<!-- End banner Area -->

	<!-- ----------------------------------- Start Links Escolares Area ----------------------------------- -->
	<section class="feature-area" style="margin-bottom: 50px;">
		<div class="container">
			<div class="row">
				<?php foreach ($ligacoesRapidas as $ligacao): ?>
					<div class="col-lg-3">
						<div class="single-feature">
							<div class="title">
								<a href="<?php echo $ligacao['Link']; ?>" target="_blank" style="color: inherit;">
									<h4><?php echo $ligacao['Nome']; ?></h4>
								</a>
							</div>
							<div class="desc-wrap">
								<p>
									<a href="<?php echo $ligacao['Link']; ?>" target="_blank">
										<img src="<?php echo $ligacao['Imagem']; ?>" 
											 alt="<?php echo $ligacao['Nome']; ?>" 
											 style="width: <?php echo $ligacao['Largura'] ?? 200; ?>px; 
													height: <?php echo $ligacao['Altura'] ?? 200; ?>px; 
													object-fit: contain;">
									</a>
								</p>
								<a href="<?php echo $ligacao['Link']; ?>" target="_blank">Aceda Já</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<!-- ----------------------------------- End Links Escolares Area ----------------------------------- -->

	<!-- ----------------------------------- Start Sobre Nós ----------------------------------- -->
	<section class="info-area pb-120">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-6 no-padd4ing info-area-left">
                <img class="img-fluid" src="<?php echo $sobreNos['Imagem']; ?>" alt="Sobre Nós">
            </div>
            <div class="col-lg-6 info-area-right" style="color: #000; text-align: justify; font-size: 19px;">
                <h1>Sobre Nós</h1>
                <p><?php echo $sobreNos['Texto1']; ?></p>
                <br>
                <p><?php echo $sobreNos['Texto2']; ?></p>
                <br>
            </div>
        </div>
    </div>
</section>
	<!-- ----------------------------------- End Sobre Nós ----------------------------------- -->

	<!-- ------------------------------------------ Start Avaliações ------------------------------------------ -->
	<section class="review-area section-gap relative">
		<div class="overlay overlay-bg"></div>
		<div class="container">
			<div class="row">
				<div class="active-review-carusel">
					<?php foreach ($avaliacoes as $avaliacao): ?>
						<div class="single-review item">
							<div class="title justify-content-start d-flex">
								<a href="#">
									<h4><?php echo $avaliacao['Nome']; ?></h4>
								</a>
								<div class="star">
									<?php for ($i = 0; $i < 5; $i++): ?>
										<span class="fa fa-star <?php echo $i < $avaliacao['Estrelas'] ? 'checked' : ''; ?>"></span>
									<?php endfor; ?>
								</div>
							</div>
							<p>
								<?php echo $avaliacao['Texto']; ?>
							</p>
						</div>
					<?php endforeach; ?>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Adicionar Comentário</h4>
							</a>
						</div>
						<p>
							Clique aqui para adicionar seu comentário.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ------------------------------------------ End Avaliações ------------------------------------------ -->



	<!-- ------------------------------------------ Start CTA LOGIN Index Area ------------------------------------------ -->
	<section class="cta-one-area relative section-gap" style="background-image: url('<?php echo $ctaInicio['Fundo']; ?>');">
		<div class="overlay overlay-bg"></div>
		<div class="wrap">
			<h1 class="text-white"><?php echo $ctaInicio['Titulo']; ?></h1>
			<p class="text-white">
				<?php echo $ctaInicio['Texto']; ?>
			</p>
			<a class="primary-btn wh" href="#" id="cta-login-button" data-bs-toggle="modal"
				data-bs-target="#loginModal"><?php echo $ctaInicio['BtnText']; ?></a>
		</div>
	</section>

	<!-- ------------------------------------------ End LOGIN Index Area ------------------------------------------ -->

	<!-- ------------------------------------------ Start blog Area ------------------------------------------ -->
	<section class="blog-area section-gap" id="blog">
		<div class="container">
			<div class="row d-flex justify-content-center">
				<div class="menu-content pb-70 col-lg-8">
					<div class="title text-center">
						<h1 class="mb-10">FAQ's</h1>
					</div>
				</div>
			</div>
			<div class="row">
				<?php foreach ($faqs as $faq): ?>
					<div class="col-lg-3 col-md-6 single-blog">
						<div class="thumb">
							<img class="img-fluid" src="<?php echo $faq['imagemfaq']; ?>" alt="">
						</div>
						<p class="meta"><a href="<?php echo $faq['Link']; ?>"></a></p>
						<a href="<?php echo $faq['Link']; ?>">
							<h5><?php echo $faq['titulofaq']; ?></h5>
						</a>
						<p>
							<?php echo $faq['textofaq']; ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<!-- ------------------------------------------ End blog Area ------------------------------------------ -->


	<!-- ------------------------------------------ Start AVISO LARANJA Area ------------------------------------------ -->
	<section class="cta-two-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 cta-left">
					<h1 style="font-size: 35px;"><?php echo $avisolaranjaInicio['Texto']; ?></h1>
				</div>
				<div class="col-lg-4 cta>-right">
					<a class="primary-btn wh" href="#"><?php echo $avisolaranjaInicio['Textobtn']; ?></a>
				</div>
			</div>
		</div>
	</section>

	<button onclick="topFunction()" id="backToTopBtn" title="Voltar ao topo">⬆</button>

	<!--  ------------------------------------------ End AVISO LARANJA Area ------------------------------------------ -->
	<!-- comentário -->
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
	<!-- ------------------------------------------ End FOOTER Area ------------------------------------------ -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="js/extra.js" type="module"></script>
	<script src="js/google-login.js" type="module"></script>
	<script src="js/vendor/jquery-2.2.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
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
	<script>
		// Verificar se existe o cookie de logout
		if (document.cookie.includes('logout=true')) {
			// Limpar o localStorage
			localStorage.removeItem('user');
			localStorage.removeItem('loginOrigem');
			
			// Remover o cookie
			document.cookie = 'logout=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
			
			// Recarregar a página para atualizar o estado
			location.reload();
		}
	</script>

	<?php include 'modals.php'; ?>
</body>

</html>