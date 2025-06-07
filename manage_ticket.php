<?php
require_once 'connection.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success'=>false, 'message'=>'Não autenticado']);
  exit;
}
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
  echo json_encode(['success'=>false, 'message'=>'Acesso negado']);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
  $id = intval($_POST['id']);
  $status = $_POST['status'];
  $allowed = ['Pendente','Aceite','Rejeitado','Concluído'];
  if (!in_array($status, $allowed)) {
    echo json_encode(['success'=>false, 'message'=>'Estado inválido']);
    exit;
  }
  $stmt = $conn->prepare("UPDATE Tickets SET Estado=? WHERE ID_Ticket=?");
  $stmt->bind_param("si", $status, $id);
  if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'message'=>'Erro ao atualizar']);
  }
  exit;
}
echo json_encode(['success'=>false, 'message'=>'Requisição inválida']); 