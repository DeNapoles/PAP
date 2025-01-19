<?php
include('connection.php'); // Inclui o arquivo connection.php

// Teste a conexão
if ($conn->ping()) { // Verifica se a conexão está ativa
    echo "Conexão bem-sucedida com a base de dados!";
} else {
    echo "Erro ao conectar com a base de dados: " . $conn->error;   

}       
?>

