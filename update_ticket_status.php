<?php
session_start();
require_once 'connection.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

// Verificar se o usuário é admin
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Validar parâmetros
if (!isset($_POST['ticket_id']) || !isset($_POST['novo_estado'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros obrigatórios não fornecidos']);
    exit;
}

$ticketId = intval($_POST['ticket_id']);
$novoEstado = trim($_POST['novo_estado']);

// Validar estados permitidos
$estadosPermitidos = ['Pendente', 'Aceite', 'Rejeitado', 'Concluído'];
if (!in_array($novoEstado, $estadosPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

try {
    // Verificar se o ticket existe
    $stmt = $conn->prepare("SELECT ID_Ticket FROM Tickets WHERE ID_Ticket = ?");
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Ticket não encontrado']);
        exit;
    }
    
    // Atualizar o estado do ticket
    $stmt = $conn->prepare("UPDATE Tickets SET Estado = ? WHERE ID_Ticket = ?");
    $stmt->bind_param("si", $novoEstado, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => "Ticket atualizado para '$novoEstado' com sucesso!"
        ]);
    } else {
        throw new Exception("Erro ao atualizar ticket: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Erro ao atualizar ticket: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

$conn->close();
?> 