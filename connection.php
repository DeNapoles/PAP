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

// echo "Conectado com sucesso!";
?>