<?php
header('Content-Type: text/html; charset=utf-8');
include 'connection.php';

function getInicioInicio() {
    global $conn;
    $sql = "SELECT * FROM InicioInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getSobreNos() {
    global $conn;
    $sql = "SELECT * FROM SobreNosInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getSeparadores() {
    global $conn;
    $sql = "SELECT * FROM SeparadoresNavBar ORDER BY id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getLigacoesUteis() {
    global $conn;
    $sql = "SELECT * FROM LigacoesUteis ORDER BY id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAvisolaranjaInicio() {
    global $conn;
    $sql = "SELECT * FROM AvisolaranjaInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getLigacoesRapidas() {
    global $conn;
    $sql = "SELECT * FROM LigacoesRapidasInicio ORDER BY id";
    $result = $conn->query($sql);
    $ligacoesRapidas = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ligacoesRapidas[] = $row;
        }
    }
    return $ligacoesRapidas;
}

function getAvaliacoes() {
    global $conn;
    $sql = "SELECT * FROM TabelaAvaliacoesInicio ORDER BY id";
    $result = $conn->query($sql);
    $avaliacoes = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $avaliacoes[] = $row;
        }
    }
    return $avaliacoes;
}

function getFaqPrevisualizacao() {
    global $conn;
    $sql = "SELECT * FROM FaqPrevisualizacaoInicio ORDER BY id";
    $result = $conn->query($sql);
    $faqs = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
    }
    return $faqs;
}

function getFooterLinks($secao) {
    global $conn;
    $sql = "SELECT nome, link FROM FooterLinks WHERE secao = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $secao);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getContactos() {
    global $conn;
    $sql = "SELECT nome FROM FooterLinks WHERE secao = 'Contactos'";
    $result = $conn->query($sql);
    $contactos = array();
    while ($row = $result->fetch_assoc()) {
        $contactos[] = $row['nome'];
    }
    return $contactos;
}

function getCTAInicio() {
    global $conn;
    $sql = "SELECT Titulo, Texto, BtnText, Fundo FROM CTAInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getFooterData() {
    global $conn;
    $sql = "SELECT * FROM Footer LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Carregar todos os dados necessários
$inicioData = getInicioInicio();
$sobreNos = getSobreNos();
$separadores = getSeparadores();
$ligacoesUteis = getLigacoesUteis();
$avisolaranjaInicio = getAvisolaranjaInicio();
$ligacoesRapidas = getLigacoesRapidas();
$avaliacoes = getAvaliacoes();
$faqs = getFaqPrevisualizacao();
$ctaInicio = getCTAInicio();
$footerData = getFooterData();
?> 