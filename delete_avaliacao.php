<?php
// delete_avaliacao.php  
require_once 'connection.php';

header('Content-Type: application/json');

// Debug - salvar pedido em arquivo
$debug_info = date('Y-m-d H:i:s') . " - PEDIDO RECEBIDO\n";
$debug_info .= "POST: " . print_r($_POST, true) . "\n";
$debug_info .= "METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n\n";
file_put_contents('debug_delete.txt', $debug_info, FILE_APPEND);

// Verificar POST e ID
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$id = intval($_POST['id']);

// Debug - ID processado
file_put_contents('debug_delete.txt', "ID processado: $id\n", FILE_APPEND);

// Verificar conexão
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit;
}

// Tentar eliminar diretamente (método mais simples)
$sql = "DELETE FROM TabelaAvaliacoesInicio WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    file_put_contents('debug_delete.txt', "ERRO prepare: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Erro na preparação: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $affected = $stmt->affected_rows;
    file_put_contents('debug_delete.txt', "Linhas afetadas: $affected\n", FILE_APPEND);
    
    if ($affected > 0) {
        echo json_encode(['success' => true, 'message' => "Avaliação $id eliminada com sucesso!"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Nenhuma linha eliminada (ID $id não existe)"]);
    }
} else {
    file_put_contents('debug_delete.txt', "ERRO execute: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Erro na execução: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

file_put_contents('debug_delete.txt', "Script finalizado\n\n", FILE_APPEND);
?> 