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
echo "Conectado com sucesso!";
?>