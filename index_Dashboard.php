<?php
require_once 'connection.php';

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Nome, Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    header('Location: index.php');
    exit;
}

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

// Função para buscar os dados da tabela AvisolaranjaInicio
function getAvisolaranjaInicio() {
    global $conn;
    $sql = "SELECT * FROM AvisolaranjaInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
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

// Processamento do formulário do Aviso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_aviso'])) {
    $texto = $_POST['texto'];
    $textobtn = $_POST['textobtn'];
    $link = $_POST['link'];

    $sql = "UPDATE AvisolaranjaInicio SET 
            Texto = ?,
            Textobtn = ?,
            link = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $texto, $textobtn, $link);

    if ($stmt->execute()) {
        $updateMessage = "Aviso atualizado com sucesso!";
        $updateType = "success";
        // Atualiza os dados após o update
        $avisoData = getAvisolaranjaInicio();
    } else {
        $updateMessage = "Erro ao atualizar aviso: " . $conn->error;
        $updateType = "danger";
    }
}

$capaData = getCapaData();
$sobreNos = getSobreNosData();
$ctaInicio = getCTAInicioData();
$avisoData = getAvisolaranjaInicio();
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
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    .content-section {
      display: none !important;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }

    .content-section.active {
      display: block !important;
    }

    /* Evitar que elementos apareçam por trás */
    body, html {
      overflow-x: hidden;
      min-height: 100vh;
    }

    body {
      margin: 0;
      padding: 0;
    }

    .main-wrapper {
      position: relative;
      z-index: 1;
      background: #f8f9fa;
      min-height: 100vh;  
      padding: 20px;
    }

    /* Garantir que a sidebar acompanhe o conteúdo */
    .page-flex {
      position: relative;
      min-height: 100vh;
    }

    .sidebar {
      min-height: 100vh !important;
      height: auto !important;
    }

    /* Garantir que modais tenham z-index correto */
    .modal, .custom-modal {
      z-index: 1050 !important;
    }

    /* Esconder qualquer elemento que possa estar vazando */
    .content-section:not(.active) {
      visibility: hidden;
      opacity: 0;
      position: absolute;
      left: -9999px;
    }

    .content-section.active {
      visibility: visible;
      opacity: 1;
      position: relative;
      left: auto;
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
    }

    /* Estilos para os cards de posts no dashboard */
    #posts-section .post-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        overflow: hidden;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        border: none;
        padding: 18px 18px 12px 18px;
        margin: 0;
        min-width: 270px;
        max-width: 340px;
        flex: 1 1 270px;
    }

    #posts-section .post-card:hover {
        transform: none;
        box-shadow: 0 6px 18px rgba(0,0,0,0.13);
    }

    #posts-section .post-image {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #f8f9fa;
        margin-bottom: 15px;
        border-radius: 8px;
    }

    #posts-section .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    #posts-section .post-card:hover .post-image img {
        transform: scale(1.03);
    }

    #posts-section .post-content {
        padding: 0;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: none;
    }

    #posts-section .post-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #posts-section .post-excerpt {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
    }

    #posts-section .post-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    #posts-section .tag-badge {
        background: #e9ecef;
        color: #495057;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    #posts-section .tag-badge:hover {
        background: #dee2e6;
        color: #333;
    }

    #posts-section .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
        color: #718096;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    #posts-section .post-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    #posts-section .post-meta i {
        font-size: 0.9rem;
        color: #a0aec0;
    }

    #posts-section .post-meta .post-author {
        color: #4a5568;
    }

    #posts-section .post-meta .post-date {
        color: #718096;
    }

    #posts-section .post-meta .post-comments {
        color: #718096;
    }

    #posts-section .post-meta span:hover {
        color: #4a90e2;
    }

    #posts-section .post-meta span:hover i {
        color: #4a90e2;
    }

    #posts-section .post-actions {
        display: flex;
        gap: 0.75rem;
        padding-top: 1rem;
        margin-top: auto;
        justify-content: space-around;
    }

    #posts-section .post-actions .btn {
        flex: none;
        width: auto;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #posts-section .post-actions .btn i {
        font-size: 0.9rem;
    }

    #posts-section .post-actions .btn-primary {
        background: #0d6efd;
        border: none;
        color: white;
    }

    #posts-section .post-actions .btn-primary:hover {
        background: #0b5ed7;
        transform: none;
    }

    #posts-section .post-actions .btn-danger {
        background: #dc3545;
        border: none;
        color: white;
    }

    #posts-section .post-actions .btn-danger:hover {
        background: #bb2d3b;
        transform: none;
    }

    /* Container e grid de posts */
    #posts-section .container-fluid {
        padding: 2rem;
    }

    #posts-section .row {
        margin: -1rem;
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: center;
    }

    /* Botão Novo Post */
    #posts-section .btn-primary {
        background: #0d6efd;
        border: none;
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    #posts-section .btn-primary:hover {
        background: #0b5ed7;
        transform: none;
    }

    #posts-section .btn-primary i {
        font-size: 0.9rem;
    }

    #posts-section .row.mb-4 {
        margin-bottom: 2.5rem !important; /* aumenta o espaço abaixo do botão Novo Post */
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        padding: 1rem 0;
    }
    .pagination {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    /* Estilos para a tabela de utilizadores */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .user-row td {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .avatar-wrapper {
        position: relative;
    }

    .avatar {
        transition: transform 0.2s ease;
    }

    .user-row:hover .avatar {
        transform: scale(1.1);
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .user-email {
        font-size: 0.85rem;
    }

    .form-select-sm {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 0.375rem 2rem 0.375rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-select-sm:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-check-input {
        width: 2.5em;
        height: 1.25em;
        margin-top: 0.125em;
        vertical-align: top;
        background-color: #fff;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border: 1px solid rgba(0, 0, 0, 0.25);
        appearance: none;
        color-adjust: exact;
        transition: background-position 0.15s ease-in-out;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .badge {
        padding: 0.5em 0.75em;
        font-size: 0.75em;
        font-weight: 500;
        border-radius: 6px;
    }

    .bg-success-subtle {
        background-color: #d1e7dd;
    }

    .bg-secondary-subtle {
        background-color: #e2e3e5;
    }

    .text-success {
        color: #198754 !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .btn-outline-danger {
        border-width: 1px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Estilo para a mensagem de "Nenhum utilizador encontrado" */
    .text-muted {
        color: #6c757d !important;
        font-size: 0.95rem;
    }

    /* Estilo para a paginação */
    .pagination {
        margin-top: 1.5rem;
    }

    .page-link {
        padding: 0.5rem 0.75rem;
        color: #0d6efd;
        background-color: #fff;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        z-index: 2;
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* CSS extra para labels por cima dos campos na tabela de utilizadores */
    .users-table th, .users-table td {
        vertical-align: top;
        text-align: left;
    }
    .users-table .form-label {
        display: block;
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .users-table .form-control, .users-table .form-select {
        margin-bottom: 0.5rem;
    }
    .users-table .user-info {
        margin-bottom: 0.5rem;
    }

    /* Animações para linhas da tabela */
    .user-row {
        transition: all 0.3s ease;
    }
    
    .user-row.removing {
        opacity: 0.3;
        transform: translateX(-10px);
        background-color: #ffebee !important;
    }

    /* Spinner de loading personalizado */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.1em;
    }

    /* Estados dos botões */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-loading {
        pointer-events: none;
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
                      <a class="nav-link" href="#" onclick="showSection('aviso-section')">
                          <i class="fas fa-exclamation-circle"></i> Aviso
                      </a>
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
                            <a href="#" onclick="showSection('posts-section')">
                            <i class="fas fa-exclamation-circle"></i> Posts
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="#" onclick="showSection('faqs-section')">
                                <i class="fas fa-question-circle"></i> Main FAQ's index
                            </a>
                        </li>
                    </ul>
                </li>
            
            </ul>
            <span class="system-menu__title">system</span>
            <ul class="sidebar-body-menu">
                <li>
                    <a href="#" onclick="showSection('suggestions-section')"><span class="icon edit" aria-hidden="true"></span>Sugestões</a>
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
                        <span class="icon user-3" aria-hidden="true"></span>Utilizadores
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="#" onclick="showSection('users-section')">
                                <i class="fas fa-users"></i> Gerir Utilizadores
                            </a>
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
      <div class="nav-user-wrapper">
        <button href="##" class="nav-user-btn dropdown-btn" title="My profile" type="button">
          <span class="sr-only">My profile</span>
          <span class="nav-user-img">
            <picture><source srcset="./img/avatar/avatar-illustrated-02.webp" type="image/webp"><img src="./img/avatar/avatar-illustrated-02.png" alt="User name"></picture>
          </span>
          <span class="nav-user-name"><?php echo htmlspecialchars($user['Nome']); ?></span>
        </button>
        <ul class="users-item-dropdown nav-user-dropdown dropdown">
          <li><a href="##">
              <i data-feather="user" aria-hidden="true"></i>
              <span>Perfil</span>
            </a></li>
          <li><a href="##">
              <i data-feather="settings" aria-hidden="true"></i>
              <span>Configurações</span>
            </a></li>
          <li><a href="index.php">
              <i data-feather="home" aria-hidden="true"></i>
              <span>Voltar ao site</span>
            </a></li>
          <li><a class="danger" href="logout.php">
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

                                    <form id="inicioForm">
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

            <!-- ---------------------------- Seção de Ligações Rápidas (escondida por padrão) ---------------------------- -->
            <div id="links-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Ligações Rápidas</h3>
                                </div>
                                <div class="card-body">
                                    <div id="linksAlert"></div>
                                    <form method="POST" id="linksForm">
                                        <div id="linksContainer" class="row" style="display: flex; flex-wrap: wrap; gap: 24px;">
                                            <?php 
                                            $ligacoesRapidas = getLigacoesRapidasData();
                                            foreach ($ligacoesRapidas as $index => $ligacao): 
                                            ?>
                                            <div class="link-item">
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

            <!-- Seção Main FAQ's (escondida por padrão) -->
            <div id="faqs-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Editar Main FAQ's</h3>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    // Processamento do formulário de FAQs
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_faqs'])) {
                                        $faqs = $_POST['faqs'] ?? [];
                                        // Limpa a tabela
                                        $conn->query("TRUNCATE TABLE FaqPrevisualizacaoInicio");
                                        // Insere as FAQs na nova ordem
                                        $stmt = $conn->prepare("INSERT INTO FaqPrevisualizacaoInicio (titulofaq, textofaq, Link, imagemfaq) VALUES (?, ?, ?, ?)");
                                        foreach ($faqs as $faq) {
                                            $titulo = $faq['titulofaq'] ?? '';
                                            $texto = $faq['textofaq'] ?? '';
                                            $link = $faq['Link'] ?? '';
                                            $imagem = $faq['imagemfaq'] ?? '';
                                            $stmt->bind_param("ssss", $titulo, $texto, $link, $imagem);
                                            $stmt->execute();
                                        }
                                        $updateMessage = "FAQs atualizadas com sucesso!";
                                        $updateType = "success";
                                    }
                                    $faqsData = [];
                                    $result = $conn->query("SELECT * FROM FaqPrevisualizacaoInicio ORDER BY id");
                                    while($row = $result->fetch_assoc()) {
                                        $faqsData[] = $row;
                                    }
                                    ?>
                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    <form method="POST" id="faqsForm" onsubmit="return submitFaqsForm(event)">
                                        <div id="faqsContainer" class="row" style="display: flex; flex-wrap: wrap; gap: 24px;">
                                            <?php foreach ($faqsData as $index => $faq): ?>
                                            <div class="faq-item" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;">
                                                <h4 class="mb-2" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd;">FAQ <?php echo $index + 1; ?></h4>
                                                <div class="mb-2">
                                                    <label class="form-label">Título</label>
                                                    <input type="text" class="form-control" name="faqs[<?php echo $index; ?>][titulofaq]" value="<?php echo htmlspecialchars($faq['titulofaq']); ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Texto</label>
                                                    <textarea class="form-control" name="faqs[<?php echo $index; ?>][textofaq]" rows="3" required><?php echo htmlspecialchars($faq['textofaq']); ?></textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Link</label>
                                                    <input type="text" class="form-control" name="faqs[<?php echo $index; ?>][Link]" value="<?php echo htmlspecialchars($faq['Link']); ?>" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Imagem</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control mb-2" name="faqs[<?php echo $index; ?>][imagemfaq]" value="<?php echo htmlspecialchars($faq['imagemfaq']); ?>" required>
                                                        <div class="image-preview mb-2">
                                                            <img src="<?php echo htmlspecialchars($faq['imagemfaq']); ?>" alt="Preview" 
                                                                 onerror="this.style.display='none'" style="max-width: 100px; max-height: 100px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="faq_<?php echo $index; ?>">
                                                            <i class="fas fa-cloud-upload-alt mb-2"></i>
                                                            <p class="mb-0">Arraste uma imagem aqui ou clique para selecionar</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-primary">
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

            <!-- Seção Aviso (escondida por padrão) -->
            <div id="aviso-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Conteúdo do Aviso</h3>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    // Processamento do formulário do Aviso
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_aviso'])) {
                                        $texto = $_POST['texto'];
                                        $textobtn = $_POST['textobtn'];
                                        $link = $_POST['link'];

                                        $sql = "UPDATE AvisolaranjaInicio SET 
                                                Texto = ?,
                                                Textobtn = ?,
                                                link = ?
                                                WHERE id = 1";

                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("sss", $texto, $textobtn, $link);

                                        if ($stmt->execute()) {
                                            $updateMessage = "Aviso atualizado com sucesso!";
                                            $updateType = "success";
                                            // Atualiza os dados após o update
                                            $avisoData = getAvisolaranjaInicio();
                                        } else {
                                            $updateMessage = "Erro ao atualizar aviso: " . $conn->error;
                                            $updateType = "danger";
                                        }
                                    }
                                    ?>

                                    <?php if (isset($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show" role="alert">
                                            <?php echo $updateMessage; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="avisoForm">
                                        <div class="text-input-container">
                                            <div class="form-group">
                                                <label for="texto" class="form-label">Texto do Aviso</label>
                                                <textarea class="form-control" id="texto" name="texto" rows="4" 
                                                          placeholder="Digite o texto do aviso..."><?php echo isset($avisoData['Texto']) ? htmlspecialchars($avisoData['Texto']) : ''; ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="textobtn" class="form-label">Texto do Botão</label>
                                                <input type="text" class="form-control" id="textobtn" name="textobtn" 
                                                       value="<?php echo isset($avisoData['Textobtn']) ? htmlspecialchars($avisoData['Textobtn']) : ''; ?>"
                                                       placeholder="Digite o texto do botão...">
                                            </div>

                                            <div class="form-group">
                                                <label for="link" class="form-label">Link do Botão</label>
                                                <input type="text" class="form-control" id="link" name="link" 
                                                       value="<?php echo isset($avisoData['link']) ? htmlspecialchars($avisoData['link']) : ''; ?>"
                                                       placeholder="Digite o link para onde o botão deve direcionar...">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="submit" name="update_aviso" class="btn btn-primary">Salvar Alterações</button>
                                            <button type="button" class="btn btn-danger" onclick="clearAvisoFields()">Apagar tudo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Posts (escondida por padrão) -->
            <div id="posts-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">Gerir Posts</h2>
                            <button id="btnNovoPost" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Novo Post
                            </button>
                        </div>
                    </div>

                    <div id="postsContainer" class="row">
                        <?php
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $posts_per_page = 8;
                        $offset = ($page - 1) * $posts_per_page;
                        
                        // Buscar posts com paginação
                        $sql = "SELECT p.*, u.Nome as autor_nome, 
                               (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as num_comentarios 
                               FROM posts p 
                               LEFT JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador 
                               ORDER BY p.data_criacao DESC LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $posts_per_page, $offset);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        // Buscar total de posts para paginação
                        $total_sql = "SELECT COUNT(*) as total FROM posts";
                        $total_result = $conn->query($total_sql);
                        $total_posts = $total_result->fetch_assoc()['total'];
                        $total_pages = ceil($total_posts / $posts_per_page);

                        if ($result->num_rows > 0) {
                            while($post = $result->fetch_assoc()) {
                                ?>
                                <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                                    <div class="post-image">
                                        <img src="<?php echo htmlspecialchars($post['img_principal']); ?>" 
                                             alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                             loading="lazy">
                                    </div>
                                    <div class="post-content">
                                        <h3 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                                        <p class="post-excerpt"><?php echo htmlspecialchars(substr($post['texto'], 0, 80)) . '...'; ?></p>
                                        
                                        <?php if (!empty($post['tags'])): ?>
                                        <div class="post-tags">
                                            <?php 
                                            $tags = explode(',', $post['tags']);
                                            foreach ($tags as $tag): 
                                            ?>
                                                <span class="tag-badge"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="post-meta">
                                            <span class="post-author">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['autor_nome']); ?>
                                            </span>
                                            <span class="post-date">
                                                <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['data_criacao'])); ?>
                                            </span>
                                            <span class="post-comments">
                                                <i class="fas fa-comments"></i> <?php echo $post['num_comentarios']; ?>
                                            </span>
                                        </div>
                                        
                                        <div class="post-actions">
                                            <button class="btn btn-primary" onclick="editPost(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-danger" onclick="deletePost(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-trash"></i> Apagar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="col-12"><p class="text-center">Nenhum post encontrado.</p></div>';
                        }
                        ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                    <div id="paginationContainer" class="pagination-container">
                        <nav aria-label="Navegação de posts">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Anterior">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="#" data-page="<?php echo $page + 1; ?>" aria-label="Próximo">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section de criação/edição de post -->
            <div id="post-editor-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" id="post-editor-title">Novo Post</h3>
                                </div>
                                <div class="card-body">
                                    <form id="postEditorForm" enctype="multipart/form-data">
                                        <input type="hidden" name="id" id="postId">
                                        <input type="hidden" name="autor_id" id="postAutorId" value="<?php echo $_SESSION['user_id']; ?>">
                                        <div class="mb-3">
                                            <label for="postTitulo" class="form-label">Título</label>
                                            <input type="text" class="form-control" id="postTitulo" name="titulo" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="postTexto" class="form-label">Texto</label>
                                            <textarea class="form-control" id="postTexto" name="texto" rows="6" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="postTags" class="form-label">Tags (separadas por vírgula)</label>
                                            <input type="text" class="form-control" id="postTags" name="tags" placeholder="ex: Moodle, GIAE, Suporte">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Imagem Principal</label>
                                            <div class="image-upload-container">
                                                <input type="text" class="form-control mb-2" id="postImgPrincipal" name="img_principal">
                                                <div class="image-preview mb-2" id="postImgPrincipalPreview">
                                                    <img src="" alt="Preview" style="display: none; max-width: 200px; max-height: 150px;">
                                                </div>
                                                <div class="drop-zone" data-target="postImgPrincipal">
                                                    Arraste uma imagem aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Imagens Adicionais</label>
                                            <div class="row">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control mb-2" id="postImg<?php echo $i; ?>" name="img_<?php echo $i; ?>">
                                                        <div class="image-preview mb-2" id="postImg<?php echo $i; ?>Preview">
                                                            <img src="" alt="Preview" style="display: none; max-width: 200px; max-height: 150px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="postImg<?php echo $i; ?>">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Data de Criação</label>
                                            <input type="text" class="form-control" id="postDataCriacao" name="data_criacao" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-secondary" id="cancelPostEdit">Cancelar</button>
                                            <button type="submit" class="btn btn-primary" id="savePostBtn">Criar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Gerenciamento de Usuários (escondida por padrão) -->
            <div id="users-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">Gerenciar Utilizadores</h2>
                            <div class="d-flex gap-2">
                                <div class="search-box">
                                    <input type="text" id="userSearch" class="form-control" placeholder="Pesquisar utilizadores...">
                                </div>
                                <!-- Removido o botão Novo Utilizador -->
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <!-- Será preenchido via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="usersPagination" class="pagination-container">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Seção de Sugestões (escondida por padrão) -->
            <div id="suggestions-section" class="content-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">Sugestões dos Utilizadores</h2>
                            <div class="d-flex gap-2">
                                <div class="search-box">
                                    <input type="text" id="suggestionSearchInput" class="form-control" placeholder="Pesquisar sugestões...">
                                </div>
                                <select id="statusFilter" class="form-select">
                                    <option value="">Todos os estados</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="em_analise">Em Análise</option>
                                    <option value="resolvido">Resolvido</option>
                                    <option value="rejeitado">Rejeitado</option>
                                </select>
                                <select id="priorityFilter" class="form-select">
                                    <option value="">Todas as prioridades</option>
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Tipo</th>
                                            <th>Prioridade</th>
                                            <th>Estado</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="suggestionsTableBody">
                                        <!-- Será preenchido via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="suggestionsPagination" class="pagination-container">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- Modal para criar/editar usuário -->
<div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="user-modal-title" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="user-modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" id="user-id" name="id">
                    <div class="mb-3">
                        <label for="user-nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="user-nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="user-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="user-senha" name="senha" required>
                        <small class="form-text text-muted">Deixe em branco para manter a senha atual ao editar.</small>
                    </div>
                    <div class="mb-3">
                        <label for="user-tipo" class="form-label">Tipo de Usuário</label>
                        <select class="form-select" id="user-tipo" name="tipo" required>
                            <option value="Aluno">Aluno</option>
                            <option value="Professor">Professor</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Salvar</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal customizado para visualizar detalhes da sugestão -->
<div id="suggestion-details-modal" class="custom-modal" style="display: none;">
    <div class="custom-modal-backdrop"></div>
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h4 class="custom-modal-title">Detalhes da Sugestão</h4>
            <button type="button" class="custom-modal-close" onclick="closeSuggestionModal()">×</button>
        </div>
        <div class="custom-modal-body">
            <!-- Seção Informações Básicas -->
            <div class="modal-section">
                <h6 class="section-title">
                    <i class="fas fa-info-circle"></i>Informações Básicas
                </h6>
                <div class="section-content">
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Nome:</strong> <span id="modal-suggestion-name"></span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong> <span id="modal-suggestion-email"></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Tipo de Sugestão:</strong> <span id="modal-suggestion-type"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Datas e Estado -->
            <div class="modal-section">
                <h6 class="section-title">
                    <i class="fas fa-calendar-alt"></i>Datas e Estado
                </h6>
                <div class="section-content">
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Data de Submissão:</strong> <span id="modal-suggestion-date"></span>
                        </div>
                        <div class="info-item">
                            <strong>Estado:</strong> <span id="modal-suggestion-status"></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Prioridade:</strong> <span id="modal-suggestion-priority"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Descrição da Sugestão -->
            <div class="modal-section">
                <h6 class="section-title">
                    <i class="fas fa-comment-dots"></i>Descrição da Sugestão
                </h6>
                <div class="suggestion-message">
                    <p id="modal-suggestion-message"></p>
                </div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn-delete-suggestion" onclick="deleteSuggestion()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; transition: background-color 0.2s ease; margin-right: 10px;">
                <i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar
            </button>
            <button type="button" class="btn-close-modal" onclick="closeSuggestionModal()">
                <i class="fas fa-times"></i>Fechar
            </button>
        </div>
    </div>
</div>

<style>
/* Estilos para o modal customizado */
.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.custom-modal.show {
    opacity: 1;
    visibility: visible;
}

.custom-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.custom-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    overflow-y: auto;
}

.custom-modal-header {
    padding: 20px 25px 15px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
}

.custom-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.custom-modal-close:hover {
    background-color: #f8f9fa;
    color: #333;
}

.custom-modal-body {
    padding: 20px 25px;
}

.modal-section {
    margin-bottom: 25px;
}

.modal-section:last-child {
    margin-bottom: 0;
}

.section-title {
    color: #007bff;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 8px;
    font-size: 1.1rem;
}

.section-content {
    padding-left: 0;
}

.info-row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-item {
    flex: 1;
    min-width: 250px;
    margin-bottom: 8px;
}

.info-item strong {
    color: #333;
    font-weight: 600;
}

.info-item span {
    color: #555;
    margin-left: 5px;
}

.suggestion-message {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.suggestion-message p {
    margin: 0;
    color: #333;
    line-height: 1.6;
    white-space: pre-line;
}

.custom-modal-footer {
    padding: 15px 25px 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.btn-close-modal {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: background-color 0.2s ease;
}

.btn-close-modal:hover {
    background-color: #5a6268;
}

.btn-close-modal i {
    margin-right: 6px;
    font-size: 12px;
}

.btn-delete-suggestion:hover {
    background-color: #bb2d3b !important;
}

.btn-delete-suggestion:disabled {
    background-color: #6c757d !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

/* Badges para status e prioridade */
.custom-badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 4px;
    text-transform: uppercase;
}

.badge-success { background-color: #28a745; color: white; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; color: white; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }

/* Responsividade */
@media (max-width: 768px) {
    .custom-modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .info-item {
        min-width: 100%;
    }
    
    .custom-modal-header,
    .custom-modal-body,
    .custom-modal-footer {
        padding-left: 20px;
        padding-right: 20px;
    }
}
</style>

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

<script src="js/inicio.js"></script>

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
        const preview = input.closest('.image-upload-container').querySelector('.image-preview');
        if (input.value) {
            preview.src = input.value;
            preview.style.display = 'block';
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
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.value = data.url;
                        // Atualiza o preview imediatamente após definir o valor
                        const previewContainer = input.closest('.image-upload-container').querySelector('.image-preview');
                        if (previewContainer) {
                            const preview = previewContainer.querySelector('img');
                            if (preview) {
                                preview.src = data.url;
                                preview.style.display = 'block';
                            }
                        }
                    }
                    
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

    // Adiciona evento de mudança para todos os inputs de imagem
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="text"]').forEach(input => {
            if (input.name && (input.name.includes('imagemfaq') || input.name.includes('Imagem'))) {
                input.addEventListener('change', function() {
                    const previewContainer = this.parentNode.querySelector('.image-preview');
                    if (previewContainer) {
                        const preview = previewContainer.querySelector('img');
                        if (preview) {
                            preview.src = this.value;
                            preview.style.display = this.value ? 'block' : 'none';
                        }
                    }
                });
            }
        });
    });

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

    // ---------------------------- Funções para gerenciar ligações rápidas ----------------------------
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

    // ---------------------------- Funções para gerenciar avaliações ----------------------------
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

    // ---------------------------- Funções para gerenciar FAQs ----------------------------
    function addNewFaq() {
        const container = document.getElementById('faqsContainer');
        const index = container.children.length;
        const newCard = document.createElement('div');
        newCard.className = 'faq-item';
        newCard.style = 'background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;';
        newCard.innerHTML = `
            <button type="button" class="remove-link-btn" onclick="removeFaq(this)" title="Remover FAQ" style="position: absolute; top: 10px; right: 10px; background: #dc3545; border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; opacity: 0.85; transition: background 0.2s, opacity 0.2s; z-index: 2;"><i class="fas fa-trash"></i></button>
            <h4 class="mb-2" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd; padding-right: 36px;">FAQ ${index + 1}</h4>
            <div class="mb-2">
                <label class="form-label">Título</label>
                <input type="text" class="form-control" name="faqs[${index}][titulofaq]" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Texto</label>
                <textarea class="form-control" name="faqs[${index}][textofaq]" rows="3" required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Link</label>
                <input type="text" class="form-control" name="faqs[${index}][Link]" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Imagem</label>
                <div class="image-upload-container">
                    <input type="text" class="form-control mb-2" name="faqs[${index}][imagemfaq]" required>
                    <div class="image-preview mb-2">
                        <img src="" alt="Preview" style="display: none; max-width: 100px; max-height: 100px;">
                    </div>
                    <div class="drop-zone" data-target="faq_${index}">
                        <i class="fas fa-cloud-upload-alt mb-2"></i>
                        <p class="mb-0">Arraste uma imagem aqui ou clique para selecionar</p>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newCard);
        updateFaqNumbers();
        initDragAndDrop();
    }

    function removeFaq(button) {
        if (confirm('Tem certeza que deseja remover esta FAQ?')) {
            button.closest('.faq-item').remove();
            updateFaqNumbers();
        }
    }

    function clearAllFaqs() {
        if (confirm('Tem certeza que deseja apagar todas as FAQs? Esta ação não pode ser desfeita.')) {
            document.getElementById('faqsContainer').innerHTML = '';
        }
    }

    function updateFaqNumbers() {
        const items = document.querySelectorAll('.faq-item');
        items.forEach((item, index) => {
            item.querySelector('h4').textContent = `FAQ ${index + 1}`;
            const inputs = item.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            const dropZone = item.querySelector('.drop-zone');
            if (dropZone) {
                dropZone.setAttribute('data-target', `faq_${index}`);
            }
        });
    }

    // Adicionar a função JavaScript para limpar os campos do formulário do Aviso
    function clearAvisoFields() {
        if (confirm('Tem certeza que deseja apagar todos os campos?')) {
            document.querySelectorAll('#avisoForm input[type="text"], #avisoForm textarea').forEach(input => {
                input.value = '';
            });
        }
    }

    // Função para atualizar o preview da imagem
    function updateImagePreview(inputId) {
        const input = document.getElementById(inputId);
        const preview = input.closest('.image-upload-container').querySelector('.image-preview');
        if (input.value) {
            preview.src = input.value;
            preview.style.display = 'block';
        }
    }

    // Função para submeter o formulário de FAQs via AJAX
    function submitFaqsForm(event) {
        event.preventDefault();
        
        const form = document.getElementById('faqsForm');
        const formData = new FormData(form);
        
        // Adiciona um campo para identificar que é uma atualização de FAQs
        formData.append('update_faqs', '1');
        
        // Debug: Mostra os dados que estão sendo enviados
        console.log('Enviando dados do formulário:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('update_faqs.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Resposta recebida:', response);
            return response.json();
        })
        .then(data => {
            console.log('Dados processados:', data);
            if (data.success) {
                // Mostra mensagem de sucesso
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                form.insertBefore(alert, form.firstChild);
                
                // Remove a mensagem após 3 segundos
                setTimeout(() => {
                    alert.remove();
                }, 3000);
                
                // Recarrega a página após 1 segundo para mostrar as alterações
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            // Mostra mensagem de erro
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            form.insertBefore(alert, form.firstChild);
            
            // Remove a mensagem após 5 segundos
            setTimeout(() => {
                alert.remove();
            }, 5000);
        });
        
        return false;
    }

    // Adicionar esta nova função para lidar com o submit do formulário de início
    function submitInicioForm(event) {
        event.preventDefault();
        
        const form = document.getElementById('inicioForm');
        const formData = new FormData(form);
        
        // Adiciona um campo para identificar que é uma atualização da capa
        formData.append('update_capa', '1');
        
        fetch('update_inicio.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostra mensagem de sucesso
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                form.insertBefore(alert, form.firstChild);
                
                // Remove a mensagem após 3 segundos
                setTimeout(() => {
                    alert.remove();
                }, 3000);

                // Garante que a seção de início permanece visível
                document.querySelectorAll('.content-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById('inicio-section').style.display = 'block';
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            // Mostra mensagem de erro
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            form.insertBefore(alert, form.firstChild);
            
            // Remove a mensagem após 5 segundos
            setTimeout(() => {
                alert.remove();
            }, 5000);
        });
        
        return false;
    }

    // ---------------------------- Funções para gerenciar sugestões ----------------------------

    // Função para carregar sugestões
    function loadSuggestions(page = 1, search = '', status = '', priority = '') {
        fetch(`get_suggestions.php?page=${page}&search=${encodeURIComponent(search)}&status=${status}&priority=${priority}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySuggestions(data.suggestions);
                    displaySuggestionsPagination(data.pagination);
                } else {
                    console.error('Erro ao carregar sugestões:', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
    }

    // Função para exibir sugestões na tabela
    function displaySuggestions(suggestions) {
        const tbody = document.getElementById('suggestionsTableBody');
        if (suggestions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma sugestão encontrada.</td></tr>';
            return;
        }

        tbody.innerHTML = suggestions.map(suggestion => {
            const statusBadgeClass = {
                'pendente': 'bg-warning text-dark',
                'em_analise': 'bg-info text-white',
                'resolvido': 'bg-success text-white',
                'rejeitado': 'bg-danger text-white'
            }[suggestion.status] || 'bg-secondary text-white';

            const priorityBadgeClass = {
                'baixa': 'bg-success text-white',
                'media': 'bg-warning text-dark',
                'alta': 'bg-danger text-white'
            }[suggestion.prioridade] || 'bg-secondary text-white';

            const statusText = {
                'pendente': 'Pendente',
                'em_analise': 'Em Análise',
                'resolvido': 'Resolvido',
                'rejeitado': 'Rejeitado'
            }[suggestion.status] || suggestion.status;

            const priorityText = {
                'baixa': 'Baixa',
                'media': 'Média',
                'alta': 'Alta'
            }[suggestion.prioridade] || suggestion.prioridade;

            const typeText = {
                'melhoria_conteudo': 'Melhoria de Conteúdo',
                'nova_funcionalidade': 'Nova Funcionalidade',
                'problema_tecnico': 'Problema Técnico',
                'feedback_geral': 'Feedback Geral',
                'outro': 'Outro'
            }[suggestion.tipo_sugestao] || suggestion.tipo_sugestao;

            return `
                <tr data-suggestion-id="${suggestion.id}">
                    <td>
                        <div class="user-info">
                            <div class="user-name">${suggestion.nome}</div>
                        </div>
                    </td>
                    <td>${suggestion.email}</td>
                    <td>${typeText}</td>
                    <td><span class="badge ${priorityBadgeClass}">${priorityText}</span></td>
                    <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
                    <td>${new Date(suggestion.data_criacao).toLocaleDateString('pt-PT')}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="viewSuggestionDetails(${suggestion.id})">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Função para exibir paginação das sugestões
    function displaySuggestionsPagination(pagination) {
        const container = document.getElementById('suggestionsPagination');
        if (pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<nav aria-label="Navegação de sugestões"><ul class="pagination justify-content-center">';
        
        if (pagination.current_page > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSuggestions(${pagination.current_page - 1}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">Anterior</a></li>`;
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadSuggestions(${i}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">${i}</a></li>`;
        }

        if (pagination.current_page < pagination.total_pages) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSuggestions(${pagination.current_page + 1}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">Próximo</a></li>`;
        }

        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    // Função para ver detalhes de uma sugestão
    function viewSuggestionDetails(suggestionId) {
        currentSuggestionId = suggestionId; // Definir ID da sugestão atual
        
        fetch(`get_suggestions.php?id=${suggestionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.suggestion) {
                    const suggestion = data.suggestion;

                    // Preenche o modal com os dados
                    document.getElementById('modal-suggestion-name').textContent = suggestion.nome;
                    document.getElementById('modal-suggestion-email').textContent = suggestion.email;
                    
                    const typeText = {
                        'melhoria_conteudo': 'Melhoria de Conteúdo',
                        'nova_funcionalidade': 'Nova Funcionalidade',
                        'problema_tecnico': 'Problema Técnico',
                        'feedback_geral': 'Feedback Geral',
                        'outro': 'Outro'
                    }[suggestion.tipo_sugestao] || suggestion.tipo_sugestao;
                    
                    document.getElementById('modal-suggestion-type').textContent = typeText;
                    
                    // Formatar data
                    const dataFormatada = new Date(suggestion.data_criacao).toLocaleString('pt-PT', {
                        day: '2-digit',
                        month: '2-digit', 
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    document.getElementById('modal-suggestion-date').textContent = dataFormatada;
                    
                    // Formatar prioridade com badge colorido
                    const priorityText = {
                        'baixa': 'Baixa',
                        'media': 'Média',
                        'alta': 'Alta'
                    }[suggestion.prioridade] || suggestion.prioridade;
                    
                    const priorityBadgeClass = {
                        'baixa': 'custom-badge badge-success',
                        'media': 'custom-badge badge-warning',
                        'alta': 'custom-badge badge-danger'
                    }[suggestion.prioridade] || 'custom-badge badge-secondary';
                    
                    document.getElementById('modal-suggestion-priority').innerHTML = 
                        `<span class="${priorityBadgeClass}">${priorityText}</span>`;
                    
                    // Formatar status com badge colorido
                    const statusText = {
                        'pendente': 'Pendente',
                        'em_analise': 'Em Análise',
                        'resolvido': 'Resolvido',
                        'rejeitado': 'Rejeitado'
                    }[suggestion.status] || suggestion.status;
                    
                    const statusBadgeClass = {
                        'pendente': 'custom-badge badge-warning',
                        'em_analise': 'custom-badge badge-info',
                        'resolvido': 'custom-badge badge-success',
                        'rejeitado': 'custom-badge badge-danger'
                    }[suggestion.status] || 'custom-badge badge-secondary';
                    
                    document.getElementById('modal-suggestion-status').innerHTML = 
                        `<span class="${statusBadgeClass}">${statusText}</span>`;
                    
                    document.getElementById('modal-suggestion-message').textContent = suggestion.mensagem;

                    // Mostra o modal customizado
                    showSuggestionModal();
                } else {
                    showAlert('Erro ao carregar detalhes da sugestão', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao carregar detalhes da sugestão', 'error');
            });
    }

    // Event listeners para filtros e pesquisa
    document.getElementById('suggestionSearchInput').addEventListener('input', function() {
        loadSuggestions(1, this.value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value);
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        loadSuggestions(1, document.getElementById('suggestionSearchInput').value, this.value, document.getElementById('priorityFilter').value);
    });

    document.getElementById('priorityFilter').addEventListener('change', function() {
        loadSuggestions(1, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, this.value);
    });



    // Função para mostrar seção (atualizada para incluir sugestões)
    window.showSection = function(sectionId) {
        // Esconde todas as seções
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
            section.style.display = 'none';
        });

        // Mostra a seção selecionada
        const selectedSection = document.getElementById(sectionId);
        if (selectedSection) {
            selectedSection.classList.add('active');
            selectedSection.style.display = 'block';
            
            // Carrega dados específicos da seção
            if (sectionId === 'suggestions-section') {
                loadSuggestions();
            } else if (sectionId === 'users-section') {
                loadUsers();
            } else if (sectionId === 'posts-section') {
                // Carregar posts com paginação e configurar event listeners
                window.loadPosts(1);
                currentPostsPage = 1;
            }
            
            console.log('Seção mostrada:', sectionId);
        } else {
            console.error('Seção não encontrada:', sectionId);
        }
    }

    // Função para mostrar o modal customizado
    function showSuggestionModal() {
        const modal = document.getElementById('suggestion-details-modal');
        modal.style.display = 'block';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // Fechar modal ao clicar no backdrop
        modal.querySelector('.custom-modal-backdrop').onclick = closeSuggestionModal;
        
        // Fechar modal com ESC
        document.addEventListener('keydown', handleEscapeKey);
    }

    // Função para fechar o modal customizado
    function closeSuggestionModal() {
        const modal = document.getElementById('suggestion-details-modal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
        
        // Limpar ID da sugestão atual
        currentSuggestionId = null;
        
        // Reabilitar botão de apagar (caso tenha ficado desabilitado)
        const deleteBtn = document.querySelector('.btn-delete-suggestion');
        if (deleteBtn) {
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
        }
        
        // Remover event listener do ESC
        document.removeEventListener('keydown', handleEscapeKey);
    }

    // Função para lidar com a tecla ESC
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeSuggestionModal();
        }
    }

    // Variável global para armazenar o ID da sugestão atual no modal
    let currentSuggestionId = null;

    // Função para apagar sugestão
    function deleteSuggestion() {
        if (!currentSuggestionId) {
            showAlert('Erro: Nenhuma sugestão selecionada', 'danger');
            return;
        }

        if (confirm('Tem certeza que deseja apagar esta sugestão? Esta ação não pode ser desfeita.')) {
            const deleteBtn = document.querySelector('.btn-delete-suggestion');
            
            // Desabilitar botão durante a requisição
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 6px; font-size: 12px;"></i>Apagando...';

            fetch('manage_suggestions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${currentSuggestionId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeSuggestionModal();
                    // Recarregar a lista de sugestões
                    loadSuggestions(1, document.getElementById('suggestionSearchInput').value, 
                                  document.getElementById('statusFilter').value, 
                                  document.getElementById('priorityFilter').value);
                } else {
                    showAlert(data.message, 'danger');
                    // Reabilitar botão em caso de erro
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao apagar sugestão: ' + error.message, 'danger');
                // Reabilitar botão em caso de erro
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
            });
        }
    }

    // Expor funções globalmente para uso nos onclick
    window.viewSuggestionDetails = viewSuggestionDetails;
    window.loadSuggestions = loadSuggestions;
    window.closeSuggestionModal = closeSuggestionModal;
    window.deleteSuggestion = deleteSuggestion;
});

// ---------------------------- Funções para gerenciar posts (separado para evitar problemas de timing) ----------------------------

// Variáveis globais para controle de paginação de posts
let currentPostsPage = 1;

// Função auxiliar para alternar entre seções
function switchToSection(hideSection, showSection) {
    console.log(`🔄 Alternando de ${hideSection} para ${showSection}`);
    
    const hide = document.getElementById(hideSection);
    const show = document.getElementById(showSection);
    
    if (hide) {
        hide.style.display = 'none';
        hide.classList.remove('active');
    }
    
    if (show) {
        show.style.display = 'block';
        show.classList.add('active');
        console.log(`✅ Seção ${showSection} mostrada`);
    }
}

// Funções para gerenciar posts (definidas globalmente)
window.showNewPostForm = function() {
    console.log('🔵 showNewPostForm called');
    
    try {
        // Verificar se elementos existem antes de acessá-los
        const postsSection = document.getElementById('posts-section');
        const editorSection = document.getElementById('post-editor-section');
        
        if (!postsSection) {
            console.error('❌ posts-section não encontrada');
            return;
        }
        
        if (!editorSection) {
            console.error('❌ post-editor-section não encontrada');
            return;
        }
        
        console.log('✅ Ambas as seções encontradas, alternando...');
        
        // Esconde a seção de posts e mostra o editor
        switchToSection('posts-section', 'post-editor-section');
        
        // Limpa o formulário e configura para novo post
        const form = document.getElementById('postEditorForm');
        if (form) {
            form.reset();
            console.log('✅ Formulário resetado');
        } else {
            console.error('❌ postEditorForm não encontrado');
        }
        
        const postId = document.getElementById('postId');
        if (postId) postId.value = '';
        
        const editorTitle = document.getElementById('post-editor-title');
        if (editorTitle) {
            editorTitle.textContent = 'Novo Post';
            console.log('✅ Título definido');
        } else {
            console.error('❌ post-editor-title não encontrado');
        }
        
        const saveBtn = document.getElementById('savePostBtn');
        if (saveBtn) {
            saveBtn.textContent = 'Criar';
            console.log('✅ Botão configurado');
        } else {
            console.error('❌ savePostBtn não encontrado');
        }
        
        const dataField = document.getElementById('postDataCriacao');
        if (dataField) {
            dataField.value = new Date().toLocaleString('pt-PT');
            console.log('✅ Data definida');
        } else {
            console.error('❌ postDataCriacao não encontrado');
        }
        
        // Esconde todas as imagens de preview
        document.querySelectorAll('#post-editor-section .image-preview img').forEach(img => {
            img.style.display = 'none';
        });
        
        console.log('🎉 showNewPostForm concluída com sucesso');
        
    } catch (error) {
        console.error('❌ Erro em showNewPostForm:', error);
        alert('Erro ao abrir formulário de post: ' + error.message);
    }
};

window.editPost = function(postId) {
    fetch(`get_posts.php?id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.post) {
                const post = data.post;
                
                // Esconde a seção de posts e mostra o editor
                switchToSection('posts-section', 'post-editor-section');
                
                // Preenche o formulário com os dados do post
                document.getElementById('postId').value = post.id;
                document.getElementById('postTitulo').value = post.titulo;
                document.getElementById('postTexto').value = post.texto;
                document.getElementById('postTags').value = post.tags;
                document.getElementById('postImgPrincipal').value = post.img_principal || '';
                document.getElementById('postDataCriacao').value = new Date(post.data_criacao).toLocaleString('pt-PT');
                
                // Preenche as imagens adicionais
                for (let i = 1; i <= 5; i++) {
                    const imgField = document.getElementById(`postImg${i}`);
                    if (imgField) {
                        imgField.value = post[`img_${i}`] || '';
                    }
                }
                
                // Atualiza os previews das imagens
                if (typeof updatePostImagePreviews === 'function') {
                    updatePostImagePreviews();
                }
                
                // Configura o formulário para edição
                document.getElementById('post-editor-title').textContent = 'Editar Post';
                document.getElementById('savePostBtn').textContent = 'Salvar';
            } else {
                alert('Erro ao carregar dados do post');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar post: ' + error.message);
        });
};

window.deletePost = function(postId) {
    if (confirm('Tem certeza que deseja apagar este post? Esta ação não pode ser desfeita.')) {
        // Encontra o card do post para mostrar loading
        const postCard = document.querySelector(`[data-post-id="${postId}"]`);
        if (postCard) {
            postCard.style.opacity = '0.5';
            postCard.style.pointerEvents = 'none';
        }
        
        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                
                // Recarregar a página atual de posts após apagar
                // Isso garantirá que outros posts venham preencher o espaço
                setTimeout(() => {
                    if (typeof window.loadPosts === 'function') {
                        window.loadPosts(currentPostsPage);
                    }
                }, 500);
                
            } else {
                alert(data.message);
                // Restaura o estado do card em caso de erro
                if (postCard) {
                    postCard.style.opacity = '1';
                    postCard.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao apagar post: ' + error.message);
            // Restaura o estado do card em caso de erro
            if (postCard) {
                postCard.style.opacity = '1';
                postCard.style.pointerEvents = 'auto';
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔵 Posts JavaScript carregado');
    
    // Função para carregar posts com paginação
    window.loadPosts = function(page = 1) {
        currentPostsPage = page;
        
        // Mostrar loading
        const postsContainer = document.getElementById('postsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        
        if (postsContainer) {
            postsContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Carregando posts...</p></div>';
        }
        
        fetch(`get_posts.php?page=${page}`)
            .then(response => response.text())
            .then(html => {
                // Criar um elemento temporário para analisar o HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extrair o conteúdo dos posts
                const postsContent = tempDiv.querySelector('#postsContainer');
                const paginationContent = tempDiv.querySelector('#paginationContainer');
                
                if (postsContainer && postsContent) {
                    postsContainer.innerHTML = postsContent.innerHTML;
                }
                
                if (paginationContainer && paginationContent) {
                    paginationContainer.innerHTML = paginationContent.innerHTML;
                    // Reconfigurar event listeners para paginação
                    setupPostsPaginationEvents();
                } else if (paginationContainer && !paginationContent) {
                    // Se não há paginação, limpar o container
                    paginationContainer.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar posts:', error);
                if (postsContainer) {
                    postsContainer.innerHTML = '<div class="col-12 text-center py-4"><p class="text-danger">Erro ao carregar posts. Tente novamente.</p></div>';
                }
            });
    };
    
    // Função para configurar event listeners de paginação de posts
    function setupPostsPaginationEvents() {
        document.querySelectorAll('#paginationContainer .page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page && page !== currentPostsPage.toString()) {
                    window.loadPosts(parseInt(page));
                }
            });
        });
    }
    
    // Funções já definidas globalmente acima - apenas event listeners aqui
    
    // Função para atualizar previews das imagens dos posts
    function updatePostImagePreviews() {
        // Preview da imagem principal
        const imgPrincipal = document.getElementById('postImgPrincipal');
        const previewPrincipal = document.querySelector('#postImgPrincipalPreview img');
        if (imgPrincipal && previewPrincipal) {
            if (imgPrincipal.value) {
                previewPrincipal.src = imgPrincipal.value;
                previewPrincipal.style.display = 'block';
            } else {
                previewPrincipal.style.display = 'none';
            }
        }
        
        // Previews das imagens adicionais
        for (let i = 1; i <= 5; i++) {
            const imgField = document.getElementById(`postImg${i}`);
            const preview = document.querySelector(`#postImg${i}Preview img`);
            if (imgField && preview) {
                if (imgField.value) {
                    preview.src = imgField.value;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            }
        }
    }
    
    // Event listener para o botão cancelar do editor de posts
    document.getElementById('cancelPostEdit').addEventListener('click', function() {
        switchToSection('post-editor-section', 'posts-section');
    });
    
    // Event listener para o formulário de posts
    document.getElementById('postEditorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEdit = document.getElementById('postId').value !== '';
        
        fetch('save_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    switchToSection('post-editor-section', 'posts-section');
                    
                    // Recarregar a página de posts para mostrar as mudanças
                    window.loadPosts(currentPostsPage);
                }, 1000);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro ao salvar post: ' + error.message, 'danger');
        });
    });
    
    // Event listeners para atualizar previews quando os campos de imagem mudarem
    document.addEventListener('input', function(e) {
        if (e.target.id && e.target.id.includes('postImg')) {
            updatePostImagePreviews();
        }
    });
    
    // Configurar event listeners iniciais para paginação de posts
    // (para a paginação que já existe quando a página carrega)
    setTimeout(() => {
        setupPostsPaginationEvents();
    }, 1000);
    
    // Event listener para o botão "Novo Post"
    const btnNovoPost = document.getElementById('btnNovoPost');
    if (btnNovoPost) {
        btnNovoPost.addEventListener('click', function() {
            console.log('🔵 Botão Novo Post clicado via event listener');
            showNewPostForm();
        });
        console.log('✅ Event listener do botão Novo Post configurado');
    } else {
        console.error('❌ Botão btnNovoPost não encontrado');
    }
    
    console.log('🎉 Posts JavaScript configurado com sucesso');
    
    // Teste se as funções estão disponíveis globalmente
    console.log('✅ Teste de funções:');
    console.log('- showNewPostForm:', typeof window.showNewPostForm);
    console.log('- editPost:', typeof window.editPost);
    console.log('- deletePost:', typeof window.deletePost);
});
</script>

</body>

</html>