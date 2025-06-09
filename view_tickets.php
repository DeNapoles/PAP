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
    <style>
        .badge-estado {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            font-size: 1em;
            border-radius: 20px;
            padding: 7px 18px;
            border: 2px solid;
            background: #fff;
            gap: 7px;
            transition: color 0.18s, border-color 0.18s, background 0.18s;
        }
        .badge-estado.pendente {
            color: #ffb648;
            border-color: #ffb648;
            background: #fff;
        }
        .badge-estado.aceite {
            color: #198754;
            border-color: #198754;
            background: #e9f7ef;
        }
        .badge-estado.rejeitado {
            color: #dc3545;
            border-color: #dc3545;
            background: #fbeaea;
        }
        .badge-estado.concluido {
            color: #0d6efd;
            border-color: #0d6efd;
            background: #e7f0fd;
        }
    </style>
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
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fa fa-info-circle me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Nenhuma reparação agendada</h5>
                                <p class="mb-0">Ainda não tem nenhuma reparação agendada. <a href="submit_ticket.php" class="alert-link">Clique aqui</a> para agendar uma reparação.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">As Minhas Reparações</h5>
                            </div>
                            <div class="card-body p-0">
                        <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-3">Data de Submissão</th>
                                                <th class="py-3">Título</th>
                                                <th class="py-3">Tipo de Equipamento</th>
                                                <th class="py-3">Data Agendada</th>
                                                <th class="py-3">Estado</th>
                                                <th class="py-3 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                                    <td class="align-middle"><?php echo $ticket['Data_Submissao_Formatada']; ?></td>
                                                    <td class="align-middle fw-medium"><?php echo htmlspecialchars($ticket['Titulo']); ?></td>
                                                    <td class="align-middle"><?php echo htmlspecialchars($ticket['Tipo_Equipamento']); ?></td>
                                                    <td class="align-middle"><?php echo $ticket['Data_Marcada_Formatada']; ?></td>
                                                    <td class="align-middle">
                                                <?php
                                                $estadoClass = '';
                                                        $estadoIcon = '';
                                                switch (strtolower($ticket['Estado'])) {
                                                    case 'pendente':
                                                        $estadoClass = 'pendente';
                                                                $estadoIcon = 'fa-clock';
                                                        break;
                                                    case 'aceite':
                                                        $estadoClass = 'aceite';
                                                                $estadoIcon = 'fa-check';
                                                        break;
                                                    case 'rejeitado':
                                                        $estadoClass = 'rejeitado';
                                                                $estadoIcon = 'fa-times-circle';
                                                        break;
                                                    case 'concluído':
                                                    case 'concluido':
                                                        $estadoClass = 'concluido';
                                                                $estadoIcon = 'fa-check-circle';
                                                        break;
                                                    default:
                                                        $estadoClass = 'pendente';
                                                        $estadoIcon = 'fa-clock';
                                                }
                                                ?>
                                                        <span class="badge-estado <?= $estadoClass ?>">
                                                            <i class="fa <?= $estadoIcon ?> me-1"></i>
                                                    <?= htmlspecialchars($ticket['Estado']) ?>
                                                </span>
                                            </td>
                                                    <td class="align-middle text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#ticketModal<?php echo $ticket['ID_Ticket']; ?>">
                                                            <i class="fa fa-eye me-1"></i> Ver Detalhes
                                                        </button>
                                                        <?php if ($ticket['Estado'] === 'Pendente' || $ticket['Estado'] === 'Em Progresso' || $ticket['Estado'] === 'Aceite'): ?>
                                                            <button type="button" class="btn btn-outline-danger btn-sm btn-cancel-ticket mt-1 mt-md-0 ms-md-1" 
                                                                    data-ticket-id="<?php echo $ticket['ID_Ticket']; ?>"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#confirmCancelModal">
                                                                <i class="fa fa-times-circle me-1"></i> Cancelar
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($ticket['Estado'] !== 'Concluído' && $ticket['Estado'] !== 'Cancelado'): ?>
                                                             <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-date mt-1 mt-md-2 ms-md-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editDateModal"
                                                                    data-ticket-id="<?php echo $ticket['ID_Ticket']; ?>"
                                                                    data-current-date="<?php echo htmlspecialchars($ticket['Data_Marcada']); ?>">
                                                                <i class="fa fa-calendar me-1"></i> Mudar Data
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                        </tr>

                                        <!-- Modal para detalhes do ticket -->
                                        <div class="modal fade" id="ticketModal<?php echo $ticket['ID_Ticket']; ?>" tabindex="-1" 
                                             aria-labelledby="ticketModalLabel<?php echo $ticket['ID_Ticket']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                            <div class="modal-header bg-light">
                                                        <h5 class="modal-title" id="ticketModalLabel<?php echo $ticket['ID_Ticket']; ?>">
                                                                    <i class="fa fa-ticket-alt me-2"></i>
                                                            Detalhes da Reparação
                                                        </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-4">
                                                                    <div class="col-md-6">
                                                                        <div class="card h-100 border-0 shadow-sm">
                                                                            <div class="card-body">
                                                                                <h6 class="card-title text-primary mb-3">
                                                                                    <i class="fa fa-info-circle me-2"></i>
                                                                                    Informações Básicas
                                                                                </h6>
                                                                                <p class="mb-2"><strong>Título:</strong> <?php echo htmlspecialchars($ticket['Titulo']); ?></p>
                                                                                <p class="mb-2"><strong>Tipo de Equipamento:</strong> <?php echo htmlspecialchars($ticket['Tipo_Equipamento']); ?></p>
                                                                                <p class="mb-0"><strong>Número de Série:</strong> <?php echo htmlspecialchars($ticket['Numero_Serie'] ?? 'Não especificado'); ?></p>
                                                                            </div>
                                                                        </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                        <div class="card h-100 border-0 shadow-sm">
                                                                            <div class="card-body">
                                                                                <h6 class="card-title text-primary mb-3">
                                                                                    <i class="fa fa-calendar me-2"></i>
                                                                                    Datas e Estado
                                                                                </h6>
                                                                                <p class="mb-2"><strong>Data de Submissão:</strong> <?php echo $ticket['Data_Submissao_Formatada']; ?></p>
                                                                                <p class="mb-2"><strong>Data Agendada:</strong> <?php echo $ticket['Data_Marcada_Formatada']; ?></p>
                                                                                <p class="mb-0"><strong>Estado:</strong> 
                                                                                    <span class="badge-estado <?= $estadoClass ?>">
                                                                                        <i class="fa <?= $estadoIcon ?> me-1"></i>
                                                                        <?= htmlspecialchars($ticket['Estado']) ?>
                                                                    </span>
                                                                </p>
                                                                            </div>
                                                                        </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                        <div class="card border-0 shadow-sm">
                                                                            <div class="card-body">
                                                                                <h6 class="card-title text-primary mb-3">
                                                                                    <i class="fa fa-comment me-2"></i>
                                                                                    Descrição do Problema
                                                                                </h6>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo nl2br(htmlspecialchars($ticket['Descricao'])); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fa fa-times me-1"></i>
                                                                    Fechar
                                                                </button>
                                                            </div>
                                                        </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                                </div>
                            </div>
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

    <!-- Modal de Confirmação de Cancelamento -->
    <div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="confirmCancelModalLabel"><i class="fa fa-exclamation-triangle me-2"></i> Confirmar Cancelamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem a certeza que deseja cancelar esta reparação?
                    Esta ação não pode ser desfeita.
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Fechar</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn"><i class="fa fa-check me-1"></i> Sim, Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmCancelModal = document.getElementById('confirmCancelModal');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');
            let ticketIdToCancel = null;

            confirmCancelModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                // Extract info from data-bs-* attributes
                ticketIdToCancel = button.getAttribute('data-ticket-id');
            });

            confirmCancelBtn.addEventListener('click', function () {
                if (ticketIdToCancel) {
                    // Redirect or make an AJAX call to cancel_ticket.php
                    // For simplicity, we'll use a direct link here. A proper AJAX call is recommended for better UX.
                    window.location.href = 'cancel_ticket.php?id=' + ticketIdToCancel;

                    // If using AJAX (more advanced, better UX):
                    /*
                    fetch('cancel_ticket.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'ticket_id=' + ticketIdToCancel
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            alert('Reparação cancelada com sucesso!');
                            // Optionally remove the row from the table
                            // button.closest('tr').remove();
                             location.reload(); // Or reload the page
                        } else {
                            alert('Erro ao cancelar reparação: ' + data.message);
                        }
                        const modal = bootstrap.Modal.getInstance(confirmCancelModal);
                        modal.hide();
                    })
                    .catch((error) => {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro ao comunicar com o servidor.');
                        const modal = bootstrap.Modal.getInstance(confirmCancelModal);
                        modal.hide();
                    });
                    */
                }
            });
        });
    </script>

    <!-- Modal para Editar Data -->
    <div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="editDateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="editDateModalLabel"><i class="fa fa-calendar-alt me-2"></i> Editar Data Agendada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-ticket-id">
                    <div class="form-group mb-3">
                        <label for="new-data-marcada" class="form-label">Nova Data e Hora Agendada:</label>
                        <input type="datetime-local" class="form-control" id="new-data-marcada" required>
                    </div>
                    <div id="edit-date-feedback" class="mt-3 alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveNewDateBtn"><i class="fa fa-save me-1"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editDateModal = document.getElementById('editDateModal');
            const saveNewDateBtn = document.getElementById('saveNewDateBtn');
            const editTicketIdInput = document.getElementById('edit-ticket-id');
            const newDataMarcadaInput = document.getElementById('new-data-marcada');
            const editDateFeedback = document.getElementById('edit-date-feedback');

            // Adiciona event listeners aos botões "Editar Data"
            const editDateButtons = document.querySelectorAll('.btn-edit-date');
            editDateButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const ticketId = this.getAttribute('data-ticket-id');
                    const currentDate = this.getAttribute('data-current-date');

                    editTicketIdInput.value = ticketId;

                    // Formatar a data para o input datetime-local (YYYY-MM-DDTHH:MM)
                    // A data vinda do PHP está em formato 'YYYY-MM-DD HH:MM:SS' ou 'YYYY-MM-DD HH:MM'
                    // Precisa ser convertida. Vamos assumir 'YYYY-MM-DD HH:MM:SS' vindo do PHP.
                    if (currentDate) {
                        const dateParts = currentDate.split(' ');
                        const date = dateParts[0];
                        const time = dateParts[1].substring(0, 5); // Pega apenas HH:MM
                        newDataMarcadaInput.value = `${date}T${time}`;
                    } else {
                        newDataMarcadaInput.value = ''; // Limpa se não houver data agendada
                    }

                    // Limpa feedback anterior
                    editDateFeedback.classList.add('d-none');
                    editDateFeedback.textContent = '';
                    editDateFeedback.classList.remove('alert-success', 'alert-danger');
                });
            });

            // Adiciona event listener ao botão "Guardar" no modal
            saveNewDateBtn.addEventListener('click', async function () {
                const ticketId = editTicketIdInput.value;
                const newDataMarcada = newDataMarcadaInput.value;

                if (!newDataMarcada) {
                    editDateFeedback.classList.remove('d-none', 'alert-success');
                    editDateFeedback.classList.add('alert-danger');
                    editDateFeedback.textContent = 'Por favor, selecione uma nova data e hora.';
                    return;
                }

                // Envia os dados para o novo ficheiro PHP via AJAX
                try {
                    const response = await fetch('update_ticket_date.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ticket_id=${ticketId}&new_data_marcada=${newDataMarcada}`
                    });

                    const data = await response.json();

                    editDateFeedback.classList.remove('d-none', 'alert-success', 'alert-danger');
                    if (data.success) {
                        editDateFeedback.classList.add('alert-success');
                        editDateFeedback.textContent = data.message;
                        // Atualiza a tabela após sucesso (opcional, pode só recarregar a página)
                        // Para simplificar, vamos recarregar a página após um pequeno atraso.
                        setTimeout(() => {
                            location.reload();
                        }, 2000); // Recarrega após 2 segundos

                    } else {
                        editDateFeedback.classList.add('alert-danger');
                        editDateFeedback.textContent = data.message;
                    }

                } catch (error) {
                    console.error('Erro na atualização:', error);
                    editDateFeedback.classList.remove('d-none', 'alert-success');
                    editDateFeedback.classList.add('alert-danger');
                    editDateFeedback.textContent = 'Ocorreu um erro ao comunicar com o servidor.';
                }
            });
        });
    </script>
</body>

</html> 