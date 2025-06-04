<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "aebconecta.pt"; // Geralmente algo como 'meusite.com' ou 'nome_do_host.webhs.org'
$user = "aebconec_SA";
$password = "@Aeb123conecta.";
$database = "aebconec_BD";

try {
    $conn = new mysqli($host, $user, $password, $database);

    // Verifica conexão
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Definir charset para UTF-8
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Erro ao definir charset: " . $conn->error);
    }

} catch (Exception $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
}

// echo "Conectado com sucesso!";
?>