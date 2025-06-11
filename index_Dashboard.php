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
    $sql = "SELECT * FROM CTAInicio WHERE id = 2 LIMIT 1";
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

// Função para buscar os dados da tabela Footer
function getFooterData() {
    global $conn;
    $sql = "SELECT * FROM Footer LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar os dados da tabela FooterLinks
function getFooterLinksData() {
    global $conn;
    $sql = "SELECT * FROM FooterLinks ORDER BY secao, id";
    $result = $conn->query($sql);
    $links = array();
    while($row = $result->fetch_assoc()) {
        $links[] = $row;
    }
    return $links;
}

// Função para buscar seções distintas da tabela FooterLinks
function getFooterSections() {
    global $conn;
    $sql = "SELECT DISTINCT secao FROM FooterLinks ORDER BY secao";
    $result = $conn->query($sql);
    $sections = array();
    while($row = $result->fetch_assoc()) {
        $sections[] = $row['secao'];
    }
    return $sections;
}

// Processamento das ligações rápidas agora é feito via AJAX em update_links.php

// Processar atualização do Login Rápido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_login'])) {
    // DEBUG: Log para verificar se chegou aqui
    error_log("🚨 PROCESSAMENTO LOGIN INICIADO - POST recebido");
    error_log("📊 DADOS RECEBIDOS: " . print_r($_POST, true));
    
    // Output direto no HTML para debug
    echo "<!-- DEBUG: PROCESSAMENTO LOGIN INICIADO -->";
    echo "<!-- DEBUG: POST DATA: " . print_r($_POST, true) . " -->";
    
    // Verificar se a tabela existe e tem dados
    $checkResult = $conn->query("SELECT * FROM CTAInicio WHERE id = 2");
    if ($checkResult) {
        $existingData = $checkResult->fetch_assoc();
        error_log("🔍 DADOS ATUAIS NA BD (id=2): " . print_r($existingData, true));
        if (!$existingData) {
            error_log("⚠️ NENHUM REGISTRO ENCONTRADO COM ID=2!");
        }
    } else {
        error_log("❌ ERRO AO CONSULTAR TABELA CTAInicio: " . $conn->error);
    }
    
    $titulo = $_POST['Titulo'];
    $texto = $_POST['texto'];
    $btntext = $_POST['btntext'];
    $fundo = $_POST['fundo'];

    error_log("📝 VALORES EXTRAÍDOS: Titulo=$titulo, texto=$texto, btntext=$btntext, fundo=$fundo");

    $sql = "UPDATE CTAInicio SET 
            Titulo = ?,
            texto = ?,
            btntext = ?,
            fundo = ?
            WHERE id = 2";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("❌ ERRO AO PREPARAR SQL: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", 
        $titulo,
        $texto,
        $btntext,
        $fundo
    );

    if ($stmt->execute()) {
        error_log("✅ UPDATE EXECUTADO COM SUCESSO! Linhas afetadas: " . $stmt->affected_rows);
        $updateMessage = "Dados do Login Rápido atualizados com sucesso!";
        $updateType = "success";
        $activeSection = "login-section";
        
        // Output direto para debug
        echo "<!-- DEBUG: UPDATE LOGIN SUCESSO - Linhas afetadas: " . $stmt->affected_rows . " -->";
        
        // Recarrega os dados após o update
        $ctaInicio = getCTAInicioData();
        error_log("🔄 DADOS RECARREGADOS: " . print_r($ctaInicio, true));
        
        // Verificar novamente os dados na BD após update
        $verifyResult = $conn->query("SELECT * FROM CTAInicio WHERE id = 2");
        if ($verifyResult) {
            $verifiedData = $verifyResult->fetch_assoc();
            error_log("🔎 VERIFICAÇÃO PÓS-UPDATE: " . print_r($verifiedData, true));
            echo "<!-- DEBUG: DADOS PÓS-UPDATE: " . print_r($verifiedData, true) . " -->";
        }
        
    } else {
        error_log("❌ ERRO AO EXECUTAR UPDATE: " . $stmt->error);
        echo "<!-- DEBUG: ERRO UPDATE LOGIN: " . $stmt->error . " -->";
        $updateMessage = "Erro ao atualizar dados do Login Rápido: " . $conn->error;
        $updateType = "danger";
        $activeSection = "login-section";
    }
}

// Processamento do início/capa agora é feito via AJAX em update_inicio_ajax.php

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
        $activeSection = "sobre-section";
        
        // Se for requisição AJAX, retorna JSON e para a execução
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $updateMessage]);
            exit;
        }
    } else {
        $updateMessage = "Erro ao atualizar dados: " . $conn->error;
        $updateType = "danger";
        $activeSection = "sobre-section";
        
        // Se for requisição AJAX, retorna JSON e para a execução
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $updateMessage]);
            exit;
        }
    }
}

// Processamento das FAQs agora é feito via AJAX em update_faqs_ajax.php

// Processamento do formulário do Aviso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_aviso'])) {
    $texto = $_POST['texto'];
    $textobtn = $_POST['textobtn'];

    $sql = "UPDATE AvisolaranjaInicio SET 
            Texto = ?,
            Textobtn = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $texto, $textobtn);

    if ($stmt->execute()) {
        $updateMessage = "Aviso atualizado com sucesso!";
        $updateType = "success";
        $activeSection = "aviso-section";
        // Atualiza os dados após o update
        $avisoData = getAvisolaranjaInicio();
    } else {
        $updateMessage = "Erro ao atualizar aviso: " . $conn->error;
        $updateType = "danger";
        $activeSection = "aviso-section";
    }
}

// Processamento do formulário das Colunas do Footer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer_columns'])) {
    try {
        // Coletar dados das colunas
        $coluna1_titulo = trim($_POST['coluna1_titulo']);
        $coluna2_titulo = trim($_POST['coluna2_titulo']);
        $coluna3_titulo = trim($_POST['coluna3_titulo']);
        $coluna4_titulo = trim($_POST['coluna4_titulo']);
        
        $coluna1_links = isset($_POST['coluna1_links']) ? array_filter($_POST['coluna1_links'], 'trim') : [];
        $coluna2_links = isset($_POST['coluna2_links']) ? array_filter($_POST['coluna2_links'], 'trim') : [];
        $coluna3_links = isset($_POST['coluna3_links']) ? array_filter($_POST['coluna3_links'], 'trim') : [];
        $coluna4_links = isset($_POST['coluna4_links']) ? array_filter($_POST['coluna4_links'], 'trim') : [];
        
        // Construir conteúdo das colunas (título + links separados por quebra de linha)
        $textoEsquerda = $coluna1_titulo . "\n" . implode("\n", $coluna1_links);
        $textoCentro = $coluna2_titulo . "\n" . implode("\n", $coluna2_links);
        $textoDireita = $coluna3_titulo . "\n" . implode("\n", $coluna3_links);
        
        $sql = "UPDATE Footer SET 
                TextoEsquerda = ?,
                TextoCentro = ?,
                TextoDireita = ?
                WHERE id = 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $textoEsquerda, $textoCentro, $textoDireita);
        
        if ($stmt->execute()) {
            $updateMessage = "Colunas do footer atualizadas com sucesso!";
            $updateType = "success";
            $activeSection = "footer-section";
            // Recarregar dados do footer após atualização
            $footerData = getFooterData();
        } else {
            throw new Exception("Erro ao atualizar: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $updateMessage = "Erro ao atualizar colunas: " . $e->getMessage();
        $updateType = "danger";
        $activeSection = "footer-section";
    }
}

// Processamento do formulário do Copyright
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer'])) {
    $copyright = trim($_POST['Copyright']);

    $sql = "UPDATE Footer SET Copyright = ? WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $copyright);

    if ($stmt->execute()) {
        $updateMessage = "Copyright atualizado com sucesso!";
        $updateType = "success";
        $activeSection = "footer-section";
        // Recarregar dados do footer após atualização
        $footerData = getFooterData();
    } else {
        $updateMessage = "Erro ao atualizar copyright: " . $conn->error;
        $updateType = "danger";
        $activeSection = "footer-section";
    }
}



// Processamento do formulário do FooterLinks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer_links'])) {
    try {
        // Limpar a tabela atual
        $stmt = $conn->prepare("DELETE FROM FooterLinks");
        $stmt->execute();

        // Inserir os novos links
        if (isset($_POST['footer_links']) && is_array($_POST['footer_links'])) {
            $stmt = $conn->prepare("INSERT INTO FooterLinks (secao, nome, link) VALUES (?, ?, ?)");
            
            foreach ($_POST['footer_links'] as $link) {
                // Permite salvar mesmo com campos vazios, desde que a seção esteja definida
                if (isset($link['secao']) && !empty($link['secao'])) {
                    $secao = $link['secao'];
                    $nome = isset($link['nome']) ? $link['nome'] : '';
                    $linkUrl = isset($link['link']) ? $link['link'] : '';
                    
                    $stmt->bind_param("sss", $secao, $nome, $linkUrl);
                    $stmt->execute();
                }
            }
        }

        $updateMessage = "Links do Footer atualizados com sucesso!";
        $updateType = "success";
        $activeSection = "footer-section";
        // Recarregar dados dos links do footer após atualização
        $footerLinksData = getFooterLinksData();
    } catch (Exception $e) {
        $updateMessage = "Erro ao atualizar links do footer: " . $e->getMessage();
        $updateType = "danger";
        $activeSection = "footer-section";
    }
}

// Se há um campo active_section em qualquer POST, define a seção ativa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['active_section']) && !isset($activeSection)) {
    $activeSection = $_POST['active_section'];
}

$capaData = getCapaData();
$sobreNos = getSobreNosData();
$ctaInicio = getCTAInicioData();
$avisoData = getAvisolaranjaInicio();

// Carregar dados do Footer (após todos os processamentos)
if (!isset($footerData)) $footerData = getFooterData();
if (!isset($footerLinksData)) $footerLinksData = getFooterLinksData();
$footerSections = getFooterSections();

// Garantir que $footerData não seja null
if (!$footerData) {
    $footerData = array(
        'LogoFooter' => '',
        'TextoEsquerda' => '',
        'TextoCentro' => '',
        'TextoDireita' => '',
        'Copyright' => '',
        'CorFundo' => '#333333',
        'CorTexto' => '#ffffff'
    );
}

// Garantir que $footerLinksData seja um array
if (!$footerLinksData) {
    $footerLinksData = array();
}
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
    body,
    html {
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
    .modal,
    .custom-modal {
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
        margin-top: 12px;
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

    /* Design Bonito das Estrelas ⭐ */
    .star-rating {
        display: flex;
        gap: 4px;
        align-items: center;
        margin-bottom: 15px;
        padding: 8px 0;
    }

    .star-rating .star {
        font-size: 28px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #e0e0e0;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
        user-select: none;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
    }

    .star-rating .star:hover {
        transform: scale(1.15) rotate(5deg);
        color: #ffb400;
        filter: drop-shadow(0 4px 8px rgba(255, 180, 0, 0.3));
    }

    /* Efeito cascata no hover */
    .star-rating .star:hover~.star {
        transform: scale(0.95);
        color: #f0f0f0;
    }

    .star-rating .star.active {
        background: linear-gradient(135deg, #ffd700 0%, #ffb400 50%, #ff8c00 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 3px 6px rgba(255, 180, 0, 0.4));
        animation: starGlow 0.6s ease-out;
        position: relative;
    }

    .star-rating .star.active::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120%;
        height: 120%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        z-index: -1;
    }

    @keyframes starGlow {
        0% {
            transform: scale(1);
            filter: drop-shadow(0 0 0 rgba(255, 180, 0, 0));
        }

        50% {
            transform: scale(1.2);
            filter: drop-shadow(0 0 20px rgba(255, 180, 0, 0.6));
        }

        100% {
            transform: scale(1);
            filter: drop-shadow(0 3px 6px rgba(255, 180, 0, 0.4));
        }
    }

    .star-rating .rating-value {
        margin-left: 12px;
        font-size: 15px;
        font-weight: 600;
        color: #495057;
        padding: 6px 14px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 50%, #e9ecef 100%);
        border-radius: 25px;
        border: 1px solid rgba(255, 180, 0, 0.15);
        box-shadow:
            0 2px 8px rgba(0, 0, 0, 0.06),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
    }

    .star-rating .rating-value::before {
        content: '⭐';
        margin-right: 6px;
        font-size: 12px;
        filter: drop-shadow(0 1px 2px rgba(255, 180, 0, 0.3));
    }

    /* Desabilitar interação com estrelas readonly */
    .star-rating.readonly .star {
        cursor: default !important;
        pointer-events: none !important;
        opacity: 0.9;
    }

    .star-rating.readonly .star:hover {
        transform: none !important;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05)) !important;
    }

    .star-rating.readonly .star.active {
        background: linear-gradient(135deg, #ffd700 0%, #ffb400 50%, #ff8c00 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 2px 6px rgba(255, 180, 0, 0.3));
        animation: none;
    }

    .star-rating.readonly .rating-value {
        opacity: 0.85;
        background: linear-gradient(135deg, #f1f3f4 0%, #e8eaed 100%);
    }

    /* Estilo para exibição de texto das avaliações */
    .name-text-display {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 12px 16px;
        font-size: 1rem;
        font-weight: 500;
        color: #495057;
        min-height: 45px;
        display: flex;
        align-items: center;
    }

    .comment-text-display {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 16px;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #495057;
        min-height: 120px;
        max-height: 200px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .comment-text-display::-webkit-scrollbar {
        width: 6px;
    }

    .comment-text-display::-webkit-scrollbar-track {
        background: #f1f3f4;
        border-radius: 3px;
    }

    .comment-text-display::-webkit-scrollbar-thumb {
        background: #c1c8cd;
        border-radius: 3px;
    }

    .comment-text-display::-webkit-scrollbar-thumb:hover {
        background: #a8b2ba;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
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
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.13);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
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

    /* Estilo especial para o campo de texto (textarea) */
    #postTexto {
        min-height: 120px;
        max-height: 200px;
        width: 100%;
        resize: vertical;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        font-family: inherit;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        margin-top: 15px;
    }

    #postTexto:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: 0;
        background-color: #fff;
    }

    /* Espaçamento entre campos */
    .mb-3+.mb-3 {
        margin-top: 20px;
    }

    /* Espaçamento específico para data de criação */
    #post-editor-section .mb-3:has(label:contains("Data de Criação")) {
        margin-top: 25px;
        margin-bottom: 25px;
    }

    /* Espaçamento para os botões */
    #post-editor-section .card-body>.d-flex:last-child {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e3e6f0;
    }

    #posts-section .post-card:hover {
        transform: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.13);
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
        border-top: 1px solid rgba(0, 0, 0, 0.05);
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
        margin-bottom: 2.5rem !important;
        /* aumenta o espaço abaixo do botão Novo Post */
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
    .users-table th,
    .users-table td {
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

    .users-table .form-control,
    .users-table .form-select {
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

    /* Melhorias visuais para cartões de tickets admin - versão ainda mais organizada */
    .admin-ticket-card {
        min-height: 420px;
        border-radius: 1.25rem !important;
        /* Bootstrap rounded-4 */
        border: 2px solid #adb5bd !important;
        /* Bootstrap border-secondary */
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07), 0 1.5px 6px rgba(173, 181, 189, 0.08);
        padding: 0;
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #fff;
        height: 100%;
        transition: box-shadow 0.18s, border-color 0.18s;
    }

    #admin-tickets-cards {
        gap: 32px !important;
        display: flex;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .admin-ticket-card .card-header {
        background: #fff;
        border-bottom: none;
        border-radius: 14px 14px 0 0;
        padding: 20px 28px 8px 28px;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .admin-ticket-card .card-body {
        padding: 18px 28px 22px 28px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex: 1 1 auto;
    }

    .admin-ticket-card .info-block {
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-height: 48px;
        justify-content: center;
    }

    .admin-ticket-card .info-label {
        font-weight: 500;
        color: #888;
        font-size: 0.97em;
        margin-bottom: 2px;
    }

    .admin-ticket-card .info-value {
        color: #222;
        font-size: 1.01em;
        font-weight: 500;
        word-break: break-word;
    }

    .admin-ticket-card .badge-outline-warning {
        color: #ffb648;
        background: #fff;
        border: 2px solid #ffb648;
        font-weight: 600;
        font-size: 1em;
        border-radius: 20px;
        padding: 7px 18px;
        box-shadow: none;
    }

    .admin-ticket-card .badge {
        font-size: 1em;
        padding: 7px 18px;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: none;
    }

    .admin-ticket-card .p-2.bg-light {
        font-size: 0.99em;
        margin-top: 4px;
        margin-bottom: 0;
        min-height: 44px;
        background: #f8f9fa !important;
    }

    .admin-ticket-card .admin-ticket-actions {
        display: flex;
        gap: 18px;
        margin-top: 18px;
        justify-content: stretch;
        flex-wrap: wrap;
    }

    .admin-ticket-card .admin-ticket-actions .btn {
        flex: 1 1 120px;
        min-width: 120px;
        /* max-width removido para evitar corte */
        font-size: 1.08em;
        padding: 12px 0;
        border-radius: 14px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 0;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        outline: none;
    }

    .admin-ticket-card .admin-ticket-actions .btn-success {
        background: #218838;
        color: #fff;
    }

    .admin-ticket-card .admin-ticket-actions .btn-success:hover {
        background: #17692a;
        color: #fff;
        box-shadow: 0 4px 16px rgba(33, 136, 56, 0.13);
    }

    .admin-ticket-card .admin-ticket-actions .btn-danger {
        background: #dc3545;
        color: #fff;
    }

    .admin-ticket-card .admin-ticket-actions .btn-danger:hover {
        background: #b52a37;
        color: #fff;
        box-shadow: 0 4px 16px rgba(220, 53, 69, 0.13);
    }

    .admin-ticket-card .admin-ticket-actions .btn-primary {
        background: #007bff;
        color: #fff;
    }

    .admin-ticket-card .admin-ticket-actions .btn-primary:hover {
        background: #0056b3;
        color: #fff;
        box-shadow: 0 4px 16px rgba(0, 123, 255, 0.13);
    }

    .admin-ticket-card .admin-ticket-actions .btn i {
        font-size: 1.1em;
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        position: relative;
        top: 0;
        margin: 0;
    }

    @media (max-width: 991px) {
        .admin-ticket-card .admin-ticket-actions .btn {
            font-size: 1em;
            padding: 10px 0;
            min-width: 100px;
            max-width: 100%;
        }
    }

    @media (max-width: 767px) {
        .admin-ticket-card .admin-ticket-actions {
            flex-direction: column;
            gap: 12px;
        }

        .admin-ticket-card .admin-ticket-actions .btn {
            width: 100%;
            min-width: 0;
            margin: 0;
        }
    }

    /* ... existing code ... */
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

    /* Estilos específicos para a seção do Aviso */
    .aviso-textarea {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        color: #495057 !important;
        resize: vertical !important;
        min-height: 120px !important;
    }

    .aviso-textarea:focus {
        background-color: #ffffff !important;
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        outline: none !important;
    }

    .aviso-textarea::placeholder {
        color: #6c757d !important;
        opacity: 0.8 !important;
    }

    /* Melhorar espaçamento entre campos do aviso */
    #aviso-section .form-group {
        margin-bottom: 1.5rem !important;
    }

    #aviso-section .form-label {
        font-weight: 600 !important;
        color: #343a40 !important;
        margin-bottom: 8px !important;
        display: block !important;
    }

    #aviso-section .form-control {
        transition: all 0.2s ease-in-out !important;
    }

    #aviso-section .form-control:focus {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
    }

    /* Estender a textarea do aviso para ocupar mais espaço horizontal */
    .aviso-textarea {
        width: 100% !important;
        max-width: none !important;
        resize: vertical !important;
    }

    /* Ajustar o container da seção aviso para dar mais espaço */
    #aviso-section .text-input-container {
        max-width: 800px !important;
    }

    /* Melhorar espaçamento entre campos do login */
    #login-section .form-group {
        margin-bottom: 1.5rem !important;
    }

    #login-section .form-label {
        font-weight: 600 !important;
        color: #343a40 !important;
        margin-bottom: 8px !important;
        display: block !important;
    }

    #login-section .form-control {
        transition: all 0.2s ease-in-out !important;
    }

    #login-section .form-control:focus {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
    }

    /* Estilo especial para os campos de texto e textarea da seção login */
    .login-input,
    .login-textarea {
        background-color: #f8f9fa !important;
        border: 1px solid #e9ecef !important;
        border-radius: 8px !important;
        padding: 12px 15px !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        transition: all 0.3s ease !important;
    }

    .login-input:focus,
    .login-textarea:focus {
        background-color: #ffffff !important;
        border-color: #80bdff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1) !important;
        outline: 0 !important;
    }

    .login-input:hover,
    .login-textarea:hover {
        border-color: #ced4da !important;
    }

    /* Estender a textarea do login para ocupar mais espaço horizontal */
    .login-textarea {
        width: 100% !important;
        max-width: none !important;
        resize: vertical !important;
    }

    /* Ajustar o container da seção login para dar mais espaço */
    #login-section .text-input-container {
        max-width: 800px !important;
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
                            <a href="index.php"><img src="img/logo1AEBConecta.png" alt="logo" title="" /></a>
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
                            <a class="active" href="index_Dashboard.php"><span class="icon home"
                                    aria-hidden="true"></span>Painel de Controlo</a>
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
                                <li>
                                    <a class="nav-link" href="#" onclick="showSection('footer-section')">
                                        <i class="fas fa-window-maximize"></i> Footer
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
                            <a href="#" onclick="showSection('tickets-section')">
                                <i class="fa fa-tools" style="margin-right: 7px;"></i> Gerir Pedidos
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="showSection('suggestions-section')"><span class="icon edit"
                                    aria-hidden="true"></span>Sugestões</a>
                        </li>
                        <!--<li>
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
                </li> -->
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
                        <!--
                <li>
                    <a href="##"><span class="icon setting" aria-hidden="true"></span>Settings</a>
                </li> -->
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
                                    <picture>
                                        <source srcset="./img/avatar/avatar-illustrated-02.webp" type="image/webp"><img
                                            src="./img/avatar/avatar-illustrated-02.png" alt="User name">
                                    </picture>
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
                                            <p>Selecione uma opção no menu "Página Principal" para começar a editar o
                                                conteúdo.</p>
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
                                            <?php if (isset($updateMessage) && isset($activeSection) && $activeSection == 'inicio-section'): ?>
                                            <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show"
                                                role="alert">
                                                <?php echo $updateMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <?php endif; ?>

                                            <form id="inicio-form-ajax" onsubmit="return submitInicioAjax(event)">
                                                <input type="hidden" name="update_capa">
                                                <input type="hidden" name="active_section" value="inicio-section">
                                                <div class="mb-3">
                                                    <label for="LogoSeparador" class="form-label">Logo Separador</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control" id="LogoSeparador"
                                                            name="LogoSeparador"
                                                            value="<?php echo htmlspecialchars($capaData['LogoSeparador']); ?>">
                                                        <div class="image-preview" id="LogoSeparadorPreview">
                                                            <img src="<?php echo htmlspecialchars($capaData['LogoSeparador']); ?>"
                                                                alt="Logo Separador Preview"
                                                                onerror="this.style.display='none'"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="LogoSeparador">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="LogoPrincipal" class="form-label">Logo Principal</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control" id="LogoPrincipal"
                                                            name="LogoPrincipal"
                                                            value="<?php echo htmlspecialchars($capaData['LogoPrincipal']); ?>">
                                                        <div class="image-preview" id="LogoPrincipalPreview">
                                                            <img src="<?php echo htmlspecialchars($capaData['LogoPrincipal']); ?>"
                                                                alt="Logo Principal Preview"
                                                                onerror="this.style.display='none'"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="LogoPrincipal">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-input-container"
                                                    style="background: transparent !important; padding: 0 !important; border: none !important; margin: 0 !important; box-shadow: none !important; display: block !important; width: 100% !important;">
                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="TextoBemvindo" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Texto
                                                            de Boas-vindas</label>
                                                        <textarea class="form-control" id="TextoBemvindo"
                                                            name="TextoBemvindo" rows="2"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; min-height: 70px !important; resize: vertical !important; box-sizing: border-box !important; display: block !important;"
                                                            placeholder="Digite o texto de boas-vindas..."><?php echo htmlspecialchars($capaData['TextoBemvindo']); ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="TextoInicial" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Texto
                                                            Inicial</label>
                                                        <textarea class="form-control" id="TextoInicial"
                                                            name="TextoInicial" rows="2"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; min-height: 70px !important; resize: vertical !important; box-sizing: border-box !important; display: block !important;"
                                                            placeholder="Digite o texto inicial..."><?php echo htmlspecialchars($capaData['TextoInicial']); ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="TextoInicial2" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Texto
                                                            Inicial 2</label>
                                                        <textarea class="form-control" id="TextoInicial2"
                                                            name="TextoInicial2" rows="3"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; min-height: 85px !important; resize: vertical !important; box-sizing: border-box !important; display: block !important;"
                                                            placeholder="Digite o texto inicial 2..."><?php echo htmlspecialchars($capaData['TextoInicial2']); ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="BotaoInicial" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Texto
                                                            do Botão</label>
                                                        <input type="text" class="form-control" id="BotaoInicial"
                                                            name="BotaoInicial"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; box-sizing: border-box !important; display: block !important;"
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
                                                            <img src="<?php echo htmlspecialchars($capaData['Fundo']); ?>"
                                                                alt="Fundo Preview" onerror="this.style.display='none'"
                                                                style="max-width: 200px; max-height: 150px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="Fundo">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="button-text">
                                                            <i class="fas fa-save"></i> Salvar Alterações
                                                        </span>
                                                        <span class="button-loading" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Salvando...
                                                        </span>
                                                    </button>
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
                                            <?php if (isset($updateMessage) && isset($activeSection) && $activeSection == 'links-section'): ?>
                                            <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show"
                                                role="alert">
                                                <?php echo $updateMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <?php endif; ?>
                                            <form id="links-form-ajax" onsubmit="return submitLinksAjax(event)">
                                                <input type="hidden" name="update_links">
                                                <input type="hidden" name="active_section" value="links-section">
                                                <div id="linksContainer" class="row"
                                                    style="display: flex; flex-wrap: wrap; gap: 24px;">
                                                    <?php 
                                            $ligacoesRapidas = getLigacoesRapidasData();
                                            foreach ($ligacoesRapidas as $index => $ligacao): 
                                            ?>
                                                    <div class="link-item">
                                                        <h4 class="mb-2">Ligação <?php echo $index + 1; ?></h4>
                                                        <div class="mb-2">
                                                            <label class="form-label">Nome</label>
                                                            <input type="text" class="form-control"
                                                                name="links[<?php echo $index; ?>][Nome]"
                                                                value="<?php echo htmlspecialchars($ligacao['Nome']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Link</label>
                                                            <input type="url" class="form-control"
                                                                name="links[<?php echo $index; ?>][Link]"
                                                                value="<?php echo htmlspecialchars($ligacao['Link']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Imagem</label>
                                                            <div class="image-upload-container">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="links[<?php echo $index; ?>][Imagem]"
                                                                    value="<?php echo htmlspecialchars($ligacao['Imagem']); ?>"
                                                                    required>
                                                                <div class="image-preview mb-2">
                                                                    <img src="<?php echo htmlspecialchars($ligacao['Imagem']); ?>"
                                                                        alt="Preview"
                                                                        onerror="this.style.display='none'"
                                                                        style="max-width: 100px; max-height: 100px;">
                                                                </div>
                                                                <div class="drop-zone"
                                                                    data-target="link_<?php echo $index; ?>">
                                                                    <i class="fas fa-cloud-upload-alt mb-2"></i>
                                                                    <p class="mb-0">Arraste uma imagem aqui ou clique
                                                                        para selecionar</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 mb-2">
                                                                <label class="form-label">Largura (px)</label>
                                                                <input type="number" class="form-control"
                                                                    name="links[<?php echo $index; ?>][Largura]"
                                                                    value="<?php echo htmlspecialchars($ligacao['Largura'] ?? ''); ?>"
                                                                    placeholder="Ex: 200">
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="form-label">Altura (px)</label>
                                                                <input type="number" class="form-control"
                                                                    name="links[<?php echo $index; ?>][Altura]"
                                                                    value="<?php echo htmlspecialchars($ligacao['Altura'] ?? ''); ?>"
                                                                    placeholder="Ex: 200">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <div class="d-flex justify-content-end mt-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="button-text">
                                                            <i class="fas fa-save"></i> Salvar Alterações
                                                        </span>
                                                        <span class="button-loading" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Salvando...
                                                        </span>
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
                                            <?php if (isset($updateMessage) && isset($activeSection) && $activeSection == 'sobre-section'): ?>
                                            <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show"
                                                role="alert">
                                                <?php echo $updateMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <?php endif; ?>

                                            <form id="sobre-form-ajax" onsubmit="return submitSobreAjax(event)">
                                                <input type="hidden" name="update_sobre">
                                                <input type="hidden" name="active_section" value="sobre-section">
                                                <div class="text-input-container"
                                                    style="background: transparent !important; padding: 0 !important; border: none !important; margin: 0 !important; box-shadow: none !important; display: block !important; width: 100% !important;">
                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="Texto1" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Primeiro
                                                            Texto</label>
                                                        <textarea class="form-control" id="Texto1" name="Texto1"
                                                            rows="6"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; min-height: 120px !important; resize: vertical !important; box-sizing: border-box !important; display: block !important;"
                                                            placeholder="Digite o primeiro texto sobre a empresa..."><?php echo htmlspecialchars($sobreNos['Texto1']); ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4"
                                                        style="margin-bottom: 1.5rem !important; width: 100% !important; display: block !important;">
                                                        <label for="Texto2" class="form-label"
                                                            style="font-weight: 600 !important; color: #374151 !important; margin-bottom: 0.5rem !important; display: block !important; font-size: 0.875rem !important; width: 100% !important;">Segundo
                                                            Texto</label>
                                                        <textarea class="form-control" id="Texto2" name="Texto2"
                                                            rows="6"
                                                            style="width: 100% !important; border-radius: 0.375rem !important; border: 1px solid #d1d5db !important; padding: 0.75rem !important; font-size: 0.875rem !important; line-height: 1.5 !important; background-color: #f8f9fa !important; min-height: 120px !important; resize: vertical !important; box-sizing: border-box !important; display: block !important;"
                                                            placeholder="Digite o segundo texto sobre a empresa..."><?php echo htmlspecialchars($sobreNos['Texto2']); ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">
                                                    <label for="Imagem" class="form-label">Imagem</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control" id="Imagem"
                                                            name="Imagem"
                                                            placeholder="URL da imagem ou carregue uma nova imagem..."
                                                            value="<?php echo htmlspecialchars($sobreNos['Imagem']); ?>">
                                                        <div class="image-preview" id="ImagemPreview">
                                                            <img src="<?php echo htmlspecialchars($sobreNos['Imagem']); ?>"
                                                                alt="Imagem Preview" onerror="this.style.display='none'"
                                                                style="max-width: 250px; max-height: 200px; border-radius: 8px; margin-top: 10px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="Imagem">
                                                            <i class="fas fa-cloud-upload-alt mb-2"
                                                                style="font-size: 1.5rem; color: #6c757d;"></i><br>
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="button-text">Salvar Alterações</span>
                                                        <span class="button-loading" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Salvando...
                                                        </span>
                                                    </button>
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
                                        <div class="card-header">
                                            <h3 class="card-title mb-0">Editar Avaliações</h3>
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
                                        $activeSection = "avaliacoes-section";
                                    }
                                    $avaliacoesData = [];
                                    $result = $conn->query("SELECT * FROM TabelaAvaliacoesInicio ORDER BY id");
                                    while($row = $result->fetch_assoc()) {
                                        $avaliacoesData[] = $row;
                                    }
                                    ?>
                                            <?php if (isset($updateMessage) && isset($activeSection) && $activeSection == 'avaliacoes-section'): ?>
                                            <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show"
                                                role="alert">
                                                <?php echo $updateMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <?php endif; ?>
                                            <form id="avaliacoes-form-ajax">
                                                <input type="hidden" name="update_avaliacoes">
                                                <input type="hidden" name="active_section" value="avaliacoes-section">
                                                <div id="avaliacoesContainer" class="row"
                                                    style="display: flex; flex-wrap: wrap; gap: 24px;">
                                                    <?php foreach ($avaliacoesData as $index => $avaliacao): ?>
                                                    <div class="avaliacao-item"
                                                        data-id="<?php echo $avaliacao['id']; ?>"
                                                        style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;">
                                                        <button type="button" class="remove-link-btn"
                                                            onclick="removeAvaliacao(this)" title="Remover avaliação"
                                                            style="position: absolute; top: 10px; right: 10px; background: #dc3545; border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; opacity: 0.85; transition: background 0.2s, opacity 0.2s; z-index: 2;"><i
                                                                class="fas fa-trash"></i></button>
                                                        <h4 class="mb-2"
                                                            style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd; padding-right: 36px;">
                                                            Avaliação <?php echo $index + 1; ?></h4>
                                                        <div class="mb-2">
                                                            <label class="form-label">Nome</label>
                                                            <div class="name-text-display">
                                                                <?php echo htmlspecialchars($avaliacao['Nome']); ?>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Avaliação</label>
                                                            <div class="star-rating readonly"
                                                                data-rating="<?php echo htmlspecialchars($avaliacao['Estrelas']); ?>">
                                                                <span class="star" data-value="1">★</span>
                                                                <span class="star" data-value="2">★</span>
                                                                <span class="star" data-value="3">★</span>
                                                                <span class="star" data-value="4">★</span>
                                                                <span class="star" data-value="5">★</span>
                                                                <span
                                                                    class="rating-value"><?php echo htmlspecialchars($avaliacao['Estrelas']); ?>/5</span>
                                                                <input type="hidden"
                                                                    name="avaliacoes[<?php echo $index; ?>][Estrelas]"
                                                                    value="<?php echo htmlspecialchars($avaliacao['Estrelas']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Texto</label>
                                                            <div class="comment-text-display">
                                                                <?php echo htmlspecialchars($avaliacao['Texto']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
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
                                            <form id="login-form-ajax" onsubmit="return submitLoginAjax(event)">
                                                <input type="hidden" name="update_login">
                                                <input type="hidden" name="active_section" value="login-section">
                                                <div class="text-input-container">
                                                    <div class="form-group mb-4">
                                                        <label for="Titulo" class="form-label">Título</label>
                                                        <input type="text" class="form-control login-input" id="Titulo"
                                                            name="Titulo"
                                                            value="<?php echo htmlspecialchars($ctaInicio['Titulo']); ?>"
                                                            placeholder="Digite o título...">
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="texto" class="form-label">Texto</label>
                                                        <textarea class="form-control login-textarea" id="texto"
                                                            name="texto" rows="4"
                                                            placeholder="Digite o texto..."><?php echo htmlspecialchars($ctaInicio['texto']); ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="btntext" class="form-label">Texto do Botão</label>
                                                        <input type="text" class="form-control login-input" id="btntext"
                                                            name="btntext"
                                                            value="<?php echo htmlspecialchars($ctaInicio['btntext']); ?>"
                                                            placeholder="Digite o texto do botão...">
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4">
                                                    <label for="fundo" class="form-label">Imagem de Fundo</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control login-input" id="fundo"
                                                            name="fundo"
                                                            value="<?php echo htmlspecialchars($ctaInicio['fundo']); ?>"
                                                            placeholder="URL da imagem de fundo...">
                                                        <div class="image-preview" id="fundoPreview">
                                                            <img src="<?php echo htmlspecialchars($ctaInicio['fundo']); ?>"
                                                                alt="Fundo Preview" onerror="this.style.display='none'"
                                                                style="max-width: 200px; max-height: 150px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="fundo">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="button-text">Salvar Alterações</span>
                                                        <span class="button-loading" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Salvando...
                                                        </span>
                                                    </button>
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
                                    // Busca os dados das FAQs
                                    $faqsData = [];
                                    $result = $conn->query("SELECT * FROM FaqPrevisualizacaoInicio ORDER BY id");
                                    while($row = $result->fetch_assoc()) {
                                        $faqsData[] = $row;
                                    }
                                    ?>
                                            <?php if (isset($updateMessage) && isset($activeSection) && $activeSection == 'faqs-section'): ?>
                                            <div class="alert alert-<?php echo $updateType; ?> alert-dismissible fade show"
                                                role="alert">
                                                <?php echo $updateMessage; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                            <?php endif; ?>
                                            <form method="POST" id="faqsForm"
                                                onsubmit="return submitFaqsFormAjax(event)">
                                                <input type="hidden" name="active_section" value="faqs-section">
                                                <div id="faqsContainer" class="row"
                                                    style="display: flex; flex-wrap: wrap; gap: 24px;">
                                                    <?php foreach ($faqsData as $index => $faq): ?>
                                                    <div class="faq-item"
                                                        style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;">
                                                        <h4 class="mb-2"
                                                            style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd;">
                                                            FAQ <?php echo $index + 1; ?></h4>
                                                        <div class="mb-2">
                                                            <label class="form-label">Título</label>
                                                            <input type="text" class="form-control"
                                                                name="faqs[<?php echo $index; ?>][titulofaq]"
                                                                value="<?php echo htmlspecialchars($faq['titulofaq']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Texto</label>
                                                            <textarea class="form-control"
                                                                name="faqs[<?php echo $index; ?>][textofaq]" rows="3"
                                                                required><?php echo htmlspecialchars($faq['textofaq']); ?></textarea>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Link</label>
                                                            <input type="text" class="form-control"
                                                                name="faqs[<?php echo $index; ?>][Link]"
                                                                value="<?php echo htmlspecialchars($faq['Link']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Imagem</label>
                                                            <div class="image-upload-container">
                                                                <input type="text" class="form-control mb-2"
                                                                    id="faq_<?php echo $index; ?>_imagemfaq"
                                                                    name="faqs[<?php echo $index; ?>][imagemfaq]"
                                                                    value="<?php echo htmlspecialchars($faq['imagemfaq']); ?>"
                                                                    required>
                                                                <div class="image-preview mb-2">
                                                                    <img src="<?php echo htmlspecialchars($faq['imagemfaq']); ?>"
                                                                        alt="Preview"
                                                                        onerror="this.style.display='none'"
                                                                        style="max-width: 100px; max-height: 100px;">
                                                                </div>
                                                                <div class="drop-zone"
                                                                    data-target="faq_<?php echo $index; ?>_imagemfaq">
                                                                    <i class="fas fa-cloud-upload-alt mb-2"></i>
                                                                    <p class="mb-0">Arraste uma imagem aqui ou clique
                                                                        para selecionar</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="d-flex justify-content-end mt-4">
                                                    <button type="submit" name="update_faqs" class="btn btn-primary">
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
                                            <form id="aviso-form-ajax" onsubmit="return submitAvisoAjax(event)">
                                                <input type="hidden" name="update_aviso">
                                                <input type="hidden" name="active_section" value="aviso-section">
                                                <div class="text-input-container">
                                                    <div class="form-group mb-4">
                                                        <label for="texto" class="form-label">Texto do Aviso</label>
                                                        <textarea class="form-control aviso-textarea" id="texto"
                                                            name="texto" rows="4"
                                                            placeholder="Digite o texto do aviso..."><?php echo isset($avisoData['Texto']) ? htmlspecialchars($avisoData['Texto']) : ''; ?></textarea>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="textobtn" class="form-label">Texto do Botão</label>
                                                        <input type="text" class="form-control" id="textobtn"
                                                            name="textobtn"
                                                            value="<?php echo isset($avisoData['Textobtn']) ? htmlspecialchars($avisoData['Textobtn']) : ''; ?>"
                                                            placeholder="Digite o texto do botão...">
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="button-text">Salvar Alterações</span>
                                                        <span class="button-loading" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Salvando...
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Footer (escondida por padrão) -->
                    <div id="footer-section" class="content-section" style="display: none;">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Editar Footer</h3>
                                        </div>
                                        <div class="card-body">
                                            <!-- Formulários de Edição do Footer -->
                                            <form id="footer-form-ajax" onsubmit="return submitFooterAjax(event)">
                                                <input type="hidden" name="update_footer_links">
                                                <input type="hidden" name="active_section" value="footer-section">

                                                <!-- Seção Ligações Úteis -->
                                                <div class="mb-5">
                                                    <h4 class="mb-3">Ligações Úteis</h4>
                                                    <div id="ligacoes-container">
                                                        <?php 
                                $ligacoes = array_filter($footerLinksData, function($link) {
                                    return $link['secao'] === 'LigacoesUteis';
                                });
                                foreach ($ligacoes as $index => $link): 
                                ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index; ?>][nome]"
                                                                    value="<?php echo htmlspecialchars($link['nome']); ?>"
                                                                    placeholder="Nome do link">
                                                                <input type="hidden"
                                                                    name="footer_links[<?php echo $index; ?>][secao]"
                                                                    value="LigacoesUteis">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="url" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index; ?>][link]"
                                                                    value="<?php echo htmlspecialchars($link['link']); ?>"
                                                                    placeholder="https://">
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- Seção Contactos -->
                                                <div class="mb-5">
                                                    <h4 class="mb-3 mt-5">Contactos</h4>
                                                    <div id="contactos-container">
                                                        <?php 
                                $contactos = array_filter($footerLinksData, function($link) {
                                    return $link['secao'] === 'Contactos';
                                });
                                foreach ($contactos as $index => $link): 
                                ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 1000; ?>][nome]"
                                                                    value="<?php echo htmlspecialchars($link['nome']); ?>"
                                                                    placeholder="Tipo de contacto">
                                                                <input type="hidden"
                                                                    name="footer_links[<?php echo $index + 1000; ?>][secao]"
                                                                    value="Contactos">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 1000; ?>][link]"
                                                                    value="<?php echo htmlspecialchars($link['link']); ?>"
                                                                    placeholder="Informação de contacto">
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- Seção FAQ's -->
                                                <div class="mb-5">
                                                    <h4 class="mb-3 mt-5">FAQ's</h4>
                                                    <div id="faqs-container">
                                                        <?php 
                                $faqs = array_filter($footerLinksData, function($link) {
                                    return $link['secao'] === 'Faqs';
                                });
                                foreach ($faqs as $index => $link): 
                                ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 2000; ?>][nome]"
                                                                    value="<?php echo htmlspecialchars($link['nome']); ?>"
                                                                    placeholder="Pergunta">
                                                                <input type="hidden"
                                                                    name="footer_links[<?php echo $index + 2000; ?>][secao]"
                                                                    value="Faqs">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="url" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 2000; ?>][link]"
                                                                    value="<?php echo htmlspecialchars($link['link']); ?>"
                                                                    placeholder="https://">
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- Seção Tickets -->
                                                <div class="mb-5">
                                                    <h4 class="mb-3 mt-5">Tickets</h4>
                                                    <div id="tickets-container">
                                                        <?php 
                                $tickets = array_filter($footerLinksData, function($link) {
                                    return $link['secao'] === 'Tickets';
                                });
                                foreach ($tickets as $index => $link): 
                                ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 3000; ?>][nome]"
                                                                    value="<?php echo htmlspecialchars($link['nome']); ?>"
                                                                    placeholder="Nome do link">
                                                                <input type="hidden"
                                                                    name="footer_links[<?php echo $index + 3000; ?>][secao]"
                                                                    value="Tickets">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="url" class="form-control mb-2"
                                                                    name="footer_links[<?php echo $index + 3000; ?>][link]"
                                                                    value="<?php echo htmlspecialchars($link['link']); ?>"
                                                                    placeholder="https://">
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between">
                                                    <button type="submit" class="btn btn-primary">Salvar Links do
                                                        Footer</button>
                                                </div>
                                            </form>

                                            <!-- Seção Copyright removida conforme solicitado -->

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
                                            alt="<?php echo htmlspecialchars($post['titulo']); ?>" loading="lazy">
                                    </div>
                                    <div class="post-content">
                                        <h3 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                                        <p class="post-excerpt">
                                            <?php echo htmlspecialchars(substr($post['texto'], 0, 80)) . '...'; ?></p>

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
                                                <i class="fas fa-user"></i>
                                                <?php echo htmlspecialchars($post['autor_nome']); ?>
                                            </span>
                                            <span class="post-date">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('d/m/Y', strtotime($post['data_criacao'])); ?>
                                            </span>
                                            <span class="post-comments">
                                                <i class="fas fa-comments"></i> <?php echo $post['num_comentarios']; ?>
                                            </span>
                                        </div>

                                        <div class="post-actions">
                                            <button class="btn btn-primary"
                                                onclick="editPost(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-danger"
                                                onclick="deletePost(<?php echo $post['id']; ?>)">
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
                                            <a class="page-link" href="#" data-page="<?php echo $page - 1; ?>"
                                                aria-label="Anterior">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="#"
                                                data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="#" data-page="<?php echo $page + 1; ?>"
                                                aria-label="Próximo">
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
                                                <input type="hidden" name="autor_id" id="postAutorId"
                                                    value="<?php echo $_SESSION['user_id']; ?>">
                                                <div class="mb-3">
                                                    <label for="postTitulo" class="form-label">Título</label>
                                                    <input type="text" class="form-control" id="postTitulo"
                                                        name="titulo" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="postTexto" class="form-label">Texto</label>
                                                    <textarea class="form-control" id="postTexto" name="texto" rows="6"
                                                        required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="postTags" class="form-label">Tags (separadas por
                                                        vírgula)</label>
                                                    <input type="text" class="form-control" id="postTags" name="tags"
                                                        placeholder="ex: Moodle, GIAE, Suporte">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Imagem Principal</label>
                                                    <div class="image-upload-container">
                                                        <input type="text" class="form-control mb-2"
                                                            id="postImgPrincipal" name="img_principal">
                                                        <div class="image-preview mb-2" id="postImgPrincipalPreview">
                                                            <img src="" alt="Preview"
                                                                style="display: none; max-width: 200px; max-height: 150px;">
                                                        </div>
                                                        <div class="drop-zone" data-target="postImgPrincipal">
                                                            Arraste uma imagem aqui ou clique para selecionar
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3" style="margin-bottom: 2rem;">
                                                    <label class="form-label">Imagens Adicionais</label>
                                                    <div class="row">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="image-upload-container">
                                                                <input type="text" class="form-control mb-2"
                                                                    id="postImg<?php echo $i; ?>"
                                                                    name="img_<?php echo $i; ?>">
                                                                <div class="image-preview mb-2"
                                                                    id="postImg<?php echo $i; ?>Preview">
                                                                    <img src="" alt="Preview"
                                                                        style="display: none; max-width: 200px; max-height: 150px;">
                                                                </div>
                                                                <div class="drop-zone"
                                                                    data-target="postImg<?php echo $i; ?>">
                                                                    Arraste uma imagem aqui ou clique para selecionar
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <div class="mb-3" style="margin-top: 2.5rem; margin-bottom: 2rem;">
                                                    <label class="form-label">Data de Criação</label>
                                                    <input type="text" class="form-control" id="postDataCriacao"
                                                        name="data_criacao" readonly>
                                                </div>
                                                <div class="d-flex justify-content-between"
                                                    style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid #e3e6f0;">
                                                    <button type="button" class="btn btn-secondary"
                                                        id="cancelPostEdit">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        id="savePostBtn">Criar</button>
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
                                            <input type="text" id="userSearch" class="form-control"
                                                placeholder="Pesquisar utilizadores...">
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
                                            <input type="text" id="suggestionSearchInput" class="form-control"
                                                placeholder="Pesquisar sugestões...">
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

                    <!-- Seção de Gerir Pedidos (escondida por padrão) -->
                    <div id="tickets-section" class="content-section" style="display: none;">
                        <div class="container-fluid">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h2 class="mb-0">Gerir Pedidos de Reparação</h2>
                                </div>
                            </div>
                            <div class="row" id="admin-tickets-cards">
                                <?php
                        $sql = "SELECT t.*, u.Nome as NomeAluno, u.Email as EmailAluno, u.Tipo_Utilizador as TipoAluno, u.Estado as EstadoAluno FROM Tickets t LEFT JOIN Utilizadores u ON t.ID_Utilizador = u.ID_Utilizador ORDER BY t.Data_Submissao DESC";
                        $result = $conn->query($sql);
                        if ($result->num_rows === 0): ?>
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fa fa-info-circle me-3 fs-4"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Nenhum pedido de reparação encontrado</h5>
                                            <p class="mb-0">Ainda não existem tickets submetidos por alunos.</p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php while ($ticket = $result->fetch_assoc()):
                            $dataSub = date('d/m/Y H:i', strtotime($ticket['Data_Submissao']));
                            $dataMarc = $ticket['Data_Marcada'] ? date('d/m/Y H:i', strtotime($ticket['Data_Marcada'])) : '-';
                            $estado = $ticket['Estado'];
                            // Estado visual
                            $estadoClass = '';
                            $estadoIcon = '';
                            switch (strtolower($estado)) {
                                case 'pendente': $estadoClass = 'pendente'; $estadoIcon = 'fa-clock'; break;
                                case 'aceite': $estadoClass = 'aceite'; $estadoIcon = 'fa-check'; break;
                                case 'rejeitado': $estadoClass = 'rejeitado'; $estadoIcon = 'fa-times-circle'; break;
                                case 'concluído':
                                case 'concluido': $estadoClass = 'concluido'; $estadoIcon = 'fa-check-circle'; break;
                                default: $estadoClass = 'pendente'; $estadoIcon = 'fa-clock';
                            }
                        ?>
                                <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                    <div class="card shadow-sm h-100 border-0 admin-ticket-card">
                                        <div class="card-header">
                                            <span class="badge-estado <?= $estadoClass ?>">
                                                <i class="fa <?= $estadoIcon ?> me-1"></i>
                                                <?= htmlspecialchars($estado) ?>
                                            </span>
                                            <span class="text-muted small"><i class="fa fa-calendar me-1"></i>
                                                <?= $dataSub ?></span>
                                        </div>
                                        <div class="card-body pt-2">
                                            <!-- Bloco: Dados do Utilizador -->
                                            <div class="mb-4">
                                                <h6 class="section-title mb-3 text-primary" style="font-size:1.05em;"><i
                                                        class="fa fa-user me-1"></i> Utilizador</h6>
                                                <div class="d-flex flex-wrap align-items-center gap-4">
                                                    <div class="mb-2"><span class="info-label">Nome:</span> <span
                                                            class="info-value ms-1"><?= htmlspecialchars($ticket['NomeAluno']) ?></span>
                                                    </div>
                                                    <div class="mb-2"><span class="info-label">Email:</span> <span
                                                            class="info-value ms-1"><?= htmlspecialchars($ticket['EmailAluno']) ?></span>
                                                    </div>
                                                    <div class="mb-2"><span class="info-label">Tipo:</span> <span
                                                            class="info-value ms-1"><?= htmlspecialchars($ticket['TipoAluno']) ?></span>
                                                    </div>
                                                    <div class="mb-2"><span class="info-label">Estado:</span> <span
                                                            class="info-value ms-1"><?= htmlspecialchars($ticket['EstadoAluno']) ?></span>
                                                    </div>
                                                    <div class="mb-2"><span class="info-label">Nº Processo:</span> <span
                                                            class="info-value ms-1">#<?= htmlspecialchars($ticket['Numero_Processo_Aluno']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bloco: Dados do Equipamento -->
                                            <div class="mb-4">
                                                <h6 class="section-title mb-3 text-primary" style="font-size:1.05em;"><i
                                                        class="fa fa-laptop me-1"></i> Equipamento</h6>
                                                <div class="">
                                                    <div class="mb-2"><span class="info-label">Tipo:</span> <span
                                                            class="info-value ms-1"><?= htmlspecialchars($ticket['Tipo_Equipamento']) ?></span>
                                                    </div>
                                                    <?php if ($ticket['Numero_Serie']): ?>
                                                    <div class="mb-2"><span class="info-label">Nº Série:</span> <span
                                                            class="info-value ms-1">#<?= htmlspecialchars($ticket['Numero_Serie']) ?></span>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <!-- Bloco: Dados do Pedido -->
                                            <div class="mb-4">
                                                <h6 class="section-title mb-3 text-primary" style="font-size:1.05em;"><i
                                                        class="fa fa-ticket-alt me-1"></i> Pedido</h6>
                                                <div class="mb-2"><span class="info-label">Título:</span> <span
                                                        class="info-value ms-1"><?= htmlspecialchars($ticket['Titulo']) ?></span>
                                                </div>
                                                <div class="mb-2"><span class="info-label">Data Agendada:</span> <span
                                                        class="info-value ms-1"><?= $dataMarc ?></span></div>
                                                <div class="mb-2"><span class="info-label">Descrição:</span></div>
                                                <div class="p-2 bg-light rounded info-value mb-2"
                                                    style="white-space:pre-line;">
                                                    <?= nl2br(htmlspecialchars($ticket['Descricao'])) ?>
                                                </div>
                                            </div>
                                            <div class="admin-ticket-actions">
                                                <button class="btn btn-success"
                                                    onclick="updateTicketStatus(<?= $ticket['ID_Ticket'] ?>, 'Aceite')">
                                                    <i class="fa fa-check"></i> Aceitar
                                                </button>
                                                <button class="btn btn-danger"
                                                    onclick="updateTicketStatus(<?= $ticket['ID_Ticket'] ?>, 'Rejeitado')">
                                                    <i class="fa fa-times"></i> Rejeitar
                                                </button>
                                                <button class="btn btn-primary"
                                                    onclick="updateTicketStatus(<?= $ticket['ID_Ticket'] ?>, 'Concluído')">
                                                    <i class="fa fa-check-circle"></i> Concluído
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
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
                        <p>Copyright &copy;<script>
                            document.write(new Date().getFullYear());
                            </script>
                            Todos os direitos reservados | Criado com <i class="fa fa-heart-o" aria-hidden="true"></i>
                            por
                            <a href="https://www.linkedin.com/in/tom%C3%A1s-n%C3%A1poles-087517233/"
                                target="_blank">Tomás Nápoles
                            </a> &amp; <a href="#" target="_blank">Salvador Coimbras</a></a>
                        </p>
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
    <div class="modal fade" id="renameImageModal" tabindex="-1" aria-labelledby="renameImageModalLabel"
        aria-hidden="true">
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
    <div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="user-modal-title" aria-hidden="true"
        style="display: none;">
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
                            <small class="form-text text-muted">Deixe em branco para manter a senha atual ao
                                editar.</small>
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
                <button type="button" class="btn-delete-suggestion" onclick="deleteSuggestion()"
                    style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; transition: background-color 0.2s ease; margin-right: 10px;">
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

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }

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

    /* CSS para Modal de Confirmação Simples */
    .custom-confirm-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        animation: fadeIn 0.2s ease forwards;
    }

    .custom-confirm-modal {
        background: #fff;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transform: scale(0.9);
        animation: modalSlideIn 0.2s ease forwards;
        border: 1px solid #dee2e6;
    }

    .custom-confirm-header {
        padding: 20px 24px 16px 24px;
        text-align: center;
        border-bottom: 1px solid #e9ecef;
    }

    .custom-confirm-icon {
        font-size: 2rem;
        color: #ffc107;
        margin-bottom: 8px;
        display: block;
    }

    .custom-confirm-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: #343a40;
    }

    .custom-confirm-body {
        padding: 20px 24px;
        text-align: center;
    }

    .custom-confirm-body p {
        margin: 0;
        font-size: 1rem;
        color: #6c757d;
        line-height: 1.5;
    }

    .custom-confirm-footer {
        padding: 16px 24px 20px 24px;
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .custom-confirm-footer .btn {
        min-width: 100px;
        padding: 8px 16px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .custom-confirm-cancel {
        background: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        opacity: 0.8;
    }

    .custom-confirm-cancel:hover {
        background: #5a6268 !important;
        border-color: #545b62 !important;
        color: white !important;
        opacity: 1;
    }

    .custom-confirm-ok {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    .custom-confirm-ok:hover {
        background: #c82333 !important;
        border-color: #bd2130 !important;
    }

    /* Animações Simples */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes modalSlideIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    @keyframes modalSlideOut {
        from {
            transform: scale(1);
            opacity: 1;
        }

        to {
            transform: scale(0.9);
            opacity: 0;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }

        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    /* Responsivo */
    @media (max-width: 480px) {
        .custom-confirm-modal {
            width: 320px;
        }

        .custom-confirm-footer {
            flex-direction: column;
        }

        .custom-confirm-footer .btn {
            width: 100%;
        }
    }
    </style>

    <!-- Modal de Confirmação Simples -->
    <div id="customConfirmModal" class="custom-confirm-overlay" style="display: none;">
        <div class="custom-confirm-modal">
            <div class="custom-confirm-header">
                <i class="fas fa-exclamation-triangle custom-confirm-icon"></i>
                <h4 class="custom-confirm-title">Confirmar</h4>
            </div>
            <div class="custom-confirm-body">
                <p id="customConfirmMessage">Tem certeza que deseja realizar esta ação?</p>
            </div>
            <div class="custom-confirm-footer">
                <button type="button" class="btn btn-secondary custom-confirm-cancel"
                    onclick="closeCustomConfirm(false)">
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger custom-confirm-ok" onclick="closeCustomConfirm(true)">
                    Confirmar
                </button>
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

    <script src="js/inicio.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página carregada');

        // Debug: Verificar se existe seção ativa definida pelo PHP
        <?php if (isset($activeSection)): ?>
        console.log('DEBUG: activeSection definida no PHP como:', '<?php echo $activeSection; ?>');
        <?php else: ?>
        console.log('DEBUG: Nenhuma activeSection definida no PHP');
        <?php endif; ?>

        // Função auxiliar para mostrar alertas
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.maxWidth = '400px';
            alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
            document.body.appendChild(alertDiv);

            // Remove automaticamente após 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Expõe a função globalmente
        window.showAlert = showAlert;

        // Funções para o modal de confirmação customizado
        let customConfirmResolve = null;

        function showCustomConfirm(message) {
            return new Promise((resolve) => {
                customConfirmResolve = resolve;

                // Define a mensagem
                document.getElementById('customConfirmMessage').textContent = message;

                // Mostra o modal
                const modal = document.getElementById('customConfirmModal');
                modal.style.display = 'flex';

                // Foca no botão cancelar para acessibilidade
                setTimeout(() => {
                    document.querySelector('.custom-confirm-cancel').focus();
                }, 150);
            });
        }

        function closeCustomConfirm(result) {
            const modal = document.getElementById('customConfirmModal');

            // Adiciona animação de saída
            modal.style.animation = 'fadeOut 0.2s ease forwards';
            modal.querySelector('.custom-confirm-modal').style.animation = 'modalSlideOut 0.2s ease forwards';

            setTimeout(() => {
                modal.style.display = 'none';
                modal.style.animation = '';
                modal.querySelector('.custom-confirm-modal').style.animation = '';

                if (customConfirmResolve) {
                    customConfirmResolve(result);
                    customConfirmResolve = null;
                }
            }, 150);
        }

        // Expõe as funções globalmente
        window.showCustomConfirm = showCustomConfirm;
        window.closeCustomConfirm = closeCustomConfirm;

        // Fechar modal clicando no overlay
        document.getElementById('customConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomConfirm(false);
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('customConfirmModal').style.display ===
                'flex') {
                closeCustomConfirm(false);
            }
        });



        // Função para submeter FAQs via AJAX (sem sair da seção)
        window.submitFaqsFormAjax = function(event) {
            event.preventDefault(); // Impede o submit normal

            const form = document.getElementById('faqsForm');
            const formData = new FormData(form);
            formData.append('update_faqs', '1'); // Adiciona o campo necessário

            // Mostra loading no botão
            const submitBtn = form.querySelector('button[name="update_faqs"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            submitBtn.disabled = true;

            fetch('update_faqs_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('Erro ao salvar FAQs: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restaura o botão
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });

            return false; // Impede qualquer redirecionamento
        };

        // Função para submeter Avaliações via AJAX (sem sair da seção)
        window.submitAvaliacoesAjax = function(event) {
            event.preventDefault(); // Impede o submit normal

            const form = document.getElementById('avaliacoes-form-ajax');
            const formData = new FormData(form);

            // Mostra loading no botão
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline';
            submitBtn.disabled = true;

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Se chegou aqui, deu tudo certo
                    showAlert('Avaliações atualizadas com sucesso!', 'success');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('Erro ao salvar avaliações: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restaura o botão
                    buttonText.style.display = 'inline';
                    buttonLoading.style.display = 'none';
                    submitBtn.disabled = false;
                });

            return false; // Impede qualquer redirecionamento
        };

        // Inicializa os ícones do Feather
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Inicializar estrelas após carregar a página
        setTimeout(() => {
            // Inicializar estrelas readonly
            if (typeof initReadonlyStars === 'function') {
                initReadonlyStars();
            }
            // Inicializar estrelas editáveis
            document.querySelectorAll('.star-rating:not(.readonly)').forEach(container => {
                if (typeof initStarRating === 'function') {
                    initStarRating(container);
                }
            });
        }, 100);

        // Função para mostrar/esconder seções
        function showSection(sectionId) {
            console.log('showSection chamada para:', sectionId);

            // Debug específico para footer
            if (sectionId === 'footer-section') {
                console.log('🚨 FOOTER SECTION SENDO ATIVADA 🚨');
            }

            // Esconde todas as seções
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
                console.log('Escondendo seção:', section.id);
            });

            // Mostra a seção selecionada
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
                console.log('Seção mostrada com sucesso:', sectionId);

                // Debug específico para footer
                if (sectionId === 'footer-section') {
                    console.log('✅ FOOTER SECTION ATIVADA COM SUCESSO!');
                    // Força scroll para o topo da seção
                    selectedSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }

                // Remove classes ativas dos links do menu
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Adiciona classe ativa ao link correspondente
                const activeLink = document.querySelector(`[onclick*="${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                    console.log('Link do menu ativado para:', sectionId);
                }

                // Inicializar estrelas quando a seção de avaliações for mostrada
                if (sectionId === 'avaliacoes-section') {
                    setTimeout(() => {
                        // Inicializar estrelas readonly
                        if (typeof initReadonlyStars === 'function') {
                            initReadonlyStars();
                            console.log('⭐ Estrelas readonly inicializadas na seção avaliações');
                        }
                        // Inicializar estrelas editáveis
                        document.querySelectorAll('.star-rating:not(.readonly)').forEach(container => {
                            if (typeof initStarRating === 'function') {
                                initStarRating(container);
                            }
                        });
                    }, 100);
                }
            } else {
                console.error('Seção não encontrada:', sectionId);
                console.log('Seções disponíveis:', Array.from(document.querySelectorAll('.content-section'))
                    .map(s => s.id));
            }
        }

        // Adiciona event listeners aos links do menu
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                showSection(sectionId);
            });
        });

        // Verifica se há uma seção ativa definida (após form submit)
        <?php if (isset($activeSection)): ?>
        console.log('Seção ativa definida pelo PHP:', '<?php echo $activeSection; ?>');
        // Força imediatamente a exibição da seção correta
        showSection('<?php echo $activeSection; ?>');

        // Força novamente após um delay para garantir
        setTimeout(() => {
            showSection('<?php echo $activeSection; ?>');
            console.log('Seção forçada novamente:', '<?php echo $activeSection; ?>');
        }, 100);
        <?php else: ?>
        console.log('Nenhuma seção ativa definida, mostrando welcome-section');
        // Mostra a seção de boas-vindas por padrão
        showSection('welcome-section');
        <?php endif; ?>

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

        // Função AJAX para enviar o formulário footer SEM redirecionamento
        window.submitFooterAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 FOOTER AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('footer-form-ajax');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

            // Envia via AJAX
            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    console.log('✅ FOOTER SALVO COM SUCESSO VIA AJAX!');

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-check-circle"></i> Links do Footer atualizados com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Salvar Links do Footer';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção footer
                    showSection('footer-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO FOOTER - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar footer:', error);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Salvar Links do Footer';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // JavaScript para garantir que o footer funcione via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 FOOTER AJAX SYSTEM ATIVO!');

            // Força a exibição da seção footer se necessário (apenas para compatibilidade)
            <?php if (isset($activeSection) && $activeSection == 'footer-section'): ?>
            console.log('🔥 EXIBINDO SEÇÃO FOOTER 🔥');
            showSection('footer-section');
            <?php endif; ?>
        });

        // Função AJAX para enviar o formulário do aviso SEM redirecionamento
        window.submitAvisoAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 AVISO AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('aviso-form-ajax');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline-block';

            // Envia via AJAX
            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    console.log('✅ AVISO SALVO COM SUCESSO VIA AJAX!');

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-check-circle"></i> Aviso atualizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção aviso
                    showSection('aviso-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO AVISO - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar aviso:', error);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // JavaScript para garantir que o aviso funcione via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 AVISO AJAX SYSTEM ATIVO!');

            // Força a exibição da seção aviso se necessário (apenas para compatibilidade)
            <?php if (isset($activeSection) && $activeSection == 'aviso-section'): ?>
            console.log('🔥 EXIBINDO SEÇÃO AVISO 🔥');
            showSection('aviso-section');
            <?php endif; ?>
        });

        // Função AJAX para enviar o formulário do login SEM redirecionamento
        window.submitLoginAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 LOGIN AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('login-form-ajax');
            const formData = new FormData(form);

            // DEBUG: Verificar os dados que estão sendo enviados
            console.log('📊 DADOS DO FORMULÁRIO LOGIN:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline-block';

            // Envia via AJAX
            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('📡 RESPOSTA RECEBIDA - Status:', response.status);
                    console.log('📡 RESPOSTA HEADERS:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log('✅ LOGIN SALVO COM SUCESSO VIA AJAX!');
                    console.log('📄 RESPOSTA HTML (primeiros 500 chars):', html.substring(0, 500));

                    // Verificar se há mensagens de erro no HTML
                    if (html.includes('erro') || html.includes('error')) {
                        console.warn('⚠️ POSSÍVEL ERRO DETECTADO NA RESPOSTA');
                    }

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-check-circle"></i> Login Rápido atualizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção login
                    showSection('login-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO LOGIN - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar login:', error);
                    console.error('🔍 DETALHES DO ERRO:', error.stack);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // JavaScript para garantir que o login funcione via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 LOGIN AJAX SYSTEM ATIVO!');

            // Força a exibição da seção login se necessário (apenas para compatibilidade)
            <?php if (isset($activeSection) && $activeSection == 'login-section'): ?>
            console.log('🔥 EXIBINDO SEÇÃO LOGIN 🔥');
            showSection('login-section');
            <?php endif; ?>
        });

        // Função AJAX para enviar o formulário do sobre nós SEM redirecionamento
        window.submitSobreAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 SOBRE NÓS AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('sobre-form-ajax');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline-block';

            // Envia para arquivo dedicado AJAX
            fetch('update_sobre_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('📡 RESPOSTA RECEBIDA - Status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ RESPOSTA JSON RECEBIDA:', data);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Determina o tipo de alerta baseado no sucesso
                    const alertType = data.success ? 'success' : 'danger';
                    const iconClass = data.success ? 'fa-check-circle' : 'fa-exclamation-circle';

                    // Mostra mensagem
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${alertType} alert-dismissible fade show`;
                    alert.innerHTML = `
                <i class="fas ${iconClass}"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção sobre nós
                    showSection('sobre-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO SOBRE NÓS - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar sobre nós:', error);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // Função AJAX para enviar o formulário das ligações rápidas SEM redirecionamento
        window.submitLinksAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 LIGAÇÕES RÁPIDAS AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('links-form-ajax');
            const formData = new FormData(form);

            // Debug: Mostrar dados que estão sendo enviados
            console.log('📊 DADOS DO FORMULÁRIO:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline-block';

            // Envia para arquivo dedicado AJAX
            fetch('update_links.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('📡 RESPOSTA RECEBIDA - Status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ RESPOSTA JSON RECEBIDA:', data);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Determina o tipo de alerta baseado no sucesso
                    const alertType = data.success ? 'success' : 'danger';
                    const iconClass = data.success ? 'fa-check-circle' : 'fa-exclamation-circle';

                    // Mostra mensagem
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${alertType} alert-dismissible fade show`;
                    alert.innerHTML = `
                <i class="fas ${iconClass}"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção ligações rápidas
                    showSection('links-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO LIGAÇÕES RÁPIDAS - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar ligações rápidas:', error);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // Função AJAX para enviar o formulário do início SEM redirecionamento
        window.submitInicioAjax = function(event) {
            event.preventDefault(); // IMPEDE o envio normal
            console.log('🚨 INÍCIO AJAX SUBMIT INICIADO 🚨');

            const form = document.getElementById('inicio-form-ajax');
            const formData = new FormData(form);

            // Debug: Mostrar dados que estão sendo enviados
            console.log('📊 DADOS DO FORMULÁRIO INÍCIO:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const buttonText = submitBtn.querySelector('.button-text');
            const buttonLoading = submitBtn.querySelector('.button-loading');

            // Desabilita o botão durante o envio
            submitBtn.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline-block';

            // Envia para arquivo dedicado AJAX
            fetch('update_inicio_ajax.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('📡 RESPOSTA RECEBIDA - Status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ RESPOSTA JSON RECEBIDA:', data);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Determina o tipo de alerta baseado no sucesso
                    const alertType = data.success ? 'success' : 'danger';
                    const iconClass = data.success ? 'fa-check-circle' : 'fa-exclamation-circle';

                    // Mostra mensagem
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${alertType} alert-dismissible fade show`;
                    alert.innerHTML = `
                <i class="fas ${iconClass}"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Adiciona a mensagem no topo do formulário
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    // Remove a mensagem após 5 segundos
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);

                    // GARANTE que permaneça na seção início
                    showSection('inicio-section');
                    console.log('🏠 PERMANECENDO NA SEÇÃO INÍCIO - SEM REDIRECIONAMENTO!');
                })
                .catch(error => {
                    console.error('❌ Erro ao salvar início:', error);

                    // Remove qualquer mensagem anterior para evitar duplicações
                    const oldAlerts = form.parentNode.querySelectorAll('.alert');
                    oldAlerts.forEach(oldAlert => oldAlert.remove());

                    // Mostra mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i> Erro ao salvar: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
                    form.parentNode.insertBefore(alert, form);

                    // Reabilita o botão
                    submitBtn.disabled = false;
                    buttonText.style.display = 'inline-block';
                    buttonLoading.style.display = 'none';

                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                });

            return false; // IMPEDE completamente o envio normal
        };

        // JavaScript para garantir que o sobre nós funcione via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 SOBRE NÓS AJAX SYSTEM ATIVO!');

            // Força a exibição da seção sobre nós se necessário
            <?php if (isset($activeSection) && $activeSection == 'sobre-section'): ?>
            console.log('🔥 EXIBINDO SEÇÃO SOBRE NÓS 🔥');
            showSection('sobre-section');
            <?php endif; ?>
        });

        // Função para fazer upload do arquivo
        async function handleFileUpload(file, inputId, customName = null) {
            console.log('handleFileUpload chamada com:', {
                fileName: file.name,
                inputId,
                customName
            });

            if (file && file.type.startsWith('image/')) {
                const formData = new FormData();
                formData.append('image', file);

                // Tenta encontrar o input por ID, se não encontrar, tenta pela drop zone
                let input = document.getElementById(inputId);
                console.log('Input encontrado por ID:', !!input);

                if (!input) {
                    // Se não encontrar por ID, procura pela drop zone correspondente
                    const dropZone = document.querySelector(`[data-target="${inputId}"]`);
                    console.log('Drop zone encontrada:', !!dropZone);
                    if (dropZone) {
                        const container = dropZone.closest('.image-upload-container');
                        if (container) {
                            input = container.querySelector('input[type="text"]');
                            console.log('Input encontrado via drop zone:', !!input);
                        }
                    }
                }

                // Adiciona a imagem antiga ao FormData
                if (input && input.value) {
                    formData.append('old_image', input.value);
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
                        if (input) {
                            input.value = data.url;
                            // Atualiza o preview imediatamente após definir o valor
                            const previewContainer = input.closest('.image-upload-container').querySelector(
                                '.image-preview');
                            if (previewContainer) {
                                const preview = previewContainer.querySelector('img');
                                if (preview) {
                                    preview.src = data.url;
                                    preview.style.display = 'block';
                                }
                            }

                            // Mostra mensagem de sucesso
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                            alert.innerHTML = `
                            <i class="fas fa-check-circle"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                            input.closest('.image-upload-container').appendChild(alert);

                            console.log('Upload bem-sucedido! URL da imagem:', data.url);

                            // Remove a mensagem após 3 segundos
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        } else {
                            console.error('Input não encontrado para:', inputId);
                        }
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

                    if (input) {
                        input.closest('.image-upload-container').appendChild(alert);
                    } else {
                        // Se não conseguir encontrar o input, mostra no topo da seção
                        const faqsSection = document.getElementById('faqs-section');
                        if (faqsSection) {
                            faqsSection.querySelector('.card-body').prepend(alert);
                        }
                    }

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
                if (input.name && (input.name.includes('imagemfaq') || input.name.includes(
                        'Imagem'))) {
                    input.addEventListener('change', function() {
                        const previewContainer = this.parentNode.querySelector(
                            '.image-preview');
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
                    console.log('Arquivo selecionado:', file.name, 'Input ID:', inputId);
                    currentFile = file;
                    currentInputId = inputId;
                    showRenameModal(file);
                }
            });
        });

        // Função clearAllFields removida - botão "Apagar tudo" foi removido da seção início

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



        // ---------------------------- Funções para gerenciar avaliações ----------------------------
        function addNewAvaliacao() {
            const container = document.getElementById('avaliacoesContainer');
            const index = container.children.length;
            const newCard = document.createElement('div');
            newCard.className = 'avaliacao-item';
            newCard.style =
                'background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;';
            newCard.innerHTML =
                `
            <button type=\"button\" class=\"remove-link-btn\" onclick=\"removeAvaliacao(this)\" title=\"Remover avaliação\" style=\"position: absolute; top: 10px; right: 10px; background: #dc3545; border: none; color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; opacity: 0.85; transition: background 0.2s, opacity 0.2s; z-index: 2;\"><i class='fas fa-trash'></i></button>
            <h4 class=\"mb-2\" style=\"font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; margin-top: 0; color: #0d6efd; padding-right: 36px;\">Avaliação ${index + 1}</h4>
            <div class=\"mb-2\">\n        <label class=\"form-label\">Nome</label>\n        <input type=\"text\" class=\"form-control\" name=\"avaliacoes[${index}][Nome]\" required>\n    </div>\n    <div class=\"mb-2\">\n        <label class=\"form-label\">Avaliação</label>\n        <div class=\"star-rating\" data-rating=\"${avaliacao['Estrelas']}\">\n            <span class=\"star\" data-value=\"1\">★</span>\n            <span class=\"star\" data-value=\"2\">★</span>\n            <span class=\"star\" data-value=\"3\">★</span>\n            <span class=\"star\" data-value=\"4\">★</span>\n            <span class=\"star\" data-value=\"5\">★</span>\n            <span class=\"rating-value\">${avaliacao['Estrelas']}/5</span>\n            <input type=\"hidden\" name=\"avaliacoes[${index}][Estrelas]\" value=\"${avaliacao['Estrelas']}\">\n        </div>\n    </div>\n    <div class=\"mb-2\">\n        <label class=\"form-label\">Texto</label>\n        <textarea class=\"form-control\" name=\"avaliacoes[${index}][Texto]\" rows=\"3\" required>${avaliacao['Texto']}</textarea>\n    </div>\n`;
            container.appendChild(newCard);
            updateAvaliacaoNumbers();
            initStarRating(newCard.querySelector('.star-rating'));
            initDragAndDrop();
        }

        // Exponha a função globalmente para que o onclick possa acessá-la
        window.addNewAvaliacao = addNewAvaliacao;

        async function removeAvaliacao(button) {
            const confirmed = await showCustomConfirm(
                'Tem certeza que deseja remover esta avaliação? Esta ação não pode ser desfeita.');
            if (confirmed) {
                const avaliacaoCard = button.closest('.avaliacao-item');
                const avaliacaoId = avaliacaoCard.getAttribute('data-id');

                if (!avaliacaoId) {
                    showAlert('Erro: ID da avaliação não encontrado!', 'danger');
                    return;
                }

                // Mostrar loading no botão
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                try {
                    // Fazer chamada AJAX para apagar da BD
                    const formData = new FormData();
                    formData.append('id', avaliacaoId);

                    console.log('🚀 Enviando pedido para delete_avaliacao.php com ID:', avaliacaoId);

                    const response = await fetch('delete_avaliacao.php', {
                        method: 'POST',
                        body: formData
                    });

                    console.log('📡 Response status:', response.status);
                    console.log('📡 Response headers:', response.headers);

                    const responseText = await response.text();
                    console.log('📄 Response text:', responseText);

                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('❌ Erro ao fazer parse do JSON:', parseError);
                        console.error('📄 Response original:', responseText);
                        throw new Error('Resposta inválida do servidor: ' + responseText.substring(0, 100));
                    }

                    if (data.success) {
                        // Remover o card visualmente com animação
                        avaliacaoCard.style.animation = 'fadeOut 0.3s ease';
                        setTimeout(() => {
                            avaliacaoCard.remove();
                            updateAvaliacaoNumbers();
                            showAlert(data.message, 'success');
                        }, 300);
                    } else {
                        // Restaurar botão em caso de erro
                        button.innerHTML = '<i class="fas fa-trash"></i>';
                        button.disabled = false;
                        showAlert('Erro ao apagar: ' + data.message, 'danger');
                    }
                } catch (error) {
                    // Restaurar botão em caso de erro
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                    button.disabled = false;
                    console.error('Erro na requisição:', error);
                    showAlert('Erro de conexão: ' + error.message, 'danger');
                }
            }
        }

        // Exponha a função globalmente para que o onclick possa acessá-la
        window.removeAvaliacao = removeAvaliacao;

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
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });

            // Se for readonly, só mostra as estrelas, sem interação
            if (container.classList.contains('readonly')) {
                return; // Para por aqui, sem adicionar eventos de clique
            }

            // Clique para selecionar (apenas para não-readonly)
            stars.forEach((star, index) => {
                star.onclick = function() {
                    const value = index + 1;
                    hiddenInput.value = value;
                    currentValue = value;
                    stars.forEach((s, i) => {
                        if (i < value) s.classList.add('active');
                        else s.classList.remove('active');
                    });
                    // Atualizar o rating value
                    const ratingValue = container.querySelector('.rating-value');
                    if (ratingValue) ratingValue.textContent = `${value}/5`;
                };

                // Hover visual (apenas para não-readonly)
                star.onmouseover = function() {
                    stars.forEach((s, i) => {
                        if (i <= index) s.classList.add('active');
                        else s.classList.remove('active');
                    });
                };
                star.onmouseout = function() {
                    stars.forEach((s, i) => {
                        if (i < currentValue) s.classList.add('active');
                        else s.classList.remove('active');
                    });
                };
            });
        }

        // Função específica para inicializar estrelas readonly
        function initReadonlyStars() {
            document.querySelectorAll('.star-rating.readonly').forEach(container => {
                const stars = container.querySelectorAll('.star');
                const rating = parseInt(container.getAttribute('data-rating')) || 0;

                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            });
        }

        // Inicializar rating em todos os cartões ao carregar (exceto readonly)
        document.querySelectorAll('.star-rating:not(.readonly)').forEach(initStarRating);

        // Inicializar estrelas readonly
        initReadonlyStars();

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
                        this.insertAdjacentHTML('beforebegin', e.dataTransfer.getData(
                            'text/html'));
                        updateAvaliacaoNumbers();
                        document.querySelectorAll('.star-rating:not(.readonly)').forEach(
                            initStarRating);
                        initDragAndDrop();
                    }
                    this.classList.remove('over');
                    return false;
                });
                item.addEventListener('dragend', function(e) {
                    this.classList.remove('dragElem');
                    document.querySelectorAll('.avaliacao-item').forEach(i => i.classList
                        .remove('over'));
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
            newCard.style =
                'background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px 24px 20px 24px; margin: 0; min-width: 270px; max-width: 340px; min-height: 370px; flex: 1 1 270px; position: relative; display: flex; flex-direction: column; transition: box-shadow 0.2s;';
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
                    <input type="text" class="form-control mb-2" id="faq_${index}_imagemfaq" name="faqs[${index}][imagemfaq]" required>
                    <div class="image-preview mb-2">
                        <img src="" alt="Preview" style="display: none; max-width: 100px; max-height: 100px;">
                    </div>
                    <div class="drop-zone" data-target="faq_${index}_imagemfaq">
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

        // Expõe as funções das FAQs globalmente
        window.addNewFaq = addNewFaq;
        window.removeFaq = removeFaq;

        async function removeFaq(button) {
            const confirmed = await showCustomConfirm(
                'Tem certeza que deseja remover esta FAQ? Esta ação não pode ser desfeita.');
            if (confirmed) {
                button.closest('.faq-item').remove();
                updateFaqNumbers();
                showAlert('FAQ removida com sucesso!', 'success');
            }
        }

        async function clearAllFaqs() {
            const confirmed = await showCustomConfirm(
                'Tem certeza que deseja apagar todas as FAQs? Esta ação não pode ser desfeita.');
            if (confirmed) {
                document.getElementById('faqsContainer').innerHTML = '';
                showAlert('Todas as FAQs foram removidas!', 'success');
            }
        }

        function updateFaqNumbers() {
            const items = document.querySelectorAll('.faq-item');
            items.forEach((item, index) => {
                item.querySelector('h4').textContent = `FAQ ${index + 1}`;
                const inputs = item.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    const id = input.getAttribute('id');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                    if (id && id.includes('imagemfaq')) {
                        input.setAttribute('id', `faq_${index}_imagemfaq`);
                    }
                });
                const dropZone = item.querySelector('.drop-zone');
                if (dropZone) {
                    dropZone.setAttribute('data-target', `faq_${index}_imagemfaq`);
                }
            });
        }



        // ---------------------------- Funções para Footer ----------------------------

        // Função para atualizar preview da imagem do footer
        function updateFooterImagePreview(inputId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(inputId + 'Preview');

            if (input && preview) {
                if (input.value) {
                    preview.src = input.value;
                    preview.style.display = 'block';
                    preview.onerror = function() {
                        this.style.display = 'none';
                    };
                } else {
                    preview.style.display = 'none';
                }
            }
        }



        // Funções para gerenciar links das colunas do footer
        function addLink(containerId) {
            const container = document.getElementById(containerId);

            const newLinkHtml = `
            <div class="input-group mb-2" style="animation: slideInDown 0.3s;">
                <input type="text" class="form-control" name="${containerId.replace('Links', '_links[]')}" 
                       value="" placeholder="${getPlaceholderText(containerId)}">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeLink(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

            // Inserir antes do botão "Adicionar"
            const addButton = container.nextElementSibling;
            addButton.insertAdjacentHTML('beforebegin', newLinkHtml);

            // Atualizar preview
            updateFooterPreview();
        }

        function removeLink(button) {
            if (confirm('Tem certeza que deseja remover este item?')) {
                const linkItem = button.closest('.input-group');
                linkItem.style.animation = 'slideOutUp 0.3s';

                setTimeout(() => {
                    linkItem.remove();
                    updateFooterPreview();
                }, 300);
            }
        }

        function getPlaceholderText(containerId) {
            switch (containerId) {
                case 'coluna1Links':
                case 'coluna3Links':
                case 'coluna4Links':
                    return 'Nome do link';
                case 'coluna2Links':
                    return 'Informação de contacto';
                default:
                    return 'Digite o texto';
            }
        }

        // Função para atualizar o preview do footer em tempo real
        function updateFooterPreview() {
            // Coletar dados das colunas
            const coluna1Titulo = document.querySelector('input[name="coluna1_titulo"]')?.value ||
                'Ligações Úteis';
            const coluna2Titulo = document.querySelector('input[name="coluna2_titulo"]')?.value || 'Contactos';
            const coluna3Titulo = document.querySelector('input[name="coluna3_titulo"]')?.value || 'FAQ\'s';
            const coluna4Titulo = document.querySelector('input[name="coluna4_titulo"]')?.value || 'Tickets';

            const coluna1Links = Array.from(document.querySelectorAll('input[name="coluna1_links[]"]')).map(
                input => input.value).filter(val => val);
            const coluna2Links = Array.from(document.querySelectorAll('input[name="coluna2_links[]"]')).map(
                input => input.value).filter(val => val);
            const coluna3Links = Array.from(document.querySelectorAll('input[name="coluna3_links[]"]')).map(
                input => input.value).filter(val => val);
            const coluna4Links = Array.from(document.querySelectorAll('input[name="coluna4_links[]"]')).map(
                input => input.value).filter(val => val);

            const copyright = document.getElementById('Copyright')?.value ||
                'Copyright © 2025 Todos os direitos reservados | Criado com ♥ por Tomás Nápoles & Salvador Coimbras';

            // Atualizar preview
            const preview = document.getElementById('footerPreview');
            if (preview) {

                preview.innerHTML = `
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <h6 style="color: white; margin-bottom: 20px; font-weight: bold;">${coluna1Titulo}</h6>
                        <div style="line-height: 2;">
                            ${coluna1Links.map(link => `<div>${link}</div>`).join('')}
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h6 style="color: white; margin-bottom: 20px; font-weight: bold;">${coluna2Titulo}</h6>
                        <div style="line-height: 1.8;">
                            ${coluna2Links.map(link => `<div>${link}</div>`).join('')}
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h6 style="color: white; margin-bottom: 20px; font-weight: bold;">${coluna3Titulo}</h6>
                        <div style="line-height: 2;">
                            ${coluna3Links.map(link => `<div>${link}</div>`).join('')}
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h6 style="color: white; margin-bottom: 20px; font-weight: bold;">${coluna4Titulo}</h6>
                        <div style="line-height: 2;">
                            ${coluna4Links.map(link => `<div>${link}</div>`).join('')}
                        </div>
                    </div>
                </div>
                <hr style="border-color: #333; margin: 30px 0;">
                <div style="text-align: center; font-size: 14px;">
                    ${copyright.replace(/♥/g, '<span style="color: #ff6b6b;">♥</span>')}
                </div>
            `;
            }
        }

        // Event listeners para preview em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const copyrightInput = document.getElementById('Copyright');

            // Copyright
            if (copyrightInput) {
                copyrightInput.addEventListener('input', updateFooterPreview);
            }

            // Títulos das colunas
            document.querySelectorAll('input[name*="_titulo"]').forEach(input => {
                input.addEventListener('input', updateFooterPreview);
            });

            // Links das colunas
            document.querySelectorAll('input[name*="_links[]"]').forEach(input => {
                input.addEventListener('input', updateFooterPreview);
            });

            // Chamada inicial para configurar preview
            updateFooterPreview();
        });

        // Adicionar estilos CSS para animações do footer
        const footerStyle = document.createElement('style');
        footerStyle.textContent = `
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
        
        .input-group {
            transition: all 0.3s ease;
        }
        
        /* Hover removido conforme solicitado */
        
        #footerPreview {
            transition: all 0.3s ease;
        }
        
        /* Card hover removido conforme solicitado */
    `;
        document.head.appendChild(footerStyle);

        // Função para atualizar o preview da imagem (mantém compatibilidade)
        function updateImagePreview(inputId) {
            const input = document.getElementById(inputId);
            const preview = input.closest('.image-upload-container').querySelector('.image-preview');
            if (input.value) {
                preview.src = input.value;
                preview.style.display = 'block';
            }
        }

        // ========================= Funções para Footer Links =========================

        let footerLinkCounter = 10000; // Contador global para índices únicos

        // Função para adicionar link ao footer
        function addFooterLink(containerId, secao) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const linkRow = document.createElement('div');
            linkRow.className = 'row mb-2';

            linkRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="footer_links[${footerLinkCounter}][nome]" 
                       placeholder="${getPlaceholderForSection(secao, 'nome')}">
                <input type="hidden" name="footer_links[${footerLinkCounter}][secao]" value="${secao}">
            </div>
            <div class="col-md-5">
                <input type="${secao === 'Contactos' ? 'text' : 'url'}" class="form-control" 
                       name="footer_links[${footerLinkCounter}][link]" 
                       placeholder="${getPlaceholderForSection(secao, 'link')}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeFooterLink(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

            container.appendChild(linkRow);
            footerLinkCounter++;
        }

        // Função para remover link do footer
        function removeFooterLink(button) {
            const row = button.closest('.row');
            if (row) {
                row.style.animation = 'slideOutUp 0.3s ease-out';
                setTimeout(() => {
                    row.remove();
                }, 300);
            }
        }

        // Função para obter placeholders adequados por seção
        function getPlaceholderForSection(secao, tipo) {
            const placeholders = {
                'LigacoesUteis': {
                    'nome': 'Nome do link',
                    'link': 'https://'
                },
                'Contactos': {
                    'nome': 'Tipo de contacto',
                    'link': 'Informação de contacto'
                },
                'Faqs': {
                    'nome': 'Pergunta',
                    'link': 'https://'
                },
                'Tickets': {
                    'nome': 'Nome do link',
                    'link': 'https://'
                }
            };

            return placeholders[secao] ? placeholders[secao][tipo] : 'Digite aqui';
        }

        // ========================= Funções para Gestão de Links por Seção =========================

        // Contador global para índices de links
        let footerLinkIndex = <?php echo count($footerLinksData); ?>;

        // Função para adicionar link a uma seção específica
        function addSectionLink(section) {
            const containers = {
                'ligacoes-uteis': 'ligacoes-uteis-links',
                'faqs': 'faqs-links',
                'tickets': 'tickets-links',
                'contactos': 'contactos-links'
            };

            const sectionNames = {
                'ligacoes-uteis': 'LigacoesUteis',
                'faqs': 'FAQs',
                'tickets': 'Tickets',
                'contactos': 'Contactos'
            };

            const colors = {
                'ligacoes-uteis': 'primary',
                'faqs': 'info',
                'tickets': 'warning',
                'contactos': 'success'
            };

            const containerId = containers[section];
            const container = document.getElementById(containerId);

            if (!container) return;

            // Remove mensagem "nenhum link" se existir
            const noLinksMsg = container.parentElement.querySelector('.text-center.py-3');
            if (noLinksMsg) {
                noLinksMsg.remove();
            }

            const linkItem = document.createElement('div');
            linkItem.className = 'link-item mb-3 p-3 border rounded bg-light';
            linkItem.style.animation = 'slideInDown 0.3s ease-out';

            linkItem.innerHTML = `
            <div class="mb-2">
                <label class="form-label fw-bold text-${colors[section]} small">Nome do Link</label>
                <input type="text" class="form-control form-control-sm" name="footer_links[${footerLinkIndex}][nome]" 
                       placeholder="Ex: ${section === 'ligacoes-uteis' ? 'GIAE' : section === 'faqs' ? 'Como usar o GIAE?' : section === 'tickets' ? 'Submeter Ticket' : 'Email da Escola'}">
                <input type="hidden" name="footer_links[${footerLinkIndex}][secao]" value="${sectionNames[section]}">
            </div>
            <div class="mb-2">
                <label class="form-label fw-bold text-${colors[section]} small">URL</label>
                <input type="url" class="form-control form-control-sm" name="footer_links[${footerLinkIndex}][link]" 
                       placeholder="${section === 'contactos' ? 'mailto:... ou https://...' : 'https://...'}">
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSectionLink(this)">
                <i class="fas fa-trash me-1"></i>Remover
            </button>
        `;

            container.appendChild(linkItem);
            footerLinkIndex++;

            // Foca no primeiro input do novo link
            const firstInput = linkItem.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }

            // Mostra notificação de sucesso
            showFooterNotification(`Link adicionado à seção ${sectionNames[section]}!`, 'success');
        }

        // Função para remover link de seção
        function removeSectionLink(button) {
            const linkItem = button.closest('.link-item');
            if (!linkItem) return;

            // Animação de saída
            linkItem.style.animation = 'slideOutUp 0.3s ease-in';

            setTimeout(() => {
                const container = linkItem.parentElement;
                linkItem.remove();

                // Se não há mais links, mostra mensagem
                if (container.children.length === 0) {
                    const section = container.id.replace('-links', '');
                    const icons = {
                        'ligacoes-uteis': 'link',
                        'faqs': 'question-circle',
                        'tickets': 'ticket-alt',
                        'contactos': 'address-book'
                    };

                    const messages = {
                        'ligacoes-uteis': 'Nenhum link adicionado',
                        'faqs': 'Nenhum FAQ adicionado',
                        'tickets': 'Nenhum link adicionado',
                        'contactos': 'Nenhum contacto adicionado'
                    };

                    container.innerHTML = `
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-${icons[section]} fa-2x mb-2"></i>
                        <p class="small mb-0">${messages[section]}</p>
                    </div>
                `;
                }
            }, 300);

            showFooterNotification('Link removido com sucesso!', 'info');
        }

        // Função para mostrar notificações do footer
        function showFooterNotification(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

            alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            document.body.appendChild(alertDiv);

            // Remove automaticamente após 3 segundos
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        // Adicionar estilos para animações dos links
        const linkAnimationStyles = document.createElement('style');
        linkAnimationStyles.textContent = `
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
        
        .link-item {
            transition: all 0.3s ease;
        }
        
        /* Link-item hover removido conforme solicitado */
        
        /* Card border hovers removidos conforme solicitado */
    `;
        document.head.appendChild(linkAnimationStyles);

        // Estilos para os cards das colunas melhorados
        const columnCardsStyles = document.createElement('style');
        columnCardsStyles.textContent = `
        .hover-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        /* Hover-card efeito removido conforme solicitado */
        
        .hover-card .card-header {
            position: relative;
            overflow: hidden;
        }
        
        .hover-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        /* Card header hover effect removido conforme solicitado */
        
        .input-group .btn {
            transition: all 0.2s ease;
        }
        
        /* Input-group btn hover removido conforme solicitado */
    `;
        document.head.appendChild(columnCardsStyles);



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
            fetch(
                    `get_suggestions.php?page=${page}&search=${encodeURIComponent(search)}&status=${status}&priority=${priority}`)
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
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center text-muted">Nenhuma sugestão encontrada.</td></tr>';
                return;
            }

            tbody.innerHTML = suggestions.map(suggestion => {
                const statusBadgeClass = {
                    'pendente': 'bg-warning text-dark',
                    'em_analise': 'bg-info text-white',
                    'resolvido': 'bg-success text-white',
                    'rejeitado': 'bg-danger text-white'
                } [suggestion.status] || 'bg-secondary text-white';

                const priorityBadgeClass = {
                    'baixa': 'bg-success text-white',
                    'media': 'bg-warning text-dark',
                    'alta': 'bg-danger text-white'
                } [suggestion.prioridade] || 'bg-secondary text-white';

                const statusText = {
                    'pendente': 'Pendente',
                    'em_analise': 'Em Análise',
                    'resolvido': 'Resolvido',
                    'rejeitado': 'Rejeitado'
                } [suggestion.status] || suggestion.status;

                const priorityText = {
                    'baixa': 'Baixa',
                    'media': 'Média',
                    'alta': 'Alta'
                } [suggestion.prioridade] || suggestion.prioridade;

                const typeText = {
                    'melhoria_conteudo': 'Melhoria de Conteúdo',
                    'nova_funcionalidade': 'Nova Funcionalidade',
                    'problema_tecnico': 'Problema Técnico',
                    'feedback_geral': 'Feedback Geral',
                    'outro': 'Outro'
                } [suggestion.tipo_sugestao] || suggestion.tipo_sugestao;

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

            let paginationHtml =
                '<nav aria-label="Navegação de sugestões"><ul class="pagination justify-content-center">';

            if (pagination.current_page > 1) {
                paginationHtml +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="loadSuggestions(${pagination.current_page - 1}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">Anterior</a></li>`;
            }

            for (let i = 1; i <= pagination.total_pages; i++) {
                const activeClass = i === pagination.current_page ? 'active' : '';
                paginationHtml +=
                    `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadSuggestions(${i}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">${i}</a></li>`;
            }

            if (pagination.current_page < pagination.total_pages) {
                paginationHtml +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="loadSuggestions(${pagination.current_page + 1}, document.getElementById('suggestionSearchInput').value, document.getElementById('statusFilter').value, document.getElementById('priorityFilter').value)">Próximo</a></li>`;
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
                        } [suggestion.tipo_sugestao] || suggestion.tipo_sugestao;

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
                        } [suggestion.prioridade] || suggestion.prioridade;

                        const priorityBadgeClass = {
                            'baixa': 'custom-badge badge-success',
                            'media': 'custom-badge badge-warning',
                            'alta': 'custom-badge badge-danger'
                        } [suggestion.prioridade] || 'custom-badge badge-secondary';

                        document.getElementById('modal-suggestion-priority').innerHTML =
                            `<span class="${priorityBadgeClass}">${priorityText}</span>`;

                        // Formatar status com badge colorido
                        const statusText = {
                            'pendente': 'Pendente',
                            'em_analise': 'Em Análise',
                            'resolvido': 'Resolvido',
                            'rejeitado': 'Rejeitado'
                        } [suggestion.status] || suggestion.status;

                        const statusBadgeClass = {
                            'pendente': 'custom-badge badge-warning',
                            'em_analise': 'custom-badge badge-info',
                            'resolvido': 'custom-badge badge-success',
                            'rejeitado': 'custom-badge badge-danger'
                        } [suggestion.status] || 'custom-badge badge-secondary';

                        document.getElementById('modal-suggestion-status').innerHTML =
                            `<span class="${statusBadgeClass}">${statusText}</span>`;

                        document.getElementById('modal-suggestion-message').textContent = suggestion
                            .mensagem;

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
            loadSuggestions(1, this.value, document.getElementById('statusFilter').value, document
                .getElementById('priorityFilter').value);
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            loadSuggestions(1, document.getElementById('suggestionSearchInput').value, this.value,
                document.getElementById('priorityFilter').value);
        });

        document.getElementById('priorityFilter').addEventListener('change', function() {
            loadSuggestions(1, document.getElementById('suggestionSearchInput').value, document
                .getElementById('statusFilter').value, this.value);
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
                deleteBtn.innerHTML =
                    '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
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
                deleteBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin" style="margin-right: 6px; font-size: 12px;"></i>Apagando...';

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
                            deleteBtn.innerHTML =
                                '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showAlert('Erro ao apagar sugestão: ' + error.message, 'danger');
                        // Reabilitar botão em caso de erro
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML =
                            '<i class="fas fa-trash" style="margin-right: 6px; font-size: 12px;"></i>Apagar';
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
                            showAlert('Erro ao abrir formulário de post: ' + error.message, 'danger');
        }
    };

    window.editPost = function(postId) {
        fetch(`get_posts.php?id=${postId}&for_edit=true`)
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
                    document.getElementById('postDataCriacao').value = new Date(post.data_criacao)
                        .toLocaleString('pt-PT');

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
                                                showAlert('Erro ao carregar dados do post', 'danger');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao carregar post: ' + error.message, 'danger');
            });
    };

    // FUNÇÃO PARA APAGAR POST - SÓ COM MODAL CUSTOMIZADO!
    window.deletePost = function(postId) {
        if (!postId) {
            console.error('❌ ID do post inválido para exclusão');
            showAlert('Erro: ID do post inválido', 'danger');
            return false;
        }
        
        console.log('🗑️ INICIANDO EXCLUSÃO DE POST - ID:', postId);
        console.log('🎯 FORÇANDO USO DO MODAL CUSTOMIZADO APENAS!');
        
        // Verificar se showCustomConfirm existe
        if (typeof window.showCustomConfirm !== 'function') {
            console.error('❌ CRÍTICO: showCustomConfirm não encontrada para posts!');
            showAlert('ERRO: Modal de confirmação não está disponível', 'danger');
            return;
        }
        
        // Mensagem personalizada para posts
        const confirmMessage = 'Tem certeza que deseja apagar este post? Esta ação não pode ser desfeita.';
        
        console.log('✅ Chamando showCustomConfirm para post...');
        
        // USAR APENAS O MODAL
        window.showCustomConfirm(confirmMessage)
            .then(confirmed => {
                console.log('📝 Resposta do modal para post:', confirmed);
                
                if (!confirmed) {
                    console.log('❌ Usuário cancelou a exclusão do post');
                    return;
                }
                
                console.log('✅ Usuário confirmou - prosseguindo com exclusão do post');
                proceedWithPostDeletion(postId);
            })
            .catch(error => {
                console.error('❌ Erro no modal para post:', error);
                showAlert('Erro no modal: ' + error.message, 'danger');
            });
    };
    
    // Função auxiliar para processar a exclusão de post após confirmação
    function proceedWithPostDeletion(postId) {
        console.log('🗑️ PROCESSANDO EXCLUSÃO DE POST - ID:', postId);
        
        // Encontra o card do post para mostrar loading
        const postCard = document.querySelector(`[data-post-id="${postId}"]`);
        if (postCard) {
            postCard.style.opacity = '0.5';
            postCard.style.pointerEvents = 'none';
            postCard.style.backgroundColor = '#ffebee';
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
                    console.log('🎉 Post excluído com sucesso!');
                    showAlert(data.message, 'success');

                    // Recarregar a página atual de posts após apagar
                    setTimeout(() => {
                        if (typeof window.loadPosts === 'function') {
                            window.loadPosts(currentPostsPage);
                        }
                    }, 500);

                } else {
                    console.error('❌ Erro ao excluir post:', data);
                    showAlert(data.message, 'danger');
                    // Restaura o estado do card em caso de erro
                    if (postCard) {
                        postCard.style.opacity = '1';
                        postCard.style.pointerEvents = 'auto';
                        postCard.style.backgroundColor = '';
                    }
                }
            })
            .catch(error => {
                console.error('❌ Erro na requisição para apagar post:', error);
                showAlert('Erro ao apagar post: ' + error.message, 'danger');
                // Restaura o estado do card em caso de erro
                if (postCard) {
                    postCard.style.opacity = '1';
                    postCard.style.pointerEvents = 'auto';
                    postCard.style.backgroundColor = '';
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔵 Posts JavaScript carregado');

        // Função para carregar posts com paginação
        window.loadPosts = function(page = 1) {
            currentPostsPage = page;

            // Mostrar loading
            const postsContainer = document.getElementById('postsContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            if (postsContainer) {
                postsContainer.innerHTML =
                    '<div class="col-12 text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Carregando posts...</p></div>';
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
                        postsContainer.innerHTML =
                            '<div class="col-12 text-center py-4"><p class="text-danger">Erro ao carregar posts. Tente novamente.</p></div>';
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

        // Event listeners para Footer
        const corFundoInput = document.getElementById('CorFundo');
        const corTextoInput = document.getElementById('CorTexto');

        if (corFundoInput) {
            corFundoInput.addEventListener('input', function() {
                updateColorPreview('CorFundo', 'corFundoPreview');
            });
        }

        if (corTextoInput) {
            corTextoInput.addEventListener('input', function() {
                updateColorPreview('CorTexto', 'corTextoPreview');
            });
        }

        // Teste se as funções estão disponíveis globalmente
        console.log('✅ Teste de funções:');
        console.log('- showNewPostForm:', typeof window.showNewPostForm);
        console.log('- editPost:', typeof window.editPost);
        console.log('- deletePost:', typeof window.deletePost);
        console.log('- showCustomConfirm para posts:', typeof window.showCustomConfirm);
        console.log('- addFooterLink:', typeof window.addFooterLink);
        console.log('- removeFooterLink:', typeof window.removeFooterLink);
    });

    // Modal de confirmação customizado
    let currentTicketId = null;
    let currentTicketAction = null;

    function showCustomConfirmModal(ticketId, novoEstado) {
        currentTicketId = ticketId;
        currentTicketAction = novoEstado;
        
        const modal = document.getElementById('customConfirmModal');
        const modalMessage = document.getElementById('customConfirmMessage');
        
        // Personalizar mensagem baseada na ação
        let message = '';
        switch(novoEstado.toLowerCase()) {
            case 'aceite':
                message = 'Tem certeza que deseja aceitar este pedido de reparação? Esta ação irá confirmar que o pedido foi aprovado.';
                break;
            case 'rejeitado':
                message = 'Tem certeza que deseja rejeitar este pedido de reparação? Esta ação não pode ser desfeita.';
                break;
            case 'concluído':
                message = 'Tem certeza que deseja marcar este pedido como concluído? Esta ação finalizará o processo de reparação.';
                break;
            default:
                message = `Tem certeza que deseja alterar o estado para "${novoEstado}"? Esta ação pode não ser desfeita.`;
        }
        
        modalMessage.textContent = message;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideCustomConfirmModal() {
        const modal = document.getElementById('customConfirmModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        currentTicketId = null;
        currentTicketAction = null;
    }

    function confirmTicketAction() {
        if (currentTicketId && currentTicketAction) {
            hideCustomConfirmModal();
            
            fetch('update_ticket_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ticket_id=${currentTicketId}&novo_estado=${encodeURIComponent(currentTicketAction)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(`Ticket ${currentTicketAction.toLowerCase()} com sucesso!`, 'success');
                        // Recarregar a página após 1 segundo para mostrar as mudanças
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('Erro ao atualizar ticket: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('Erro ao atualizar ticket', 'danger');
                });
        }
    }

    // Função para atualizar status dos tickets
    function updateTicketStatus(ticketId, novoEstado) {
        showCustomConfirmModal(ticketId, novoEstado);
    }
    </script>

    <!-- Modal de Confirmação Customizado -->
    <div id="customConfirmModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-container">
            <div class="custom-modal-content">
                <div class="custom-modal-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h3 class="custom-modal-title">Confirmar</h3>
                <p id="customConfirmMessage" class="custom-modal-message">
                    Tem certeza que deseja realizar esta ação?
                </p>
                <div class="custom-modal-buttons">
                    <button type="button" class="custom-btn custom-btn-cancel" onclick="hideCustomConfirmModal()">
                        Cancelar
                    </button>
                    <button type="button" class="custom-btn custom-btn-confirm" onclick="confirmTicketAction()">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease-out;
    }

    .custom-modal-container {
        max-width: 480px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .custom-modal-content {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px 32px 32px;
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalSlideIn 0.3s ease-out;
        position: relative;
    }

    .custom-modal-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 24px;
        background: #fef3c7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 28px;
    }

    .custom-modal-title {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin: 0 0 16px;
        line-height: 1.2;
    }

    .custom-modal-message {
        font-size: 16px;
        color: #6b7280;
        line-height: 1.5;
        margin: 0 0 32px;
    }

    .custom-modal-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .custom-btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 120px;
        outline: none;
    }

    .custom-btn-cancel {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .custom-btn-cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .custom-btn-confirm {
        background: #dc2626;
        color: white;
    }

    .custom-btn-confirm:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Responsivo */
    @media (max-width: 640px) {
        .custom-modal-content {
            padding: 32px 24px 24px;
        }
        
        .custom-modal-buttons {
            flex-direction: column;
        }
        
        .custom-btn {
            width: 100%;
        }
    }
    </style>

</body>

</html>

</html>