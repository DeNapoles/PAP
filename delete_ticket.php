<?php
require_once 'connection.php';
session_start();
// Apenas permitir admins autenticados
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}
// Verificar se é admin
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Sem permissões']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_Ticket'])) {
    $id = intval($_POST['ID_Ticket']);
    $stmt = $conn->prepare("DELETE FROM Tickets WHERE ID_Ticket = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Pedido inválido']); 