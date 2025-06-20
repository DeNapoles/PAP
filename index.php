<?php
require_once 'functions.php';
session_start();

// Buscar dados do utilizador logado, se houver
$user = null;
if (isset($_SESSION['user_id'])) {
    require_once 'connection.php';
    $stmt = $conn->prepare("SELECT Nome, Email FROM Utilizadores WHERE ID_Utilizador = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
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
					<div class="single-review item add-review-card">
						<div class="add-review-content">
							<div class="add-review-icon">
								<i class="fa fa-plus-circle"></i>
							</div>
							<h4 class="add-review-title">Adicionar Nova Avaliação</h4>
							<p class="add-review-text">Partilhe a sua experiência connosco</p>
							<button type="button" class="btn-add-review" onclick="toggleAvaliacaoForm(); return false;">
								<i class="fa fa-star"></i>
								Escrever Avaliação
								<i class="fa fa-arrow-right"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Formulário de Avaliação -->
			<div class="row" id="avaliacao-form-area" style="display: none; margin-top: 40px;">
				<div class="col-lg-8 mx-auto">
					<div id="avaliacaoForm" style="background: rgba(255,255,255,0.9); padding: 30px; border-radius: 5px;">
						<h4 class="text-center mb-4" style="color: #222;">Adicionar Avaliação</h4>
						
						<div class="row mb-3">
							<div class="col-md-6">
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome *" required>
							</div>
							<div class="col-md-6">
								<input type="email" class="form-control" id="avaliacao-email" name="email" placeholder="Email (opcional)">
							</div>
						</div>
						
						<div class="form-group mb-3 text-center">
							<label style="color: #222; margin-bottom: 10px;">Classificação:</label>
							<div class="star-rating" style="font-size: 24px;">
								<span id="star1" onclick="selectStar(1)" style="color: #ddd; cursor: pointer; margin: 0 3px; transition: color 0.2s;">★</span>
								<span id="star2" onclick="selectStar(2)" style="color: #ddd; cursor: pointer; margin: 0 3px; transition: color 0.2s;">★</span>
								<span id="star3" onclick="selectStar(3)" style="color: #ddd; cursor: pointer; margin: 0 3px; transition: color 0.2s;">★</span>
								<span id="star4" onclick="selectStar(4)" style="color: #ddd; cursor: pointer; margin: 0 3px; transition: color 0.2s;">★</span>
								<span id="star5" onclick="selectStar(5)" style="color: #ddd; cursor: pointer; margin: 0 3px; transition: color 0.2s;">★</span>
								<input type="hidden" id="estrelas" name="estrelas" value="0">
							</div>
						</div>
						
						<div class="form-group mb-4">
							<textarea class="form-control" id="texto" name="texto" rows="3" placeholder="A sua avaliação... *" required></textarea>
						</div>
						
						<div class="text-center">
							<button type="button" onclick="enviarAvaliacao()" class="primary-btn">Enviar</button>
							<button type="button" onclick="cancelarAvaliacao()" class="primary-btn" style="background: #6c757d; margin-left: 10px;">Cancelar</button>
						</div>
						
						<div id="form-message" style="display: none; margin-top: 20px;"></div>
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
					<a class="primary-btn wh" href="ajuda.php#sugestoes"><?php echo $avisolaranjaInicio['Textobtn']; ?></a>
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
				<?php if (isset($_SESSION['user_id'])): ?>
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h4>Tickets</h4>
						<ul>
							<li><a href="view_tickets.php">Ver Reparações</a></li>
							<li><a href="submit_ticket.php">Submeter Ticket</a></li>
						</ul>
					</div>
				</div>
				<?php endif; ?>
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
			
			// Limpar os campos do formulário de avaliação
			const nomeInput = document.getElementById('nome');
			const emailInput = document.getElementById('avaliacao-email');
			if (nomeInput) nomeInput.value = '';
			if (emailInput) emailInput.value = '';
			if (nomeInput) nomeInput.removeAttribute('readonly');
			if (emailInput) emailInput.removeAttribute('readonly');
			
			// Remover o cookie
			document.cookie = 'logout=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
			
			// Recarregar a página para atualizar o estado
			location.reload();
		}

		// Função para preencher os campos do formulário de avaliação
		function preencherCamposAvaliacao() {
			const nomeInput = document.getElementById('nome');
			const emailInput = document.getElementById('avaliacao-email');
			
			if (!nomeInput || !emailInput) return;
			
			// Verificar se há dados do usuário no localStorage
			const userData = localStorage.getItem('user');
			if (userData) {
				try {
					const user = JSON.parse(userData);
					nomeInput.value = user.nome || user.displayName || '';
					emailInput.value = user.email || '';
					nomeInput.setAttribute('readonly', 'readonly');
					emailInput.setAttribute('readonly', 'readonly');
				} catch (e) {
					console.error('Erro ao processar dados do usuário:', e);
				}
			}
		}

		// Chamar a função quando o formulário for exibido
		function toggleAvaliacaoForm() {
			const formArea = document.getElementById('avaliacao-form-area');
			
			if (formArea) {
				if (formArea.style.display === 'none' || formArea.style.display === '') {
					formArea.style.display = 'block';
					// Preencher os campos se o usuário estiver autenticado
					preencherCamposAvaliacao();
					// Scroll suave para o formulário
					formArea.scrollIntoView({ behavior: 'smooth' });
				} else {
					formArea.style.display = 'none';
				}
			}
		}

		function cancelarAvaliacao() {
			const formArea = document.getElementById('avaliacao-form-area');
			
			if (formArea) {
				formArea.style.display = 'none';
			}
			limparFormularioAvaliacao();
		}

		function limparFormularioAvaliacao() {
			const nomeInput = document.getElementById('nome');
			const emailInput = document.getElementById('avaliacao-email');
			const textoInput = document.getElementById('texto');
			const formMessage = document.getElementById('form-message');
			
			if (nomeInput) nomeInput.value = '';
			if (emailInput) emailInput.value = '';
			if (textoInput) textoInput.value = '';
			
			// Limpar classificação de estrelas
			clearStars();
			
			if (formMessage) formMessage.style.display = 'none';
		}

		function enviarAvaliacao() {
			console.log('Função enviarAvaliacao() chamada');

			// Coletar dados do formulário
			const nome = document.getElementById('nome').value.trim();
			const email = document.getElementById('avaliacao-email').value.trim();
			const estrelas = document.getElementById('estrelas').value;
			const texto = document.getElementById('texto').value.trim();

			console.log('Dados do formulário:', {nome, email, estrelas, texto});

			// Validações (email não é obrigatório)
			if (!nome) {
				showMessageAvaliacao('Por favor, preencha o seu nome.', 'error');
				return;
			}

			if (!texto) {
				showMessageAvaliacao('Por favor, escreva a sua avaliação.', 'error');
				return;
			}

			if (!estrelas || estrelas === '0') {
				showMessageAvaliacao('Por favor, selecione uma classificação de estrelas.', 'error');
				return;
			}

			const formData = new FormData();
			formData.append('nome', nome);
			formData.append('email', email);
			formData.append('estrelas', estrelas);
			formData.append('texto', texto);

			console.log('Enviando dados para process_avaliacao.php...');

			fetch('process_avaliacao.php', {
				method: 'POST',
				body: formData
			})
			.then(response => {
				console.log('Resposta recebida:', response);
				if (!response.ok) {
					throw new Error('Erro na resposta do servidor');
				}
				return response.json();
			})
			.then(data => {
				console.log('Dados da resposta:', data);
				if (data.success) {
					showMessageAvaliacao(data.message, 'success');
					limparFormularioAvaliacao();
					
					// Recarregar a página após alguns segundos para mostrar a nova avaliação
					setTimeout(() => {
						window.location.reload();
					}, 2000);
				} else {
					showMessageAvaliacao(data.message || 'Erro desconhecido', 'error');
				}
			})
			.catch(error => {
				console.error('Erro completo:', error);
				showMessageAvaliacao('Erro ao conectar com o servidor. Verifique sua conexão.', 'error');
			});
		}

		function showMessageAvaliacao(message, type) {
			const formMessage = document.getElementById('form-message');
			if (formMessage) {
				formMessage.innerHTML = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'}">${message}</div>`;
				formMessage.style.display = 'block';
			}
		}

		function getCookie(name) {
			const value = `; ${document.cookie}`;
			const parts = value.split(`; ${name}=`);
			if (parts.length === 2) return parts.pop().split(';').shift();
		}

		// Sistema de estrelas - versão simples e funcional
		var selectedRating = 0;

		function selectStar(rating) {
			selectedRating = rating;
			// Atualizar o input hidden
			const starsInput = document.getElementById('estrelas');
			if (starsInput) {
				starsInput.value = rating;
			}
			
			// Atualizar as estrelas visualmente
			for (let i = 1; i <= 5; i++) {
				const star = document.getElementById('star' + i);
				if (star) {
					if (i <= rating) {
						star.style.color = '#FFD700'; // Amarelo
					} else {
						star.style.color = '#ddd'; // Cinzento
					}
				}
			}
			
			console.log('Estrelas selecionadas:', rating);
		}

		// Função para limpar as estrelas
		function clearStars() {
			selectedRating = 0;
			for (let i = 1; i <= 5; i++) {
				const star = document.getElementById('star' + i);
				if (star) {
					star.style.color = '#ddd';
				}
			}
			const starsInput = document.getElementById('estrelas');
			if (starsInput) {
				starsInput.value = '0';
			}
		}
	</script>

	<?php include 'modals.php'; ?>
</body>

</html>