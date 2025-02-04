<?php
$host = "aebconecta.pt"; // Geralmente algo como 'meusite.com' ou 'nome_do_host.webhs.org'
$user = "aebconec_SA";
$password = "@Aeb123conecta.";
$database = "aebconec_BD";

$conn = new mysqli($host, $user, $password, $database);

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


// Definir charset para UTF-8
$conn->set_charset("utf8mb4");



// Função para buscar dados do Sobre Nós
function getSobreNos() {
    global $conn;
    $sql = "SELECT * FROM SobreNosInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar dados do InicioInicio
function getInicioInicio() {
    global $conn;
    $sql = "SELECT * FROM InicioInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar separadores da navbar
function getSeparadores() {
    global $conn;
    $sql = "SELECT * FROM SeparadoresNavBar ORDER BY id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Função para buscar ligações úteis
function getLigacoesUteis() {
    global $conn;
    $sql = "SELECT * FROM LigacoesUteis ORDER BY id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Função para buscar dados do Aviso Laranja
function getAvisolaranjaInicio() {
    global $conn;
    $sql = "SELECT * FROM AvisolaranjaInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para buscar ligações rápidas
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

// Função para buscar avaliações
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

// Função para buscar dados da tabela FaqPrevisualizacaoInicio
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

// echo "Conectado com sucesso!";
?>