<?php
require_once 'functions.php';

// Verificar se o utilizador está autenticado e é um aluno
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Verificar o tipo de utilizador
require_once 'connection.php';
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Aluno') {
    header('Location: index.php');
    exit;
}

// Buscar todos os tickets do aluno
$stmt = $conn->prepare("
    SELECT t.*, 
           DATE_FORMAT(t.Data_Submissao, '%d/%m/%Y %H:%i') as Data_Submissao_Formatada,
           DATE_FORMAT(t.Data_Marcada, '%d/%m/%Y %H:%i') as Data_Marcada_Formatada
    FROM Tickets t 
    WHERE t.ID_Utilizador = ? 
    ORDER BY t.Data_Submissao DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt" class="no-js">

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
    <title>Reparações Agendadas | AEB Conecta</title>

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

    <!-- Start Banner Area -->
    <section class="banner-area relative" id="tickets-banner">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row fullscreen d-flex align-items-center justify-content-between">
                <div class="banner-content col-lg-9 col-md-12">
                    <h1 class="text-uppercase">Reparações Agendadas</h1>
                    <p class="pt-10 pb-10">Visualize o estado das suas reparações agendadas.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- End Banner Area -->

    <!-- Tickets List Area -->
    <section class="contact-area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php if (empty($tickets)): ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Ainda não tem nenhuma reparação agendada.
                            <a href="submit_ticket.php" class="alert-link">Clique aqui</a> para agendar uma reparação.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data de Submissão</th>
                                        <th>Título</th>
                                        <th>Tipo de Equipamento</th>
                                        <th>Data Agendada</th>
                                        <th>Estado</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td><?php echo $ticket['Data_Submissao_Formatada']; ?></td>
                                            <td><?php echo htmlspecialchars($ticket['Titulo']); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['Tipo_Equipamento']); ?></td>
                                            <td><?php echo $ticket['Data_Marcada_Formatada']; ?></td>
                                            <td>
                                                <?php
                                                $estadoClass = '';
                                                switch ($ticket['Estado']) {
                                                    case 'Pendente':
                                                        $estadoClass = 'warning';
                                                        break;
                                                    case 'Em Progresso':
                                                        $estadoClass = 'info';
                                                        break;
                                                    case 'Concluído':
                                                        $estadoClass = 'success';
                                                        break;
                                                    case 'Cancelado':
                                                        $estadoClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?php echo $estadoClass; ?>">
                                                    <?php echo $ticket['Estado']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#ticketModal<?php echo $ticket['ID_Ticket']; ?>">
                                                    <i class="fa fa-eye"></i> Ver Detalhes
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal para detalhes do ticket -->
                                        <div class="modal fade" id="ticketModal<?php echo $ticket['ID_Ticket']; ?>" tabindex="-1" 
                                             aria-labelledby="ticketModalLabel<?php echo $ticket['ID_Ticket']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="ticketModalLabel<?php echo $ticket['ID_Ticket']; ?>">
                                                            Detalhes da Reparação
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6>Informações Básicas</h6>
                                                                <p><strong>Título:</strong> <?php echo htmlspecialchars($ticket['Titulo']); ?></p>
                                                                <p><strong>Tipo de Equipamento:</strong> <?php echo htmlspecialchars($ticket['Tipo_Equipamento']); ?></p>
                                                                <p><strong>Número de Série:</strong> <?php echo htmlspecialchars($ticket['Numero_Serie'] ?? 'Não especificado'); ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Datas</h6>
                                                                <p><strong>Data de Submissão:</strong> <?php echo $ticket['Data_Submissao_Formatada']; ?></p>
                                                                <p><strong>Data Agendada:</strong> <?php echo $ticket['Data_Marcada_Formatada']; ?></p>
                                                                <p><strong>Estado:</strong> 
                                                                    <span class="badge bg-<?php echo $estadoClass; ?>">
                                                                        <?php echo $ticket['Estado']; ?>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h6>Descrição do Problema</h6>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo nl2br(htmlspecialchars($ticket['Descricao'])); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
        integrity="sha384-ApNb9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/extra.js" type="module"></script>

    <?php include 'modals.php'; ?>
</body>

</html> 