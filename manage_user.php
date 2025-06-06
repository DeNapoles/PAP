<?php
// Enable error reporting for debugging (should be disabled in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Não mostrar erros na tela para não quebrar JSON
ini_set('display_startup_errors', 0);

// Limpar qualquer output anterior que possa interferir com o JSON
ob_clean();

require_once 'connection.php';

// Ensure session is started to get admin ID for logging
session_start();

// Set header to indicate JSON content
header('Content-Type: application/json');

// Garantir que autocommit está ativado para mudanças imediatas
$conn->autocommit(true);

// Function to send JSON response
function sendJsonResponse($success, $message, $data = []) {
    echo json_encode(['success' => $success, 'message' => $message] + $data);
    exit;
}

// Function to log user changes (optional, based on your log system)
// This function needs to interact with your 'user_logs' table structure
function logUserChange($userId, $action, $details) {
    global $conn;
    // Assuming 'user_logs' table has columns: user_id, action, details, admin_id, created_at
    // You might need to get the current admin user ID from the session
    // Replace with actual column names and table name if different
    $admin_id = $_SESSION['user_id'] ?? null; // Get admin ID from session
     if ($admin_id === null) { // Handle case where admin ID is not in session
        error_log("Warning: Admin user ID not found in session for logging user changes.");
        // Optionally, you can skip logging or use a default/anonymous admin ID
     }

    // Check if user_logs table exists (optional, avoids errors if logging is not set up)
    $tableExists = $conn->query("SHOW TABLES LIKE 'user_logs'")->num_rows > 0;
    if ($tableExists) {
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_id) VALUES (?, ?, ?, ?)");
        // Use 'i' for integers, 's' for strings
        // Ensure admin_id is an integer or null
        $admin_id_param = $admin_id !== null ? (int)$admin_id : null;
        $stmt->bind_param("issi", $userId, $action, $details, $admin_id_param);
        if (!$stmt->execute()) {
             error_log("Error logging user change: " . $stmt->error);
        }
    }
}

// Check if request method is POST and action is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            // Validate required fields
            if (!isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['tipo'])) {
                sendJsonResponse(false, 'Campos obrigatórios faltando.');
            }
            
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Hash the password
            $tipo = $_POST['tipo'];
            $estado = 'Ativo'; // Default status for new user

            // Check if email already exists
            $stmt = $conn->prepare("SELECT ID_Utilizador FROM Utilizadores WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                sendJsonResponse(false, 'Email já cadastrado.');
            }
            $stmt->close();

            // Insert new user
            // Use column names: Nome, Email, Senha, Tipo_Utilizador, Estado
            $stmt = $conn->prepare("INSERT INTO Utilizadores (Nome, Email, Senha, Tipo_Utilizador, Estado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $email, $senha, $tipo, $estado);

            if ($stmt->execute()) {
                $newUserId = $conn->insert_id;
                logUserChange($newUserId, 'create', 'Criou o utilizador ' . $nome . ' (' . $email . '). Tipo: ' . $tipo . ', Estado: ' . $estado);
                sendJsonResponse(true, 'Utilizador criado com sucesso!');
            } else {
                error_log("Error creating user: " . $stmt->error);
                sendJsonResponse(false, 'Erro ao criar utilizador. Por favor, tente novamente.');
            }
            $stmt->close();
            break;

        case 'update':
            // Validate required fields
            if (!isset($_POST['id'], $_POST['nome'], $_POST['email'], $_POST['tipo'])) {
                 sendJsonResponse(false, 'Campos obrigatórios faltando para atualização.');
            }

            $id = (int)$_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $tipo = $_POST['tipo'];
            $senha = $_POST['senha'] ?? ''; // Senha é opcional na edição

            // Check if email already exists for another user
            $stmt = $conn->prepare("SELECT ID_Utilizador FROM Utilizadores WHERE Email = ? AND ID_Utilizador != ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                sendJsonResponse(false, 'Email já cadastrado por outro utilizador.');
            }
            $stmt->close();

            // Build update query dynamically based on whether password is provided
            $sql = "UPDATE Utilizadores SET Nome = ?, Email = ?, Tipo_Utilizador = ?";
            $types = "sss";
            $params = [$nome, $email, $tipo];
            $log_details = 'Atualizou o utilizador ID ' . $id . '. Campos: Nome=' . $nome . ', Email=' . $email . ', Tipo=' . $tipo;

            if (!empty($senha)) {
                $sql .= ", Senha = ?";
                $types .= "s";
                $params[] = password_hash($senha, PASSWORD_DEFAULT); // Hash the new password
                $log_details .= ', Senha=***'; // Avoid logging actual password
            }
            
            $sql .= " WHERE ID_Utilizador = ?";
            $types .= "i";
            $params[] = $id;

            $stmt = $conn->prepare($sql);
            // Using call_user_func_array because bind_param does not accept array directly
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
            
            if ($stmt->execute()) {
                logUserChange($id, 'update', $log_details);
                sendJsonResponse(true, 'Utilizador atualizado com sucesso!');
            } else {
                error_log("Error updating user: " . $stmt->error);
                sendJsonResponse(false, 'Erro ao atualizar utilizador. Por favor, tente novamente.');
            }
            $stmt->close();
            break;

        case 'delete':
            // Validate required fields
            if (!isset($_POST['id'])) {
                 sendJsonResponse(false, 'ID do utilizador faltando para exclusão.');
            }
            
            $id = (int)$_POST['id'];

            // Prevent deleting the currently logged-in user
            if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id) {
                 sendJsonResponse(false, 'Não pode excluir o seu próprio utilizador.');
            }

            // Get user details before deleting for logging
            $stmt = $conn->prepare("SELECT Nome, Email FROM Utilizadores WHERE ID_Utilizador = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $userToDelete = $result->fetch_assoc();
            $stmt->close();

            if (!$userToDelete) {
                 sendJsonResponse(false, 'Utilizador não encontrado.');
            }
            
            // Delete user
            $stmt = $conn->prepare("DELETE FROM Utilizadores WHERE ID_Utilizador = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                logUserChange($id, 'delete', 'Excluiu o utilizador ' . $userToDelete['Nome'] . ' (' . $userToDelete['Email'] . ').');
                sendJsonResponse(true, 'Utilizador excluído com sucesso!');
            } else {
                 error_log("Error deleting user: " . $stmt->error);
                sendJsonResponse(false, 'Erro ao excluir utilizador. Por favor, tente novamente.');
            }
            $stmt->close();
            break;

        case 'update_status':
            // Validate required fields
            if (!isset($_POST['id'], $_POST['status'])) {
                sendJsonResponse(false, 'ID ou status faltando para atualização de status.');
            }

            $id = (int)$_POST['id'];
            $status = $_POST['status']; // Expected: 'Ativo' or 'Inativo'

            // Validate status value
            if ($status !== 'Ativo' && $status !== 'Inativo') {
                sendJsonResponse(false, 'Status inválido fornecido.');
            }

            // Verificar se o utilizador existe
            $checkStmt = $conn->prepare("SELECT ID_Utilizador, Estado FROM Utilizadores WHERE ID_Utilizador = ?");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $existingUser = $checkResult->fetch_assoc();
            $checkStmt->close();

            if (!$existingUser) {
                sendJsonResponse(false, 'Utilizador não encontrado.');
            }

            // Verificar se realmente precisa de atualização
            if ($existingUser['Estado'] === $status) {
                sendJsonResponse(true, 'Estado já está definido como ' . $status . '.');
            }

            // Update user status
            $stmt = $conn->prepare("UPDATE Utilizadores SET Estado = ? WHERE ID_Utilizador = ?");
            $stmt->bind_param("si", $status, $id);

            if ($stmt->execute()) {
                $affectedRows = $stmt->affected_rows;
                
                if ($affectedRows > 0) {
                    logUserChange($id, 'update_status', 'Alterou o status do utilizador ID ' . $id . ' para ' . $status . '.');
                    sendJsonResponse(true, 'Status do utilizador atualizado com sucesso!');
                } else {
                    sendJsonResponse(false, 'Nenhuma alteração foi feita.');
                }
            } else {
                error_log("Error updating user status: " . $stmt->error);
                sendJsonResponse(false, 'Erro ao atualizar status do utilizador.');
            }
            $stmt->close();
            break;

        case 'update_type':
            // Validate required fields
            if (!isset($_POST['id'], $_POST['tipo'])) {
                 sendJsonResponse(false, 'ID ou tipo faltando para atualização de tipo.');
            }

            $id = (int)$_POST['id'];
            $tipo = $_POST['tipo']; // Expected: 'Aluno', 'Professor', or 'Admin'

            // Prevent changing type of the currently logged-in user (optional, but good practice)
            // You might want to allow changing your *own* type, but be careful with Admin permissions.
            // For simplicity here, let's disallow changing your own type.
            if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id) {
                 sendJsonResponse(false, 'Não pode alterar o seu próprio tipo de utilizador.');
            }

             // Validate type value
            if (!in_array($tipo, ['Aluno', 'Professor', 'Admin'])) {
                 sendJsonResponse(false, 'Tipo de utilizador inválido fornecido.');
            }

            // Update user type
            // Use column name: Tipo_Utilizador
            $stmt = $conn->prepare("UPDATE Utilizadores SET Tipo_Utilizador = ? WHERE ID_Utilizador = ?");
            $stmt->bind_param("si", $tipo, $id);

            if ($stmt->execute()) {
                logUserChange($id, 'update_type', 'Alterou o tipo do utilizador ID ' . $id . ' para ' . $tipo . '.');
                sendJsonResponse(true, 'Tipo de utilizador atualizado com sucesso!');
            } else {
                error_log("Error updating user type: " . $stmt->error);
                sendJsonResponse(false, 'Erro ao atualizar tipo de utilizador. Por favor, tente novamente.');
            }
            $stmt->close();
            break;

        default:
            sendJsonResponse(false, 'Ação inválida.');
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Handle fetching a single user for editing
    $id = (int)$_GET['id'];
    
    // Use column names: ID_Utilizador, Nome, Email, Tipo_Utilizador, Estado
    $stmt = $conn->prepare("SELECT ID_Utilizador, Nome, Email, Tipo_Utilizador, Estado FROM Utilizadores WHERE ID_Utilizador = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        sendJsonResponse(true, '', ['user' => $user]);
    } else {
        sendJsonResponse(false, 'Utilizador não encontrado.');
    }

} else {
    sendJsonResponse(false, 'Método de requisição inválido ou ação não especificada.');
}

$conn->close();
?> 