<?php
require_once 'connection.php';

// Função para buscar os dados da tabela InicioInicio
function getCapaData() {
    global $conn;
    $sql = "SELECT * FROM InicioInicio LIMIT 1";
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

$capaData = getCapaData();
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
        border: 2px dashed #ccc;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        margin-top: 10px;
        transition: border-color 0.3s ease;
    }

    .drop-zone:hover, .drop-zone.dragover {
        border-color: #007bff;
        background-color: #e9ecef;
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
                    <li>
                      <a href="javascript:void(0);" data-section="inicio">Início</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" data-section="links">Ligações rápidas</a>
                    </li>
                    <li>
                      <a href="javascript:void(0);" data-section="sobre">Sobre nós</a>
                    </li>
                    <li>
                      <a href="javascript:void(0);" data-section="avaliacoes">Avaliações</a>
                    </li>
                    <li>
                      <a href="javascript:void(0);" data-section="login">Login rápido</a>
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
            <!-- Seção de Início -->
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

                                        <div class="mb-3">
                                            <label for="TextoBemvindo" class="form-label">Texto de Boas-vindas</label>
                                            <textarea class="form-control" id="TextoBemvindo" name="TextoBemvindo" rows="2"><?php echo htmlspecialchars($capaData['TextoBemvindo']); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="TextoInicial" class="form-label">Texto Inicial</label>
                                            <textarea class="form-control" id="TextoInicial" name="TextoInicial" rows="2"><?php echo htmlspecialchars($capaData['TextoInicial']); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="TextoInicial2" class="form-label">Texto Inicial 2</label>
                                            <textarea class="form-control" id="TextoInicial2" name="TextoInicial2" rows="3"><?php echo htmlspecialchars($capaData['TextoInicial2']); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="BotaoInicial" class="form-label">Texto do Botão</label>
                                            <input type="text" class="form-control" id="BotaoInicial" name="BotaoInicial" 
                                                   value="<?php echo htmlspecialchars($capaData['BotaoInicial']); ?>">
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

<!-- Modal para mensagem de confirmação -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                <h4 class="mt-3" id="confirmationMessage"></h4>
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
        const selectedSection = document.getElementById(sectionId + '-section');
        if (selectedSection) {
            selectedSection.style.display = 'block';
            console.log('Seção mostrada:', sectionId);
        } else {
            console.error('Seção não encontrada:', sectionId);
        }
    }

    // Adiciona event listeners aos links do menu
    document.querySelectorAll('a[data-section]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');
            showSection(sectionId);
            console.log('Link clicado:', sectionId);
        });
    });

    // Mostra a seção "inicio" por padrão
    showSection('inicio');

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

    // Função para mostrar mensagem de confirmação
    function showConfirmationMessage(message) {
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        document.getElementById('confirmationMessage').textContent = message;
        modal.show();
        
        // Fecha o modal após 2 segundos
        setTimeout(() => {
            modal.hide();
        }, 2000);
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
                    
                    // Mostra mensagem de confirmação centralizada
                    showConfirmationMessage('Imagem enviada com sucesso!');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                // Mostra mensagem de erro centralizada
                showConfirmationMessage('Erro ao fazer upload: ' + error.message);
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
});
</script>

</body>

</html>