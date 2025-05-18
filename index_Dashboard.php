<?php
require_once 'connection.php';

// Função para buscar os dados da tabela InicioInicio
function getCapaData() {
    global $conn;
    $sql = "SELECT * FROM InicioInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar os dados da tabela CTAInicio
function getCTAInicioData() {
    global $conn;
    $sql = "SELECT * FROM CTAInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar os dados da tabela LigacoesRapidasInicio
function getLigacoesRapidasData() {
    global $conn;
    $sql = "SELECT * FROM LigacoesRapidasInicio ORDER BY id";
    $result = $conn->query($sql);
    $ligacoes = array();
    while($row = $result->fetch_assoc()) {
        $ligacoes[] = $row;
    }
    return $ligacoes;
}

// Função para buscar os dados da tabela SobreNosInicio
function getSobreNosData() {
    global $conn;
    $sql = "SELECT * FROM SobreNosInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para atualizar os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_capa'])) {
    $logoSeparador = $_POST['LogoSeparador'];
    $logoPrincipal = $_POST['LogoPrincipal'];
    $textoBemvindo = $_POST['TextoBemvindo'];
    $textoInicial = $_POST['TextoInicial'];
    $textoInicial2 = $_POST['TextoInicial2'];
    $botaoInicial = $_POST['BotaoInicial'];
    $fundo = $_POST['Fundo'];

    $sql = "UPDATE InicioInicio SET 
            LogoSeparador = ?,
            LogoPrincipal = ?,
            TextoBemvindo = ?,
            TextoInicial = ?,
            TextoInicial2 = ?,
            BotaoInicial = ?,
            Fundo = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", 
        $logoSeparador,
        $logoPrincipal,
        $textoBemvindo,
        $textoInicial,
        $textoInicial2,
        $botaoInicial,
        $fundo
    );

    if ($stmt->execute()) {
        $updateMessage = "Dados atualizados com sucesso!";
        $updateType = "success";
    } else {
        $updateMessage = "Erro ao atualizar dados: " . $conn->error;
        $updateType = "danger";
    }
}

// Processar atualização das ligações rápidas
if (isset($_POST['update_links'])) {
    try {
        // Limpar a tabela atual
        $stmt = $conn->prepare("TRUNCATE TABLE LigacoesRapidasInicio");
        $stmt->execute();

        // Inserir as novas ligações
        if (isset($_POST['links']) && is_array($_POST['links'])) {
            $stmt = $conn->prepare("INSERT INTO LigacoesRapidasInicio (Nome, Link, Imagem, Largura, Altura) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($_POST['links'] as $link) {
                $largura = !empty($link['Largura']) ? $link['Largura'] : null;
                $altura = !empty($link['Altura']) ? $link['Altura'] : null;
                
                $stmt->execute([
                    $link['Nome'],
                    $link['Link'],
                    $link['Imagem'],
                    $largura,
                    $altura
                ]);
            }
        }

        $updateMessage = "Ligações rápidas atualizadas com sucesso!";
        $updateType = "success";
    } catch (PDOException $e) {
        $updateMessage = "Erro ao atualizar ligações rápidas: " . $e->getMessage();
        $updateType = "danger";
    }
}

// Processar atualização do Login Rápido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_login'])) {
    $titulo = $_POST['Titulo'];
    $texto = $_POST['texto'];
    $btntext = $_POST['btntext'];
    $fundo = $_POST['fundo'];

    $sql = "UPDATE CTAInicio SET 
            Titulo = ?,
            texto = ?,
            btntext = ?,
            fundo = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", 
        $titulo,
        $texto,
        $btntext,
        $fundo
    );

    if ($stmt->execute()) {
        $updateMessage = "Dados do Login Rápido atualizados com sucesso!";
        $updateType = "success";
    } else {
        $updateMessage = "Erro ao atualizar dados do Login Rápido: " . $conn->error;
        $updateType = "danger";
    }
}

// Adicionar o processamento do formulário Sobre Nós
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_sobre'])) {
    $texto1 = $_POST['Texto1'];
    $texto2 = $_POST['Texto2'];
    $imagem = $_POST['Imagem'];

    $sql = "UPDATE SobreNosInicio SET 
            Texto1 = ?,
            Texto2 = ?,
            Imagem = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", 
        $texto1,
        $texto2,
        $imagem
    );

    if ($stmt->execute()) {
        $updateMessage = "Dados atualizados com sucesso!";
        $updateType = "success";
    } else {
        $updateMessage = "Erro ao atualizar dados: " . $conn->error;
        $updateType = "danger";
    }
}

$capaData = getCapaData();
$sobreNos = getSobreNosData();
$ctaInicio = getCTAInicioData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard AEBConecta</title>
  <!-- Favicon -->
  <link rel="shortcut icon" href="img/logo2AEBConecta.png" type="image/x-icon">
  <!-- Custom styles -->
  <link rel="stylesheet" href="./css/style.min.css">
  <style>
    .content-section {
      display: none;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .content-section .card {
      border: none;
      box-shadow: none;
    }

    .content-section .card-header {
      background: none;
      border-bottom: 1px solid #eee;
      padding: 15px 0;
    }

    .content-section .card-title {
      margin: 0;
      color: #333;
      font-size: 1.25rem;
    }

    .content-section .card-body {
      padding: 20px 0;
    }

    .content-section .form-label {
      font-weight: 500;
      color: #555;
    }

    .content-section .form-control {
      border-radius: 4px;
      border: 1px solid #ddd;
    }

    .content-section .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .content-section .btn {
      padding: 8px 20px;
      font-weight: 500;
    }

    .content-section .alert {
      margin-bottom: 20px;
    }

    .image-upload-container {
        margin-top: 10px;
    }

    .image-preview {
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f8f9fa;
        text-align: center;
    }

    .image-preview img {
        display: block;
        margin: 0 auto;
    }

    .drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .drop-zone:hover {
        border-color: #0d6efd;
        background-color: #e9ecef;
    }

    .drop-zone.dragover {
        border-color: #198754;
        background-color: #d1e7dd;
    }

    .drop-zone i {
        font-size: 24px;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .image-preview {
        text-align: center;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }

    .image-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
    }

    /* Estilos para a seção de ligações rápidas */
    .link-item {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        padding: 18px 18px 12px 18px;
        margin: 0;
        min-width: 270px;
        max-width: 340px;
        flex: 1 1 270px;
        position: relative;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
    }

    .link-item:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.13);
    }

    .link-item .remove-link-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        border: none;
        color: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.85;
        transition: background 0.2s, opacity 0.2s;
        z-index: 2;
    }

    .link-item .remove-link-btn:hover {
        background: #b02a37;
        opacity: 1;
    }

    .link-item h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 12px;
        margin-top: 0;
        color: #0d6efd;
        padding-right: 36px;
    }

    @media (max-width: 900px) {
        #linksContainer {
            flex-direction: column;
            gap: 18px;
        }
        .link-item {
            max-width: 100%;
            min-width: 0;
        }
    }

    .link-item .row.g-2 .form-control[type="number"] {
        max-width: 90px;
        min-width: 60px;
        width: 100%;
        display: inline-block;
    }
    .link-item .row.g-2 {
        justify-content: flex-start;
        gap: 0;
    }
    @media (max-width: 600px) {
        .link-item .row.g-2 .form-control[type="number"] {
            max-width: 100%;
        }
    }
  </style>
</head>

<body>
  <div class="layer"></div>
<!-- ! Body -->
<a class="skip-link sr-only" href="#skip-target">Skip to content</a>
<div class="page-flex">



  <!-- ============================================ Start Sidebar ============================================ -->
  <aside class="sidebar">
    <div class="sidebar-start">
        <div class="sidebar-head">
            <a href="/" class="logo-wrapper" title="Home">
                <div class="logo-text">
                  <a href="index_Dashboard.html"><img src="img/logo1AEBConecta.png" alt="logo" title="" /></a>
                    <span class="logo-subtitle">Painel de Controlo</span>
                </div>
            </a>
            <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                <span class="sr-only">Toggle menu</span>
                <span class="icon menu-toggle" aria-hidden="true"></span>
            </button>
        </div>
        <div class="sidebar-body">
            <ul class="sidebar-body-menu">
                <li>
                    <a class="active" href="index_Dashboard.php"><span class="icon home" aria-hidden="true"></span>Painel de Controlo</a>
                </li>
                <li>
                  <a class="show-cat-btn" href="javascript:void(0);">
                      <span class="icon image" aria-hidden="true"></span>Página principal
                      <span class="category__btn transparent-btn" title="Open list">
                          <span class="sr-only">Open list</span>
                          <span class="icon arrow-down" aria-hidden="true"></span>
                      </span>
                  </a>
                  <ul class="cat-sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('inicio-section')">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('links-section')">
                            <i class="fas fa-link"></i> Ligações Rápidas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('sobre-section')">
                            <i class="fas fa-info-circle"></i> Sobre nós
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('avaliacoes-section')">
                            <i class="fas fa-star"></i> Avaliações
                        </a>
                    </li>
                    <li>
                      <a class="nav-link" href="#" onclick="showSection('login-section')">
                          <i class="fas fa-sign-in-alt"></i> Login Rápido
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0);" data-section="faqs">Main FAQ's</a>
                    </li>
                    <li>
                      <a href="javascript:void(0);" data-section="aviso">Aviso</a>
                    </li>
                  </ul>
              </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon paper" aria-hidden="true"></span>FAQ's
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="pages.html">Capa</a>
                        </li>
                        <li>
                            <a href="manage_posts.php">FAQ's</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="comments.html">
                        <span class="icon message" aria-hidden="true"></span> Footer </a>
                </li>
            </ul>
            <span class="system-menu__title">system</span>
            <ul class="sidebar-body-menu">
                <li>
                    <a href="appearance.html"><span class="icon edit" aria-hidden="true"></span>Appearance</a>
                </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon category" aria-hidden="true"></span>Extentions
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="extention-01.html">Extentions-01</a>
                        </li>
                        <li>
                            <a href="extention-02.html">Extentions-02</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon user-3" aria-hidden="true"></span>Users
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="users-01.html">Users-01</a>
                        </li>
                        <li>
                            <a href="users-02.html">Users-02</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="##"><span class="icon setting" aria-hidden="true"></span>Settings</a>
                </li>
            </ul>
        </div>
    </div>
    
</aside>
  <div class="main-wrapper">

      <!-- ============================================ End Sidebar ============================================ -->

    <!-- ============================================ Start Main nav ============================================ -->
    <nav class="main-nav--bg">
  <div class="container main-nav">
    <div class="main-nav-start">
      <div class="search-wrapper">
        <i data-feather="search" aria-hidden="true"></i>
        <input type="text" placeholder="Enter keywords ..." required>
      </div>
    </div>
    <div class="main-nav-end">
      <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
        <span class="sr-only">Toggle menu</span>
        <span class="icon menu-toggle--gray" aria-hidden="true"></span>
      </button>
    
      <button class="theme-switcher gray-circle-btn" type="button" title="Switch theme">
        <span class="sr-only">Switch theme</span>
        <i class="sun-icon" data-feather="sun" aria-hidden="true"></i>
        <i class="moon-icon" data-feather="moon" aria-hidden="true"></i>
      </button>
      <div class="notification-wrapper">
        <button class="gray-circle-btn dropdown-btn" title="To messages" type="button">
          <span class="sr-only">To messages</span>
          <span class="icon notification active" aria-hidden="true"></span>
        </button>
        <ul class="users-item-dropdown notification-dropdown dropdown">
          <li>
            <a href="##">
              <div class="notification-dropdown-icon info">
                <i data-feather="check"></i>
              </div>
              <div class="notification-dropdown-text">
                <span class="notification-dropdown__title">System just updated</span>
                <span class="notification-dropdown__subtitle">The system has been successfully upgraded. Read more
                  here.</span>
              </div>
            </a>
          </li>
          <li>
            <a href="##">
              <div class="notification-dropdown-icon danger">
                <i data-feather="info" aria-hidden="true"></i>
              </div>
              <div class="notification-dropdown-text">
                <span class="notification-dropdown__title">The cache is full!</span>
                <span class="notification-dropdown__subtitle">Unnecessary caches take up a lot of memory space and
                  interfere ...</span>
              </div>
            </a>
          </li>
          <li>
            <a href="##">
              <div class="notification-dropdown-icon info">
                <i data-feather="check" aria-hidden="true"></i>
              </div>
              <div class="notification-dropdown-text">
                <span class="notification-dropdown__title">New Subscriber here!</span>
                <span class="notification-dropdown__subtitle">A new subscriber has subscribed.</span>
              </div>
            </a>
          </li>
          <li>
            <a class="link-to-page" href="##">Go to Notifications page</a>
          </li>
        </ul>
      </div>
      <div class="nav-user-wrapper">
        <button href="##" class="nav-user-btn dropdown-btn" title="My profile" type="button">
          <span class="sr-only">My profile</span>
          <span class="nav-user-img">
            <picture><source srcset="./img/avatar/avatar-illustrated-02.webp" type="image/webp"><img src="./img/avatar/avatar-illustrated-02.png" alt="User name"></picture>
          </span>
        </button>
        <ul class="users-item-dropdown nav-user-dropdown dropdown">
          <li><a href="##">
              <i data-feather="user" aria-hidden="true"></i>
              <span>Profile</span>
            </a></li>
          <li><a href="##">
              <i data-feather="settings" aria-hidden="true"></i>
              <span>Account settings</span>
            </a></li>
          <li><a class="danger" href="##">
              <i data-feather="log-out" aria-hidden="true"></i>
              <span>Log out</span>
            </a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
    <!-- ============================================ End Main nav ============================================ -->

    <!-- ============================================ Start Main Content ============================================ -->
    <main class="main users chart-page" id="skip-target">
        <div class="container">
            <!-- Mensagem de Boas-vindas (visível por padrão) -->
            <div id="welcome-section" class="content-section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bem-vindo ao Painel de Controlo</h3>
                                </div>
                                <div class="card-body">
                                    <p>Selecione uma opção no menu "Página Principal" para começar a editar o conteúdo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Início (escondida por padrão) -->
            <div id="inicio-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Conteúdo do Início</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="inicioForm">
                                        <div class="mb-3">
                                            <label for="LogoSeparador" class="form-label">Logo Separador</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control" id="LogoSeparador" name="LogoSeparador" 
                                                       value="<?php echo htmlspecialchars($capaData['LogoSeparador']); ?>">
                                                <div class="image-preview" id="LogoSeparadorPreview">
                                                    <img src="<?php echo htmlspecialchars($capaData['LogoSeparador']); ?>" alt="Logo Separador Preview" 
                                                         onerror="this.style.display='none'" style="max-width: 100px; max-height: 100px;">
                                                </div>
                                                <div class="drop-zone" data-target="LogoSeparador">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="LogoPrincipal" class="form-label">Logo Principal</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control" id="LogoPrincipal" name="LogoPrincipal" 
                                                       value="<?php echo htmlspecialchars($capaData['LogoPrincipal']); ?>">
                                                <div class="image-preview" id="LogoPrincipalPreview">
                                                    <img src="<?php echo htmlspecialchars($capaData['LogoPrincipal']); ?>" alt="Logo Principal Preview" 
                                                         onerror="this.style.display='none'" style="max-width: 100px; max-height: 100px;">
                                                </div>
                                                <div class="drop-zone" data-target="LogoPrincipal">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-input-container">
                                            <div class="form-group">
                                                <label for="TextoBemvindo" class="form-label">Texto de Boas-vindas</label>
                                                <textarea class="form-control" id="TextoBemvindo" name="TextoBemvindo" rows="3" placeholder="Digite o texto de boas-vindas..."><?php echo htmlspecialchars($capaData['TextoBemvindo']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="TextoInicial" class="form-label">Texto Inicial</label>
                                                <textarea class="form-control" id="TextoInicial" name="TextoInicial" rows="3" placeholder="Digite o texto inicial..."><?php echo htmlspecialchars($capaData['TextoInicial']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="TextoInicial2" class="form-label">Texto Inicial 2</label>
                                                <textarea class="form-control" id="TextoInicial2" name="TextoInicial2" rows="4" placeholder="Digite o texto inicial 2..."><?php echo htmlspecialchars($capaData['TextoInicial2']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="BotaoInicial" class="form-label">Texto do Botão</label>
                                                <input type="text" class="form-control" id="BotaoInicial" name="BotaoInicial" 
                                                       value="<?php echo htmlspecialchars($capaData['BotaoInicial']); ?>"
                                                       placeholder="Digite o texto do botão...">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="Fundo" class="form-label">Imagem de Fundo</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control" id="Fundo" name="Fundo" 
                                                       value="<?php echo htmlspecialchars($capaData['Fundo']); ?>">
                                                <div class="image-preview" id="FundoPreview">
                                                    <img src="<?php echo htmlspecialchars($capaData['Fundo']); ?>" alt="Fundo Preview" 
                                                         onerror="this.style.display='none'" style="max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div class="drop-zone" data-target="Fundo">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="submit" name="update_capa" class="btn btn-primary">Salvar Alterações</button>
                                            <button type="button" class="btn btn-danger" onclick="clearAllFields()">Apagar tudo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Ligações Rápidas (escondida por padrão) -->
            <div id="links-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Editar Ligações Rápidas</h3>
                                    <div>
                                        <button type="button" class="btn btn-success me-2" onclick="addNewLink()">
                                            <i class="fas fa-plus"></i> Nova Ligação
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="clearAllLinks()">
                                            <i class="fas fa-trash"></i> Apagar Tudo
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="linksForm">
                                        <div id="linksContainer" class="row" style="display: flex; flex-wrap: wrap; gap: 24px;">
                                            <?php 
                                            $ligacoesRapidas = getLigacoesRapidasData();
                                            foreach ($ligacoesRapidas as $index => $ligacao): 
                                            ?>
                                            <div class="link-item">
                                                <button type="button" class="remove-link-btn" onclick="removeLink(this)" title="Remover ligação">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <h4 class="mb-2">Ligação <?php echo $index + 1; ?></h4>
                                                <div class="mb-2">
                                                    <label class="form-label">Nome</label>
                                                    <input type="text" class="form-control" name="links[<?php echo $index; ?>][Nome]" 
                                                           value="<?php echo htmlspecialchars($ligacao['Nome']); ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Link</label>
                                                    <input type="url" class="form-control" name="links[<?php echo $index; ?>][Link]" 
                                                           value="<?php echo htmlspecialchars($ligacao['Link']); ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Imagem</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control mb-2" name="links[<?php echo $index; ?>][Imagem]" 
                                                               value="<?php echo htmlspecialchars($ligacao['Imagem']); ?>" required>
                                                        <div class="image-preview mb-2">
                                                            <img src="<?php echo htmlspecialchars($ligacao['Imagem']); ?>" alt="Preview" 
                                                                 value="<?php echo htmlspecialchars($ligacao['Imagem']); ?>"
                                                                 onerror="this.style.display='none'" style="max-width: 100px; max-height: 100px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="link_<?php echo $index; ?>">
                                                            <i class="fas fa-cloud-upload-alt mb-2"></i>
                                                            <p class="mb-0">Arraste uma imagem aqui ou clique para selecionar</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-2">
                                                        <label class="form-label">Largura (px)</label>
                                                        <input type="number" class="form-control" name="links[<?php echo $index; ?>][Largura]" 
                                                               value="<?php echo htmlspecialchars($ligacao['Largura'] ?? ''); ?>"
                                                               placeholder="Ex: 200">
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label class="form-label">Altura (px)</label>
                                                        <input type="number" class="form-control" name="links[<?php echo $index; ?>][Altura]" 
                                                               value="<?php echo htmlspecialchars($ligacao['Altura'] ?? ''); ?>"
                                                               placeholder="Ex: 200">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" name="update_links" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Sobre Nós (escondida por padrão) -->
            <div id="sobre-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Conteúdo Sobre Nós</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="sobreForm">
                                        <div class="text-input-container">
                                            <div class="form-group">
                                                <label for="Texto1" class="form-label">Primeiro Texto</label>
                                                <textarea class="form-control" id="Texto1" name="Texto1" rows="4" 
                                                          placeholder="Digite o primeiro texto..."><?php echo htmlspecialchars($sobreNos['Texto1']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="Texto2" class="form-label">Segundo Texto</label>
                                                <textarea class="form-control" id="Texto2" name="Texto2" rows="4" 
                                                          placeholder="Digite o segundo texto..."><?php echo htmlspecialchars($sobreNos['Texto2']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="Imagem" class="form-label">Imagem</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control" id="Imagem" name="Imagem" 
                                                       value="<?php echo htmlspecialchars($sobreNos['Imagem']); ?>">
                                                <div class="image-preview" id="ImagemPreview">
                                                    <img src="<?php echo htmlspecialchars($sobreNos['Imagem']); ?>" alt="Imagem Preview" 
                                                         onerror="this.style.display='none'" style="max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div class="drop-zone" data-target="Imagem">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="submit" name="update_sobre" class="btn btn-primary">Salvar Alterações</button>
                                            <button type="button" class="btn btn-danger" onclick="clearSobreFields()">Apagar tudo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Avaliações (escondida por padrão) -->
            <div id="avaliacoes-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Editar Avaliações</h3>
                                    <div>
                                        <button type="button" class="btn btn-success me-2" onclick="addNewAvaliacao()">
                                            <i class="fas fa-plus"></i> Nova Avaliação
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="clearAllAvaliacoes()">
                                            <i class="fas fa-trash"></i> Apagar Tudo
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    // Processamento do formulário de avaliações
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_avaliacoes'])) {
                                        $avaliacoes = $_POST['avaliacoes'] ?? [];
                                        // Limpa a tabela
                                        $conn->query("TRUNCATE TABLE TabelaAvaliacoesInicio");
                                        // Insere as avaliações na nova ordem
                                        $stmt = $conn->prepare("INSERT INTO TabelaAvaliacoesInicio (Nome, Estrelas, Texto) VALUES (?, ?, ?)");
                                        foreach ($avaliacoes as $avaliacao) {
                                            $nome = $avaliacao['Nome'] ?? '';
                                            $estrelas = (int)($avaliacao['Estrelas'] ?? 0);
                                            $texto = $avaliacao['Texto'] ?? '';
                                            $stmt->bind_param("sis", $nome, $estrelas, $texto);
                                            $stmt->execute();
                                        }
                                        $updateMessage = "Avaliações atualizadas com sucesso!";
                                        $updateType = "success";
                                    }
                                    $avaliacoesData = [];
                                    $result = $conn->query("SELECT * FROM TabelaAvaliacoesInicio ORDER BY id");
                                    while($row = $result->fetch_assoc()) {
                                        $avaliacoesData[] = $row;
                                    }
                                    ?>
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    <form method="POST" id="avaliacoesForm">
                                        <div id="avaliacoesContainer" class="row" style="display: flex; flex-wrap: wrap; gap: 24px;">
                                            <?php foreach ($avaliacoesData as $index => $avaliacao): ?>
                                            <div class="avaliacao-item" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;">
                                                <button type="button" class="remove-link-btn" onclick="removeAvaliacao(this)" title="Remover avaliação" style="position: absolute; top: 10px; right: 10px; background: #dc3545; border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; opacity: 0.85; transition: background 0.2s, opacity 0.2s; z-index: 2;"><i class="fas fa-trash"></i></button>
                                                <h4 class="mb-2" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd; padding-right: 36px;">Avaliação <?php echo $index + 1; ?></h4>
                                                <div class="mb-2">
                                                    <label class="form-label">Nome</label>
                                                    <input type="text" class="form-control" name="avaliacoes[<?php echo $index; ?>][Nome]" value="<?php echo htmlspecialchars($avaliacao['Nome']); ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Avaliação</label>
                                                    <div class="star-rating" data-rating="<?php echo htmlspecialchars($avaliacao['Estrelas']); ?>">
                                                        <span class="star" data-value="1">★</span>
                                                        <span class="star" data-value="2">★</span>
                                                        <span class="star" data-value="3">★</span>
                                                        <span class="star" data-value="4">★</span>
                                                        <span class="star" data-value="5">★</span>
                                                        <span class="rating-value"><?php echo htmlspecialchars($avaliacao['Estrelas']); ?>/5</span>
                                                        <input type="hidden" name="avaliacoes[<?php echo $index; ?>][Estrelas]" value="<?php echo htmlspecialchars($avaliacao['Estrelas']); ?>">
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Texto</label>
                                                    <textarea class="form-control" name="avaliacoes[<?php echo $index; ?>][Texto]" rows="3" required><?php echo htmlspecialchars($avaliacao['Texto']); ?></textarea>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" name="update_avaliacoes" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Login Rápido (escondida por padrão) -->
            <div id="login-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Conteúdo do Login Rápido</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="loginForm">
                                        <div class="text-input-container">
                                            <div class="form-group">
                                                <label for="Titulo" class="form-label">Título</label>
                                                <input type="text" class="form-control" id="Titulo" name="Titulo" 
                                                       value="<?php echo htmlspecialchars($ctaInicio['Titulo']); ?>"
                                                       placeholder="Digite o título...">
                                            </div>

                                            <div class="form-group">
                                                <label for="texto" class="form-label">Texto</label>
                                                <textarea class="form-control" id="texto" name="texto" rows="4" 
                                                          placeholder="Digite o texto..."><?php echo htmlspecialchars($ctaInicio['texto']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="btntext" class="form-label">Texto do Botão</label>
                                                <input type="text" class="form-control" id="btntext" name="btntext" 
                                                       value="<?php echo htmlspecialchars($ctaInicio['btntext']); ?>"
                                                       placeholder="Digite o texto do botão...">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fundo" class="form-label">Imagem de Fundo</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control" id="fundo" name="fundo" 
                                                       value="<?php echo htmlspecialchars($ctaInicio['fundo']); ?>">
                                                <div class="image-preview" id="fundoPreview">
                                                    <img src="<?php echo htmlspecialchars($ctaInicio['fundo']); ?>" alt="Fundo Preview" 
                                                         onerror="this.style.display='none'" style="max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div class="drop-zone" data-target="fundo">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="submit" name="update_login" class="btn btn-primary">Salvar Alterações</button>
                                            <button type="button" class="btn btn-danger" onclick="clearLoginFields()">Apagar tudo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outras seções serão adicionadas aqui -->
        </div>
    </main>
    <!-- ============================================ End Main Content ============================================ -->

    <!-- ============================================ Start Footer ============================================ -->
    <footer class="footer">
      <div class="container footer--flex">
        <div class="footer-start">
          <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> 
            Todos os direitos reservados | Criado com <i class="fa fa-heart-o" aria-hidden="true"></i> por 
            <a href="https://www.linkedin.com/in/tom%C3%A1s-n%C3%A1poles-087517233/" target="_blank">Tomás Nápoles
            </a> &amp; <a href="#" target="_blank">Salvador Coimbras</a></a></p>
        </div>
        <ul class="footer-end">
          <li><a href="##">About</a></li>
          <li><a href="##">Support</a></li>
          <li><a href="##">Puchase</a></li>
        </ul>
      </div>
    </footer>
  </div>
</div>

<!-- Modal para renomear imagem -->
<div class="modal fade" id="renameImageModal" tabindex="-1" aria-labelledby="renameImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameImageModalLabel">Renomear Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="imageName" class="form-label">Nome da Imagem</label>
                    <input type="text" class="form-control" id="imageName" placeholder="Digite o nome da imagem">
                    <small class="text-muted">Não inclua a extensão do arquivo</small>
                </div>
                <div class="image-preview text-center mb-3">
                    <img id="modalImagePreview" src="" alt="Preview" style="max-width: 200px; max-height: 200px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmRename">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery primeiro -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle com Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart library -->
<script src="./plugins/chart.min.js"></script>

<!-- Icons library -->
<script src="plugins/feather.min.js"></script>

<!-- Custom scripts -->
<script src="js/script.js"></script>

<!-- Dashboard script -->
<script src="js/dashboard.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada');
    
    // Inicializa os ícones do Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Função para mostrar/esconder seções
    function showSection(sectionId) {
        // Esconde todas as seções
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Mostra a seção selecionada
        const selectedSection = document.getElementById(sectionId);
        if (selectedSection) {
            selectedSection.style.display = 'block';
            console.log('Seção mostrada:', sectionId);
        } else {
            console.error('Seção não encontrada:', sectionId);
        }
    }

    // Adiciona event listeners aos links do menu
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            showSection(sectionId);
            console.log('Link clicado:', sectionId);
        });
    });

    // Mostra a seção de boas-vindas por padrão
    showSection('welcome-section');

    // Função para atualizar preview da imagem
    function updateImagePreview(inputId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(inputId + 'Preview').querySelector('img');
        
        if (input.value) {
            preview.src = input.value;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    // Variáveis globais para armazenar o arquivo e input atual
    let currentFile = null;
    let currentInputId = null;

    // Função para lidar com upload de arquivo
    function handleFileSelect(e, inputId) {
        const file = e.target.files[0];
        if (file) {
            currentFile = file;
            currentInputId = inputId;
            showRenameModal(file);
        }
    }

    // Função para mostrar o modal de renomeação
    function showRenameModal(file) {
        const modal = new bootstrap.Modal(document.getElementById('renameImageModal'));
        const preview = document.getElementById('modalImagePreview');
        const nameInput = document.getElementById('imageName');
        
        // Mostra preview da imagem
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Define o nome padrão (nome do arquivo sem extensão)
        const defaultName = file.name.replace(/\.[^/.]+$/, "");
        nameInput.value = defaultName;
        
        modal.show();
    }

    // Função para fazer upload do arquivo
    async function handleFileUpload(file, inputId, customName = null) {
        if (file && file.type.startsWith('image/')) {
            const formData = new FormData();
            formData.append('image', file);
            
            // Adiciona a imagem antiga ao FormData
            const oldImageInput = document.getElementById(inputId);
            if (oldImageInput && oldImageInput.value) {
                formData.append('old_image', oldImageInput.value);
            }

            // Adiciona o nome personalizado se fornecido
            if (customName) {
                formData.append('custom_name', customName);
            }
            
            try {
                const response = await fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById(inputId).value = data.url;
                    updateImagePreview(inputId);
                    
                    // Mostra mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.getElementById(inputId).parentNode.appendChild(alert);
                    
                    // Remove a mensagem após 3 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 3000);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                // Mostra mensagem de erro
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-2';
                alert.innerHTML = `
                    Erro ao fazer upload: ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.getElementById(inputId).parentNode.appendChild(alert);
                
                // Remove a mensagem após 5 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        }
    }

    // Configura o evento de confirmação do modal
    document.getElementById('confirmRename').addEventListener('click', function() {
        const customName = document.getElementById('imageName').value.trim();
        if (customName && currentFile && currentInputId) {
            handleFileUpload(currentFile, currentInputId, customName);
            bootstrap.Modal.getInstance(document.getElementById('renameImageModal')).hide();
        }
    });

    // Configura drag and drop para cada zona
    document.querySelectorAll('.drop-zone').forEach(dropZone => {
        const inputId = dropZone.getAttribute('data-target');
        
        dropZone.addEventListener('click', () => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.onchange = (e) => handleFileSelect(e, inputId);
            fileInput.click();
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) {
                currentFile = file;
                currentInputId = inputId;
                showRenameModal(file);
            }
        });
    });

    // Função para limpar todos os campos
    window.clearAllFields = function() {
        if (confirm('Tem certeza que deseja apagar todos os campos?')) {
            document.querySelectorAll('#inicioForm input[type="text"], #inicioForm textarea').forEach(input => {
                input.value = '';
            });
            
            document.querySelectorAll('.image-preview img').forEach(img => {
                img.style.display = 'none';
            });
        }
    }

    // Funções para gerenciar ligações rápidas
    function addNewLink() {
        const container = document.getElementById('linksContainer');
        const index = container.children.length;
        
        const newLink = document.createElement('div');
        newLink.className = 'link-item mb-4 p-3 border rounded';
        newLink.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Ligação ${index + 1}</h4>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeLink(this)">Remover</button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="links[${index}][Nome]" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Link</label>
                <input type="url" class="form-control" name="links[${index}][Link]" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagem</label>
                <div class="image-upload-container">
                    <input type="text" class="form-control" name="links[${index}][Imagem]" required>
                    <div class="image-preview">
                        <img src="" alt="Preview" style="display: none; max-width: 100px; max-height: 100px;">
                    </div>
                    <div class="drop-zone" data-target="link_${index}">
                        Arraste uma imagem aqui ou clique para selecionar
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Largura (px)</label>
                        <input type="number" class="form-control" name="links[${index}][Largura]">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Altura (px)</label>
                        <input type="number" class="form-control" name="links[${index}][Altura]">
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(newLink);
        initializeDropZone(newLink.querySelector('.drop-zone'));
    }

    function removeLink(button) {
        if (confirm('Tem certeza que deseja remover esta ligação?')) {
            button.closest('.link-item').remove();
            updateLinkNumbers();
        }
    }

    function updateLinkNumbers() {
        const items = document.querySelectorAll('.link-item');
        items.forEach((item, index) => {
            item.querySelector('h4').textContent = `Ligação ${index + 1}`;
            const inputs = item.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            const dropZone = item.querySelector('.drop-zone');
            if (dropZone) {
                dropZone.setAttribute('data-target', `link_${index}`);
            }
        });
    }

    function clearAllLinks() {
        if (confirm('Tem certeza que deseja apagar todas as ligações? Esta ação não pode ser desfeita.')) {
            document.getElementById('linksContainer').innerHTML = '';
        }
    }

    // Inicializar drop zones para upload de imagens
    function initializeDropZone(dropZone) {
        const input = dropZone.previousElementSibling.previousElementSibling;
        const preview = dropZone.previousElementSibling.querySelector('img');

        dropZone.addEventListener('click', () => {
            input.click();
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                handleImageUpload(file, input, preview);
            }
        });

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                handleImageUpload(file, input, preview);
            }
        });
    }

    function handleImageUpload(file, input, preview) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('target', input.getAttribute('name').match(/\[(\d+)\]/)[1]);

        fetch('upload_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = data.url;
                preview.src = data.url;
                preview.style.display = 'block';
            } else {
                alert('Erro ao fazer upload da imagem: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao fazer upload da imagem');
        });
    }

    // Inicializar drop zones existentes
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.drop-zone').forEach(initializeDropZone);
    });

    // Garante que o modal de renomear imagem esteja sempre oculto ao carregar a página
    // Always hide the rename image modal on page load
    window.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('renameImageModal');
        if (modalEl) {
            modalEl.classList.remove('show');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    });

    // Adicionar a função JavaScript para limpar os campos do formulário Sobre Nós
    function clearSobreFields() {
        if (confirm('Tem certeza que deseja apagar todos os campos?')) {
            document.querySelectorAll('#sobreForm input[type="text"], #sobreForm textarea').forEach(input => {
                input.value = '';
            });
            
            document.querySelectorAll('#sobreForm .image-preview img').forEach(img => {
                img.style.display = 'none';
            });
        }
    }

    // Funções para gerenciar avaliações
    function addNewAvaliacao() {
        const container = document.getElementById('avaliacoesContainer');
        const index = container.children.length;
        const newCard = document.createElement('div');
        newCard.className = 'avaliacao-item';
        newCard.style = 'background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;';
        newCard.innerHTML = `
            <button type=\"button\" class=\"remove-link-btn\" onclick=\"removeAvaliacao(this)\" title=\"Remover avaliação\" style=\"position: absolute; top: 10px; right: 10px; background: #dc3545; border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; opacity: 0.85; transition: background 0.2s, opacity 0.2s; z-index: 2;\"><i class='fas fa-trash'></i></button>
            <h4 class=\"mb-2\" style=\"font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd; padding-right: 36px;\">Avaliação ${index + 1}</h4>
            <div class=\"mb-2\">\n        <label class=\"form-label\">Nome</label>\n        <input type=\"text\" class=\"form-control\" name=\"avaliacoes[${index}][Nome]\" required>\n    </div>\n    <div class=\"mb-2\">\n        <label class=\"form-label\">Avaliação</label>\n        <div class=\"star-rating\" data-rating=\"${avaliacao['Estrelas']}\">\n            <span class=\"star\" data-value=\"1\">★</span>\n            <span class=\"star\" data-value=\"2\">★</span>\n            <span class=\"star\" data-value=\"3\">★</span>\n            <span class=\"star\" data-value=\"4\">★</span>\n            <span class=\"star\" data-value=\"5\">★</span>\n            <span class=\"rating-value\">${avaliacao['Estrelas']}/5</span>\n            <input type=\"hidden\" name=\"avaliacoes[${index}][Estrelas]\" value=\"${avaliacao['Estrelas']}\">\n        </div>\n    </div>\n    <div class=\"mb-2\">\n        <label class=\"form-label\">Texto</label>\n        <textarea class=\"form-control\" name=\"avaliacoes[${index}][Texto]\" rows=\"3\" required>${avaliacao['Texto']}</textarea>\n    </div>\n`;
        container.appendChild(newCard);
        updateAvaliacaoNumbers();
        initStarRating(newCard.querySelector('.star-rating'));
        initDragAndDrop();
    }
    function removeAvaliacao(button) {
        if (confirm('Tem certeza que deseja remover esta avaliação?')) {
            button.closest('.avaliacao-item').remove();
            updateAvaliacaoNumbers();
        }
    }
    function clearAllAvaliacoes() {
        if (confirm('Tem certeza que deseja apagar todas as avaliações? Esta ação não pode ser desfeita.')) {
            document.getElementById('avaliacoesContainer').innerHTML = '';
        }
    }
    function updateAvaliacaoNumbers() {
        const items = document.querySelectorAll('.avaliacao-item');
        items.forEach((item, index) => {
            item.querySelector('h4').textContent = `Avaliação ${index + 1}`;
            const nome = item.querySelector('input[type="text"]');
            const estrelas = item.querySelector('input[type="hidden"]');
            const texto = item.querySelector('textarea');
            if (nome) nome.setAttribute('name', `avaliacoes[${index}][Nome]`);
            if (estrelas) estrelas.setAttribute('name', `avaliacoes[${index}][Estrelas]`);
            if (texto) texto.setAttribute('name', `avaliacoes[${index}][Texto]`);
            const starDiv = item.querySelector('.star-rating');
            if (starDiv) starDiv.setAttribute('data-rating', estrelas.value);
            if (starDiv) initStarRating(starDiv); // Re-inicializa o rating ao reordenar
        });
    }
    function initStarRating(container) {
        if (!container) return;
        console.log('Inicializando estrelas para:', container);
        const stars = container.querySelectorAll('.star');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        let currentValue = parseInt(hiddenInput.value) || 0;

        // Atualiza visual inicial
        stars.forEach((star, index) => {
            if (index < currentValue) {
                star.classList.add('checked');
            } else {
                star.classList.remove('checked');
            }
        });

        // Clique para selecionar
        stars.forEach((star, index) => {
            star.onclick = function() {
                const value = index + 1;
                hiddenInput.value = value;
                currentValue = value;
                stars.forEach((s, i) => {
                    if (i < value) s.classList.add('checked');
                    else s.classList.remove('checked');
                });
            };
            // Hover visual
            star.onmouseover = function() {
                stars.forEach((s, i) => {
                    if (i <= index) s.classList.add('checked');
                    else s.classList.remove('checked');
                });
            };
            star.onmouseout = function() {
                stars.forEach((s, i) => {
                    if (i < currentValue) s.classList.add('checked');
                    else s.classList.remove('checked');
                });
            };
        });
    }

    // Inicializar rating em todos os cartões ao carregar
    document.querySelectorAll('.star-rating').forEach(initStarRating);

    // Drag and drop para reordenar avaliações
    function initDragAndDrop() {
        const container = document.getElementById('avaliacoesContainer');
        let dragSrcEl = null;
        container.querySelectorAll('.avaliacao-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                dragSrcEl = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.outerHTML);
                this.classList.add('dragElem');
            });
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('over');
            });
            item.addEventListener('dragleave', function(e) {
                this.classList.remove('over');
            });
            item.addEventListener('drop', function(e) {
                e.stopPropagation();
                if (dragSrcEl !== this) {
                    this.parentNode.removeChild(dragSrcEl);
                    this.insertAdjacentHTML('beforebegin', e.dataTransfer.getData('text/html'));
                    updateAvaliacaoNumbers();
                    document.querySelectorAll('.star-rating').forEach(initStarRating);
                    initDragAndDrop();
                }
                this.classList.remove('over');
                return false;
            });
            item.addEventListener('dragend', function(e) {
                this.classList.remove('dragElem');
                document.querySelectorAll('.avaliacao-item').forEach(i => i.classList.remove('over'));
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        initDragAndDrop();
    });
});
</script>

</body>

</html>