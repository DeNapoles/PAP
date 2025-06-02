<?php
require_once 'connection.php';

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Função para registrar alterações no log
function logUserChange($userId, $action, $details) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userId, $action, $details, $_SESSION['user_id']);
    return $stmt->execute();
}

// Processar a requisição
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Ação inválida'];

switch ($action) {
    case 'create':
        if (isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['tipo'])) {
            // Verificar se o email já existe
            $stmt = $conn->prepare("SELECT ID_Utilizador FROM Utilizadores WHERE Email = ?");
            $stmt->bind_param("s", $_POST['email']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response = ['success' => false, 'message' => 'Este email já está em uso'];
                break;
            }

            // Criar novo usuário
            $stmt = $conn->prepare("INSERT INTO Utilizadores (Nome, Email, Senha, Tipo_Utilizador, Estado, Data_Registo) VALUES (?, ?, ?, ?, 'Ativo', NOW())");
            $hashed_password = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            $stmt->bind_param("ssss", $_POST['nome'], $_POST['email'], $hashed_password, $_POST['tipo']);
            
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;
                logUserChange($user_id, 'create', 'Novo usuário criado');
                $response = ['success' => true, 'message' => 'Usuário criado com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao criar usuário'];
            }
        }
        break;

    case 'update':
        if (isset($_POST['id'], $_POST['nome'], $_POST['email'], $_POST['tipo'])) {
            $user_id = (int)$_POST['id'];
            
            // Verificar se o email já existe para outro usuário
            $stmt = $conn->prepare("SELECT ID_Utilizador FROM Utilizadores WHERE Email = ? AND ID_Utilizador != ?");
            $stmt->bind_param("si", $_POST['email'], $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response = ['success' => false, 'message' => 'Este email já está em uso'];
                break;
            }

            // Atualizar usuário
            $sql = "UPDATE Utilizadores SET Nome = ?, Email = ?, Tipo_Utilizador = ?";
            $params = [$_POST['nome'], $_POST['email'], $_POST['tipo']];
            $types = "sss";

            // Se uma nova senha foi fornecida
            if (!empty($_POST['senha'])) {
                $sql .= ", Senha = ?";
                $hashed_password = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                $params[] = $hashed_password;
                $types .= "s";
            }

            $sql .= " WHERE ID_Utilizador = ?";
            $params[] = $user_id;
            $types .= "i";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                logUserChange($user_id, 'update', 'Usuário atualizado');
                $response = ['success' => true, 'message' => 'Usuário atualizado com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar usuário'];
            }
        }
        break;

    case 'delete':
        if (isset($_POST['id'])) {
            $user_id = (int)$_POST['id'];
            
            // Não permitir excluir o próprio usuário
            if ($user_id === $_SESSION['user_id']) {
                $response = ['success' => false, 'message' => 'Não é possível excluir seu próprio usuário'];
                break;
            }

            // Excluir usuário
            $stmt = $conn->prepare("DELETE FROM Utilizadores WHERE ID_Utilizador = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                logUserChange($user_id, 'delete', 'Usuário excluído');
                $response = ['success' => true, 'message' => 'Usuário excluído com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao excluir usuário'];
            }
        }
        break;

    case 'update_status':
        if (isset($_POST['id'], $_POST['status'])) {
            $user_id = (int)$_POST['id'];
            $status = $_POST['status'] ? 'Ativo' : 'Inativo';
            
            // Não permitir desativar o próprio usuário
            if ($user_id === $_SESSION['user_id']) {
                $response = ['success' => false, 'message' => 'Não é possível desativar seu próprio usuário'];
                break;
            }

            $stmt = $conn->prepare("UPDATE Utilizadores SET Estado = ? WHERE ID_Utilizador = ?");
            $stmt->bind_param("si", $status, $user_id);
            
            if ($stmt->execute()) {
                logUserChange($user_id, 'status_update', 'Status alterado para ' . $status);
                $response = ['success' => true, 'message' => 'Status atualizado com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar status'];
            }
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response); 