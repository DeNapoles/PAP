<?php
session_start();

require_once 'functions.php';
require_once 'functions_posts.php';

// Verificar se foi fornecido um ID
if (!isset($_GET['id'])) {
    header('Location: FAQs-home.php');
    exit;
}

$id = (int)$_GET['id'];
$post = getPostById($id);

if (!$post) {
    header('Location: FAQs-home.php');
    exit;
}

// Verificar se o utilizador está autenticado
$user = null;
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
} else {
    // Verificar se há um utilizador no localStorage (login via Google)
    if (isset($_COOKIE['user'])) {
        $userData = json_decode($_COOKIE['user'], true);
        if ($userData && isset($userData['id'])) {
            $user = $userData['id'];
        }
    }
}

// Processar comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['texto'])) {
    $comentario = trim($_POST['texto']);
    $assunto = isset($_POST['assunto']) ? trim($_POST['assunto']) : '';
    $comentario_pai_id = !empty($_POST['comentario_pai_id']) ? (int)$_POST['comentario_pai_id'] : null;
    
    if (!empty($comentario) && $user) {
        $sql = "INSERT INTO comentarios (post_id, utilizador_id, assunto, texto, comentario_pai_id, data_criacao) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $id, $user, $assunto, $comentario, $comentario_pai_id);
        $stmt->execute();
        
        // Redirecionar para evitar reenvio do formulário
        header("Location: blog-single.php?id=$id#comentarios");
        exit;
    }
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


			<!-- start banner Area -->
			<section class="banner-area relative" id="home">	
				<div class="overlay overlay-bg"></div>
				<div class="container">				
					<div class="row d-flex align-items-center justify-content-center">
						<div class="about-content col-lg-12">
							<h1 class="text-white">
								FAQ				
							</h1>					
						</div>	
					</div>
				</div>
			</section>
			<!-- End banner Area -->					  
			
			<!-- Start post-content Area -->
			<section class="post-content-area single-post-area">
				<div class="container">
					<div class="row">
						<div class="col-lg-8 posts-list">
							<div class="single-post row">
								<div class="col-lg-12">
									<div class="feature-img">
										<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_principal']); ?>" alt="">
									</div>									
								</div>
								<div class="col-lg-3  col-md-3 meta-details">
									<ul class="tags">
										<?php foreach ($post['tags'] as $tag): ?>
											<li><a href="FAQs-home.php?tag=<?php echo urlencode($tag); ?>"><?php echo htmlspecialchars($tag); ?>,</a></li>
										<?php endforeach; ?>
									</ul>
									<div class="user-details row">
										<p class="user-name col-lg-12 col-md-12 col-6">
											<a href="#"><?php echo htmlspecialchars($post['autor_nome']); ?></a> 
											<span class="lnr lnr-user"></span>
										</p>
										<p class="date col-lg-12 col-md-12 col-6">
											<a href="#"><?php echo formatDate($post['data_criacao']); ?></a> 
											<span class="lnr lnr-calendar-full"></span>
										</p>
										<p class="comments col-lg-12 col-md-12 col-6">
											<a href="#comentarios"><?php echo $post['num_comentarios']; ?> Comments</a> 
											<span class="lnr lnr-bubble"></span>
										</p>
									</div>
								</div>
								<div class="col-lg-9 col-md-9">
									<h3 class="mt-20 mb-20"><?php echo htmlspecialchars($post['titulo']); ?></h3>
									<p class="excert">
										<?php echo nl2br(htmlspecialchars($post['texto'])); ?>
									</p>
								</div>
								<?php if (!empty($post['img_1']) || !empty($post['img_2']) || !empty($post['img_3']) || !empty($post['img_4']) || !empty($post['img_5'])): ?>
								<div class="col-lg-12">
									<div class="row mt-30 mb-30">
										<?php if (!empty($post['img_1'])): ?>
										<div class="col-12 mb-4">
											<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_1']); ?>" alt="">
										</div>
										<?php endif; ?>
										<?php if (!empty($post['img_2'])): ?>
										<div class="col-12 mb-4">
											<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_2']); ?>" alt="">
										</div>
										<?php endif; ?>
										<?php if (!empty($post['img_3'])): ?>
										<div class="col-12 mb-4">
											<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_3']); ?>" alt="">
										</div>
										<?php endif; ?>
										<?php if (!empty($post['img_4'])): ?>
										<div class="col-12 mb-4">
											<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_4']); ?>" alt="">
										</div>
										<?php endif; ?>
										<?php if (!empty($post['img_5'])): ?>
										<div class="col-12 mb-4">
											<img class="img-fluid" src="<?php echo htmlspecialchars($post['img_5']); ?>" alt="">
										</div>
										<?php endif; ?>
									</div>
								</div>
								<?php endif; ?>
							</div>

                            <!-- ------------------------------------------ start Comentários Area ------------------------------------------ -->

						
							<div class="comments-area" id="comentarios">
								<h4><?php echo $post['num_comentarios']; ?> Comments</h4>
								<?php
								// Obter comentários principais
								$comments = displayComments($id);
								
								// Função para renderizar um comentário e suas respostas
								function renderComment($comment, $user) {
									?>
									<div class="comment-list <?php echo $comment['padding_class']; ?>">
										<div class="single-comment d-flex">
											<div class="user flex-grow-1 d-flex">
												<div class="thumb">
													<img src="img/blog/img_profilepic.png" alt="">
												</div>
												<div class="desc">
													<h5><a href="#"><?php echo htmlspecialchars($comment['autor_nome']); ?></a></h5>
													<p class="date"><?php echo formatDate($comment['data_criacao']); ?></p>
													<?php if (!empty($comment['assunto'])): ?>
														<h6><?php echo htmlspecialchars($comment['assunto']); ?></h6>
													<?php endif; ?>
													<p class="comment">
														<?php echo nl2br(htmlspecialchars($comment['texto'])); ?>
													</p>
												</div>
											</div>
											<div class="reply-buttons" style="min-width: 120px; margin-left: 15px;">
												<?php if ($comment['num_respostas'] > 0): ?>
													<button type="button" class="genric-btn primary-border circle btn-show-replies" data-comment-id="<?php echo $comment['id']; ?>">
														Ver Respostas
													</button>
												<?php endif; ?>
												<?php if ($user): ?>
													<button type="button" class="genric-btn primary circle btn-respond" data-comment-id="<?php echo $comment['id']; ?>" data-author="<?php echo htmlspecialchars($comment['autor_nome']); ?>" data-text="<?php echo htmlspecialchars($comment['texto']); ?>">
														Responder
													</button>
												<?php endif; ?>
											</div>
										</div>
										<div class="replies-container" id="replies-<?php echo $comment['id']; ?>" style="display: none;">
											<?php
											$replies = displayComments($comment['post_id'], $comment['id'], 1, $user);
											foreach ($replies as $reply) {
												renderComment($reply, $user);
											}
											?>
										</div>
									</div>
									<?php
								}

								// Renderizar todos os comentários principais
								if (!empty($comments)) {
									foreach ($comments as $comment) {
										renderComment($comment, $user);
									}
								} else {
									echo '<p>Nenhum comentário ainda. Seja o primeiro a comentar!</p>';
								}
								?>
							</div>
							
							<?php if ($user): ?>
							<div class="comment-form">
								<h4>Deixar um Comentário</h4>
								<div id="replyTo" style="display: none;" class="alert alert-info mb-3">
									<strong>Respondendo a:</strong> <span id="replyToAuthor"></span>
									<p class="mb-0"><small id="replyToText"></small></p>
									<button type="button" class="btn btn-sm btn-link p-0" onclick="cancelReply()">Cancelar resposta</button>
								</div>
								<form id="commentForm" method="POST" action="process_comment.php">
									<input type="hidden" name="post_id" value="<?php echo $id; ?>">
									<input type="hidden" name="comentario_pai_id" id="comentario_pai_id" value="">
									<div class="form-group form-inline">
										<div class="form-group col-lg-6 col-md-12 name">
											<input type="text" class="form-control" name="assunto" placeholder="Assunto" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Assunto'">
										</div>
									</div>
									<div class="form-group">
										<textarea class="form-control mb-10" rows="5" name="texto" placeholder="Mensagem" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Mensagem'" required></textarea>
									</div>
									<button type="submit" class="primary-btn text-uppercase">Publicar Comentário</button>
								</form>
							</div>
							<?php else: ?>
							<div class="comment-form">
								<div class="alert alert-info">
									<p>Por favor, <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">inicie sessão</a> para deixar um comentário.</p>
								</div>
							</div>
							<?php endif; ?>

							<script>
							document.addEventListener('DOMContentLoaded', function() {
								// Função para cancelar resposta
								window.cancelReply = function() {
									document.getElementById('comentario_pai_id').value = '';
									document.getElementById('replyTo').style.display = 'none';
									document.getElementById('replyToAuthor').textContent = '';
									document.getElementById('replyToText').textContent = '';
								};

								// Verificar estado de autenticação
								function checkAuthState() {
									const user = localStorage.getItem('user');
									if (user) {
										try {
											const userData = JSON.parse(user);
											// Atualizar UI para estado autenticado
											document.querySelectorAll('.comment-form').forEach(form => {
												if (form.querySelector('.alert-info')) {
													form.innerHTML = `
														<h4>Deixar um Comentário</h4>
														<div id="replyTo" style="display: none;" class="alert alert-info mb-3">
															<strong>Respondendo a:</strong> <span id="replyToAuthor"></span>
															<p class="mb-0"><small id="replyToText"></small></p>
															<button type="button" class="btn btn-sm btn-link p-0" onclick="cancelReply()">Cancelar resposta</button>
														</div>
														<form id="commentForm" method="POST" action="process_comment.php">
															<input type="hidden" name="post_id" value="<?php echo $id; ?>">
															<input type="hidden" name="comentario_pai_id" id="comentario_pai_id" value="">
															<div class="form-group form-inline">
																<div class="form-group col-lg-6 col-md-12 name">
																	<input type="text" class="form-control" name="assunto" placeholder="Assunto" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Assunto'">
																</div>
															</div>
															<div class="form-group">
																<textarea class="form-control mb-10" rows="5" name="texto" placeholder="Mensagem" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Mensagem'" required></textarea>
															</div>
															<button type="submit" class="primary-btn text-uppercase">Publicar Comentário</button>
														</form>
													`;
												}
											});
													
													// Mostrar botões de resposta
													document.querySelectorAll('.single-comment').forEach(comment => {
														if (!comment.querySelector('.reply-buttons')) {
															const replyButtons = document.createElement('div');
															replyButtons.className = 'reply-buttons';
															replyButtons.style.cssText = 'min-width: 120px; margin-left: 15px;';
															replyButtons.innerHTML = `
																<button type="button" class="genric-btn primary-border circle btn-show-replies" data-comment-id="${comment.dataset.commentId}">
																	Ver Respostas
																</button>
																<button type="button" class="genric-btn primary circle btn-respond" 
																	data-comment-id="${comment.dataset.commentId}" 
																	data-author="${comment.dataset.author}" 
																	data-text="${comment.dataset.text}">
																	Responder
																</button>
															`;
															comment.appendChild(replyButtons);
														}
													});
										} catch (e) {
											console.error('Erro ao processar dados do utilizador:', e);
										}
									} else {
										// Remover formulário de comentário e mostrar aviso
										document.querySelectorAll('.comment-form').forEach(form => {
											form.innerHTML = `
												<div class="alert alert-info">
													<p>Por favor, <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">inicie sessão</a> para deixar um comentário.</p>
												</div>
											`;
										});
										// Remover botões de responder de todos os comentários
										document.querySelectorAll('.reply-buttons').forEach(btns => {
											// Só remove o botão de responder, deixa o de ver respostas
											const responder = btns.querySelector('.btn-respond');
											if (responder) responder.remove();
										});
									}
								}

								// Verificar estado inicial
								checkAuthState();

								// Adicionar listener para mudanças no localStorage
								window.addEventListener('storage', function(e) {
									if (e.key === 'user') {
										checkAuthState();
									}
								});

								// Resto do código JavaScript existente...
								const commentForm = document.getElementById('commentForm');
								if (commentForm) {
									// ... código existente do formulário ...
								}

								// Adicionar evento de clique para os botões de responder
								document.querySelectorAll('.btn-respond').forEach(button => {
									button.addEventListener('click', function(e) {
										e.preventDefault();
										const commentId = this.dataset.commentId;
										const author = this.dataset.author;
										const text = this.dataset.text;

										// Atualizar formulário para resposta
										document.getElementById('comentario_pai_id').value = commentId;
										document.getElementById('replyToAuthor').textContent = author;
										document.getElementById('replyToText').textContent = text;
										document.getElementById('replyTo').style.display = 'block';
										document.getElementById('commentForm').scrollIntoView({ behavior: 'smooth' });
									});
								});

								// Adicionar evento de clique para os botões de mostrar respostas
								document.querySelectorAll('.btn-show-replies').forEach(button => {
									button.addEventListener('click', function(e) {
										e.preventDefault();
										const commentId = this.dataset.commentId;
										const repliesContainer = document.getElementById('replies-' + commentId);
										
										if (repliesContainer) {
											if (repliesContainer.style.display === 'none') {
												repliesContainer.style.display = 'block';
												this.textContent = 'Esconder Respostas';
											} else {
												repliesContainer.style.display = 'none';
												this.textContent = 'Ver Respostas';
											}
										}
									});
								});
							});
							</script>
							
						</div>

						<!-- ------------------------------------------ END Comentários Area ------------------------------------------ -->

						<div class="col-lg-4 sidebar-widgets">
							<div class="widget-wrap">
								<div class="single-sidebar-widget search-widget">
									<form class="search-form" action="#">
			                            <input placeholder="Search Posts" name="search" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Posts'" >
			                            <button type="submit"><i class="fa fa-search"></i></button>
			                        </form>
								</div>
								<div class="single-sidebar-widget user-info-widget">
									<img src="img/blog/user-info.png" alt="">
									<a href="#"><h4>Charlie Barber</h4></a>
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
										Boot camps have its supporters andit sdetractors. Some people do not understand why you should have to spend money on boot camp when you can get. Boot camps have itssuppor ters andits detractors.
									</p>
								</div>
								<div class="single-sidebar-widget popular-post-widget">
									<h4 class="popular-title">Popular Posts</h4>
									<div class="popular-post-list">
										<div class="single-post-list d-flex flex-row align-items-center">
											<div class="thumb">
												<img class="img-fluid" src="img/blog/pp1.jpg" alt="">
											</div>
											<div class="details">
												<a href="blog-single.html"><h6>Space The Final Frontier</h6></a>
												<p>02 Hours ago</p>
											</div>
										</div>
										<div class="single-post-list d-flex flex-row align-items-center">
											<div class="thumb">
												<img class="img-fluid" src="img/blog/pp2.jpg" alt="">
											</div>
											<div class="details">
												<a href="blog-single.html"><h6>The Amazing Hubble</h6></a>
												<p>02 Hours ago</p>
											</div>
										</div>
										<div class="single-post-list d-flex flex-row align-items-center">
											<div class="thumb">
												<img class="img-fluid" src="img/blog/pp3.jpg" alt="">
											</div>
											<div class="details">
												<a href="blog-single.html"><h6>Astronomy Or Astrology</h6></a>
												<p>02 Hours ago</p>
											</div>
										</div>
										<div class="single-post-list d-flex flex-row align-items-center">
											<div class="thumb">
												<img class="img-fluid" src="img/blog/pp4.jpg" alt="">
											</div>
											<div class="details">
												<a href="blog-single.html"><h6>Asteroids telescope</h6></a>
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
									          <div class="input-group-text"><i class="fa fa-envelope" aria-hidden="true"></i>
											</div>
									        </div>
									        <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Enter email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email'" >
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
										<li><a href="#">Technology</a></li>
										<li><a href="#">Fashion</a></li>
										<li><a href="#">Architecture</a></li>
										<li><a href="#">Fashion</a></li>
										<li><a href="#">Food</a></li>
										<li><a href="#">Technology</a></li>
										<li><a href="#">Lifestyle</a></li>
										<li><a href="#">Art</a></li>
										<li><a href="#">Adventure</a></li>
										<li><a href="#">Food</a></li>
										<li><a href="#">Lifestyle</a></li>
										<li><a href="#">Adventure</a></li>
									</ul>
								</div>								
							</div>
						</div>
					</div>
				</div>	
			</section>
			<!-- End post-content Area -->
			
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
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
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

			<!-- Scripts necessários para o login -->
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
			<script src="js/extra.js" type="module"></script>
			<script src="js/google-login.js" type="module"></script>

			<?php include 'modals.php'; ?>
		</body>
	</html>