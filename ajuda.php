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
	<title>Ajuda - AEB Conecta</title>

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
									<a class="nav-link <?php echo ($separador['separador'] === 'Ajuda') ? 'active' : ''; ?>" 
									   href="<?php echo $separador['link']; ?>" 
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

	<!-- start banner Area -->
	<section class="banner-area relative about-banner" id="home">
		<div class="overlay overlay-bg"></div>
		<div class="container">
			<div class="row d-flex align-items-center justify-content-center">
				<div class="about-content col-lg-12">
					<h1 class="text-white">
						Ajuda
					</h1>
					<p class="text-white link-nav"><a href="index.php">Home </a> <span class="lnr lnr-arrow-right"></span> <a href="ajuda.php"> Ajuda</a></p>
				</div>
			</div>
		</div>
	</section>
	<!-- End banner Area -->

	<!-- ================================= Start Disclaimer Section ================================= -->
	<section class="section-gap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="card shadow-lg rounded-3 p-5 mb-5">
						<h2 class="text-center mb-4 text-dark">Aviso Legal (Disclaimer)</h2>
						<div class="disclaimer-content" style="text-align: justify; line-height: 1.8; font-size: 16px;">
							<p>Este site disponibiliza respostas a perguntas frequentes (FAQs) relacionadas com os kits digitais fornecidos pela escola, o GIAE, o Moodle e outras plataformas educativas utilizadas no contexto escolar. A maioria das informações foi fornecida ou validada por professores, com o objetivo de orientar os alunos de forma clara, fiável e acessível.</p>
							
							<p>Este site pode conter hiperligações para recursos educativos externos ou outros websites. Não temos controlo sobre o conteúdo ou as políticas desses sites, e não assumimos qualquer responsabilidade pelos mesmos. Recomendamos que os utilizadores consultem as respetivas políticas de privacidade e termos de utilização.</p>
							
							<p>Reservamo-nos o direito de atualizar ou modificar o conteúdo deste site a qualquer momento e sem aviso prévio, de forma a refletir novas informações ou alterações nas diretrizes educativas.</p>
							
							<h4 class="mt-4 mb-3">Política de Cookies</h4>
							<p>Este site utiliza cookies para melhorar a experiência do utilizador, analisar o tráfego e personalizar conteúdos.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ================================= End Disclaimer Section ================================= -->

	<!-- ================================= Start Suggestions Form Section ================================= -->
	<section class="section-gap bg-light">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="card shadow-lg rounded-3 p-4">
						<h3 class="text-center mb-4 text-dark">Formulário de Sugestões</h3>
						<p class="text-center text-muted mb-4">Tem alguma sugestão para melhorar o nosso site? Partilhe connosco!</p>
						
						<form action="process_suggestions.php" method="POST" id="suggestionsForm">
							<div class="row">
								<!-- Nome -->
								<div class="col-lg-6 form-group mb-4">
									<label for="nome" class="form-label text-dark">Nome *</label>
									<input type="text" class="form-control border-2" id="nome" name="nome" placeholder="O seu nome" required>
								</div>

								<!-- Email -->
								<div class="col-lg-6 form-group mb-4">
									<label for="email_sugestao" class="form-label text-dark">Email *</label>
									<input type="email" class="form-control border-2" id="email_sugestao" name="email" placeholder="O seu email" required>
								</div>

								<!-- Tipo de Sugestão -->
								<div class="col-lg-12 form-group mb-4">
									<label for="tipo_sugestao" class="form-label text-dark">Tipo de Sugestão *</label>
									<select class="form-control border-2" id="tipo_sugestao" name="tipo_sugestao" required>
										<option value="">Selecione o tipo de sugestão</option>
										<option value="melhoria_conteudo">Melhoria de Conteúdo</option>
										<option value="nova_funcionalidade">Nova Funcionalidade</option>
										<option value="problema_tecnico">Problema Técnico</option>
										<option value="design_interface">Design/Interface</option>
										<option value="outro">Outro</option>
									</select>
								</div>

								<!-- Mensagem -->
								<div class="col-lg-12 form-group mb-4">
									<label for="mensagem" class="form-label text-dark">Sugestão/Mensagem *</label>
									<textarea class="form-control border-2" id="mensagem" name="mensagem" rows="6" 
											  placeholder="Descreva a sua sugestão em detalhe..." required></textarea>
								</div>

								<!-- Prioridade -->
								<div class="col-lg-12 form-group mb-4">
									<label class="form-label text-dark">Prioridade</label>
									<div class="d-flex gap-3">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="prioridade" id="baixa" value="baixa" checked>
											<label class="form-check-label" for="baixa">Baixa</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="prioridade" id="media" value="media">
											<label class="form-check-label" for="media">Média</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="prioridade" id="alta" value="alta">
											<label class="form-check-label" for="alta">Alta</label>
										</div>
									</div>
								</div>

								<!-- Botão de Envio -->
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow">
										<i class="fa fa-paper-plane me-2"></i>Enviar Sugestão
									</button>
								</div>
							</div>
						</form>

						<!-- Mensagem de Sucesso -->
						<div id="success-message" class="alert alert-success mt-4" style="display: none;">
							<i class="fa fa-check-circle me-2"></i>Obrigado pela sua sugestão! A sua mensagem foi enviada com sucesso.
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ================================= End Suggestions Form Section ================================= -->

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
	<!-- ------------------------------------------ End FOOTER Area ------------------------------------------ -->

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="js/extra.js" type="module"></script>
	<script src="js/vendor/jquery-2.2.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
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
		// Scroll to top function
		function topFunction() {
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		}

		// Show/hide back to top button
		window.onscroll = function() {
			if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
				document.getElementById("backToTopBtn").style.display = "block";
			} else {
				document.getElementById("backToTopBtn").style.display = "none";
			}
		};

		// Form submission with AJAX
		document.getElementById('suggestionsForm').addEventListener('submit', function(e) {
			e.preventDefault();
			
			const formData = new FormData(this);
			
			fetch('process_suggestions.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					document.getElementById('success-message').style.display = 'block';
					this.reset();
					setTimeout(() => {
						document.getElementById('success-message').style.display = 'none';
					}, 5000);
				} else {
					alert('Erro ao enviar sugestão. Tente novamente.');
				}
			})
			.catch(error => {
				console.error('Erro:', error);
				alert('Erro ao enviar sugestão. Tente novamente.');
			});
		});
	</script>

	<?php include 'modals.php'; ?>
</body>

</html>