<?php
header('Content-Type: text/html; charset=utf-8');
include 'connection.php';
$inicioData = getInicioInicio();
$sobreNos = getSobreNos();
$separadores = getSeparadores();
$ligacoesUteis = getLigacoesUteis();
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
				<a class="navbar-brand d-flex align-items-center" href="index.php">
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
					<h5 class="modal-title" id="loginModalLabel">Iniciar Sessão</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">
					<form id="loginForm">
						<div class="form-floating mb-3">
							<input type="email" class="form-control" id="email" placeholder="name@example.com" required>
							<label for="email">Email</label>
						</div>
						<div class="form-floating mb-3">
							<input type="password" class="form-control" id="password" placeholder="Password" required>
							<label for="password">Palavra-passe</label>
						</div>
						<button type="submit" class="btn btn-primary w-100">Entrar</button>
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
					<h1 class="text-uppercase">
						Bem-vindo ao AEBConecta
					</h1>
					<p class="pt-10 pb-10">
						O portal que liga a comunidade escolar com informações úteis, respostas rápidas e apoio
						especializado.
					</p>
					<a href="contact.html" id="banner-login-button" class="primary-btn text-uppercase">Regista-te!</a>
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
				<div class="col-lg-4">
					<div class="single-feature">
						<div class="title">
							<h4>GIAE</h4>
						</div>
						<div class="desc-wrap">
							<p>
								<img src="img/giae_img.jpeg" alt="img_giae" style="width: 150px;">
							</p>
							<a href="http://193.236.85.189/" target="_blank">Aceda Já</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="single-feature">
						<div class="title">
							<h4>Moodle</h4>
						</div>
						<div class="desc-wrap">
							<p>
								<img src="img/moodle.png" alt="img_moodle">

							</p>
							<a href="https://moodle.agbatalha.pt/" target="_blank">Aceda Já</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="single-feature">
						<div class="title">
							<h4>Eu Sou PRO</h4>
						</div>
						<div class="desc-wrap">
							<p>
								<img src="img/eusoupro.jpeg" alt="img_eusoupro" style="width: 150px;">
							</p>
							<a href="https://agbatalha.pt/eusoupro/" target="_blank">Aceda Já</a>
						</div>
					</div>
				</div>
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


	<!-- ----------------------------------- Start popular-course Area ----------------------------------- 
			<section class="popular-course-area section-gap">
				<div class="container">
					<div class="row d-flex justify-content-center">
						<div class="menu-content pb-70 col-lg-8">
							<div class="title text-center">
								<h1 class="mb-10">Popular Courses we offer</h1>
								<p>There is a moment in the life of any aspiring.</p>
							</div>
						</div>
					</div>						
					<div class="row">
						<div class="active-popular-carusel">
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p1.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Designing
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p2.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn React js beginners
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p3.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Photography
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p4.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Surveying
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p1.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Designing
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p2.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn React js beginners
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p3.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Photography
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>	
							<div class="single-popular-carusel">
								<div class="thumb-wrap relative">
									<div class="thumb relative">
										<div class="overlay overlay-bg"></div>	
										<img class="img-fluid" src="img/p4.jpg" alt="">
									</div>
									<div class="meta d-flex justify-content-between">
										<p><span class="lnr lnr-users"></span> 355 <span class="lnr lnr-bubble"></span>35</p>
										<h4>$150</h4>
									</div>									
								</div>
								<div class="details">
									<a href="#">
										<h4>
											Learn Surveying
										</h4>
									</a>
									<p>
										When television was young, there was a hugely popular show based on the still popular fictional characte										
									</p>
								</div>
							</div>							
						</div>
					</div>
				</div>	
			</section>
			 End popular-course Area -->


	<!-- ------------------------------------------ Start search-course Area ------------------------------------------ 
			<section class="search-course-area relative">
				<div class="overlay overlay-bg"></div>
				<div class="container">
					<div class="row justify-content-between align-items-center">
						<div class="col-lg-6 col-md-6 search-course-left">
							<h1 class="text-white">
								Get reduced fee <br>
								during this Summer!
							</h1>
							<p>
								inappropriate behavior is often laughed off as "boys will be boys," women face higher conduct standards especially in the workplace. That's why it's crucial that, as women, our behavior on the job is beyond reproach.
							</p>
							<div class="row details-content">
								<div class="col single-detials">
									<span class="lnr lnr-graduation-hat"></span>
									<a href="#"><h4>Expert Instructors</h4></a>		
									<p>
										Usage of the Internet is becoming more common due to rapid advancement of technology and power.
									</p>						
								</div>
								<div class="col single-detials">
									<span class="lnr lnr-license"></span>
									<a href="#"><h4>Certification</h4></a>		
									<p>
										Usage of the Internet is becoming more common due to rapid advancement of technology and power.
									</p>						
								</div>								
							</div>
						</div>
						<div class="col-lg-4 col-md-6 search-course-right section-gap">
							<form class="form-wrap" action="#">
								<h4 class="text-white pb-20 text-center mb-30">Search for Available Course</h4>		
								<input type="text" class="form-control" name="name" placeholder="Your Name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Your Name'" >
								<input type="phone" class="form-control" name="phone" placeholder="Your Phone Number" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Your Phone Number'" >
								<input type="email" class="form-control" name="email" placeholder="Your Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Your Email Address'" >
								<div class="form-select" id="service-select">
									<select>
										<option datd-display="">Choose Course</option>
										<option value="1">Course One</option>
										<option value="2">Course Two</option>
										<option value="3">Course Three</option>
										<option value="4">Course Four</option>
									</select>
								</div>									
								<button class="primary-btn text-uppercase">Submit</button>
							</form>
						</div>
					</div>
				</div>	
			</section>
			------------------------------------------ End search-course Area ------------------------------------------ -->


	<!-- ------------------------------------------ Start upcoming-event Area ------------------------------------------ 
			<section class="upcoming-event-area section-gap">
				<div class="container">
					<div class="row d-flex justify-content-center">
						<div class="menu-content pb-70 col-lg-8">
							<div class="title text-center">
								<h1 class="mb-10">Upcoming Events of our Institute</h1>
								<p>If you are a serious astronomy fanatic like a lot of us</p>
							</div>
						</div>
					</div>								
					<div class="row">
						<div class="active-upcoming-event-carusel">
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e1.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e2.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>	
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e1.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>	
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e1.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e2.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>	
							<div class="single-carusel row align-items-center">
								<div class="col-12 col-md-6 thumb">
									<img class="img-fluid" src="img/e1.jpg" alt="">
								</div>
								<div class="detials col-12 col-md-6">
									<p>25th February, 2018</p>
									<a href="#"><h4>The Universe Through
									A Child S Eyes</h4></a>
									<p>
										For most of us, the idea of astronomy is something we directly connect to "stargazing", telescopes and seeing magnificent displays in the heavens.
									</p>
								</div>
							</div>																						
						</div>
					</div>
				</div>	
			</section>
			 ------------------------------------------ End upcoming-event Area ------------------------------------------ -->


	<!-- ------------------------------------------ Start Avaliações ------------------------------------------ -->
	<section class="review-area section-gap relative">
		<div class="overlay overlay-bg"></div>
		<div class="container">
			<div class="row">
				<div class="active-review-carusel">
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Fannie Rowe</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Hulda Sutton</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Fannie Rowe</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Hulda Sutton</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Fannie Rowe</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Hulda Sutton</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<img src="img/r1.png" alt="">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Fannie Rowe</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
					<div class="single-review item">
						<div class="title justify-content-start d-flex">
							<a href="#">
								<h4>Hulda Sutton</h4>
							</a>
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
						</div>
						<p>
							Accessories Here you can find the best computer accessory for your laptop, monitor, printer,
							scanner, speaker. Here you can find the best computer accessory for your laptop, monitor,
							printer, scanner, speaker.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ------------------------------------------ End Avaliações ------------------------------------------ -->



	<!-- ------------------------------------------ Start CTA LOGIN Index Area ------------------------------------------ -->
	<section class="cta-one-area relative section-gap">
		<div class="overlay overlay-bg"></div>
		<div class="wrap">
			<h1 class="text-white">Bem-vindo ao AEBConecta</h1>
			<p class="text-white">
				Regista-te para agendar serviços de manutenção. Obtenha soluções rápidas para problemas tecnológicos.
			</p>
			<a class="primary-btn wh" href="#" id="cta-login-button" data-bs-toggle="modal"
				data-bs-target="#loginModal">Entrar</a>
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
				<div class="col-lg-3 col-md-6 single-blog">
					<div class="thumb">
						<img class="img-fluid" src="img/b1.jpg" alt="">
					</div>
					<p class="meta"><!--25 April, 2018  |  By--> <a href="FAQs-GIAE.html"><!--Mark Wiens--></a></p>
					<a href="blog-single.html">
						<h5>FAQ's relacionadas com o GIAE
						</h5>
					</a>
					<p>
						Clica aqui para aceder as perguntas mais frequêntes relacionadas com o GIAE.
					<p> </p>
					</p>
				</div>
				<div class="col-lg-3 col-md-6 single-blog">
					<div class="thumb">
						<img class="img-fluid" src="img/b2.jpg" alt="">
					</div>
					<p class="meta"><!--25 April, 2018  |  By--> <a href="FAQs-Moodle.html"><!--Mark Wiens--></a></p>
					<a href="blog-single.html">
						<h5>FAQ's relacionadas com o Moodle</h5>
					</a>
					<p>
						Clica aqui para aceder as perguntas mais frequêntes relacionadas com o Moodle
					</p>
				</div>
				<div class="col-lg-3 col-md-6 single-blog">
					<div class="thumb">
						<img class="img-fluid" src="img/b3.jpg" alt="">
					</div>
					<p class="meta"><!--25 April, 2018  |  By--> <a href="FAQs-CompEscolar.html"><!--Mark Wiens--></a>
					</p>
					<a href="blog-single.html">
						<h5>FAQ's relacionadas com os Computadores do kit digital
							 
						</h5>
					</a>
					<p>
						Clica aqui para aceder as perguntas mais frequêntes relacionadas com os Computadores do kit
						digital.
					</p>
				</div>
				<div class="col-lg-3 col-md-6 single-blog">
					<div class="thumb">
						<img class="img-fluid" src="img/b4.jpg" alt="">
					</div>
					<p class="meta"><!--25 April, 2018  |  By--> <a href="FAQs-AcessoriosKit.html"><!--Mark Wiens--></a>
					</p>
					<a href="blog-single.html">
						<h5>FAQ's relacionadas com os acessórios do kit digital</h5>
					</a>
					<p>
						Clica aqui para aceder as perguntas mais frequêntes relacionadas com os acessórios do kit
						digital.
					</p>
				</div>
			</div>
		</div>
	</section>
	<!-- ------------------------------------------ End blog Area ------------------------------------------ -->


	<!-- ------------------------------------------ Start cta-two Area ------------------------------------------ -->
	<section class="cta-two-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 cta-left">
					<h1>Tens alguma reclamação sobre o site?</h1>

				</div>
				<div class="col-lg-4 cta-right">
					<a class="primary-btn wh" href="#">Faz a tua reclamação</a>
				</div>
			</div>
		</div>
	</section>

	<button onclick="topFunction()" id="backToTopBtn" title="Voltar ao topo">⬆</button>

	<!--  ------------------------------------------ End cta-two Area ------------------------------------------ -->

	<!-- ------------------------------------------ start footer Area ------------------------------------------ -->
	<footer class="footer-area section-gap">
		<div class="container">
			<div class="row">
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h4>Ligações úteis</h4>
						<ul>
							<li><a href="http://193.236.85.189/">GIAE</a></li>
							<li><a href="https://moodle.agbatalha.pt/">Moodle</a></li>
							<li><a href="https://agbatalha.pt/eusoupro/">Eu Sou Pro</a></li>
							<li><a href="https://www.facebook.com/aebatalha/?locale=pt_PT">Facebook do AEB</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h4>Contactos</h4>
						<ul>
							<li>Escola Básica e Secundária da Batalha</a></li>
							<li>Estrada da Freiria</a></li>
							<li>2440-062 Batalha Portugal</a></li>
							<li>Telefone: 244 769 290</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h4>FAQ's</h4>
						<ul>
							<li><a href="#">GIAE</a></li>
							<li><a href="#">Moodle</a></li>
							<li><a href="#">Computador</a></li>
							<li><a href="#">Acessórios do kit</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h4>Resources</h4>
						<ul>
							<li><a href="#">Guides</a></li>
							<li><a href="#">Research</a></li>
							<li><a href="#">Experts</a></li>
							<li><a href="#">Agencies</a></li>
						</ul>
					</div>
				</div>
				<!--<div class="col-lg-4  col-md-6 col-sm-6">
							<div class="single-footer-widget">
								<h4>Newsletter</h4>
								<p>Stay update with our latest</p>
								<div class="" id="mc_embed_signup">
									 <form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01" method="get">
									  <div class="input-group">
									    <input type="text" class="form-control" name="EMAIL" placeholder="Enter Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Email Address '" required="" type="email">
									    <div class="input-group-btn">
									      <button class="btn btn-default" type="submit">
									        <span class="lnr lnr-arrow-right"></span>
									      </button>    
									    </div>
									    	<div class="info"></div>  
									  </div>
									</form> 
								</div>
							</div>
						</div>	-->
			</div>
			<div class="footer-bottom row align-items-center justify-content-between">
				<div class="col-lg-8 col-md-12">
					<p class="footer-text m-0">
						<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. https://colorlib.com-->
						Copyright &copy;
						<script>document.write(new Date().getFullYear());</script> Todos os direitos reservados | Criado
						com <i class="fa fa-heart-o" aria-hidden="true"></i> por <a
							href="https://www.linkedin.com/in/tom%C3%A1s-n%C3%A1poles-087517233/" target="_blank">Tomás
							Nápoles</a> &amp; <a href="https://themewagon.com" target="_blank">Salvador Coimbras</a>
						<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. https://colorlib.com-->
					</p>
				</div>
				<div class="col-lg-4 col-sm-12 footer-social">
					<a target="_blank" href="https://www.facebook.com/aebatalha/?locale=pt_PT"><i
							class="fa fa-facebook"></i></a>
					<a target="_blank" href="https://www.instagram.com/agrupamento_escolas_batalha/"><img
							class="favinsta" src="img/fa-intagram.png"></i></a>
					<a target="_blank" href="#"><i class="fa fa-dribbble"></i></a>
					<a target="_blank" href="#"><i class="fa fa-behance"></i></a>
				</div>
			</div>
		</div>
	</footer>
	<!-- End footer Area -->

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>