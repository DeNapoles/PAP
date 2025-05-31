<?php
require_once 'connection.php';

// Verificar se é uma requisição POST
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
        echo json_encode([
            'success' => true,
            'message' => 'Dados atualizados com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar dados: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida'
    ]);
}
?> 