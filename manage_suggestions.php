<?php
require_once 'connection.php';
header('Content-Type: application/json');

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Nome, Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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

try {
    $action = $_POST['action'] ?? '';
    $suggestion_id = (int)($_POST['id'] ?? 0);

    if (empty($action) || $suggestion_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        exit;
    }

    // Verificar se a sugestão existe
    $stmt = $conn->prepare("SELECT * FROM sugestoes WHERE id = ?");
    $stmt->bind_param("i", $suggestion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggestion = $result->fetch_assoc();

    if (!$suggestion) {
        echo json_encode(['success' => false, 'message' => 'Sugestão não encontrada']);
        exit;
    }

    switch ($action) {
        case 'update':
            $new_status = $_POST['status'] ?? '';
            $new_priority = $_POST['prioridade'] ?? '';

            // Validar status
            $valid_statuses = ['pendente', 'em_analise', 'resolvido', 'rejeitado'];
            if (!in_array($new_status, $valid_statuses)) {
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                exit;
            }

            // Validar prioridade
            $valid_priorities = ['baixa', 'media', 'alta'];
            if (!in_array($new_priority, $valid_priorities)) {
                echo json_encode(['success' => false, 'message' => 'Prioridade inválida']);
                exit;
            }

            // Atualizar a sugestão
            $update_stmt = $conn->prepare("UPDATE sugestoes SET status = ?, prioridade = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $new_status, $new_priority, $suggestion_id);
            
            if ($update_stmt->execute()) {
                // Registrar no log de alterações se existir a tabela
                try {
                    $log_stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_user_id) VALUES (?, 'sugestao_atualizada', ?, ?)");
                    $details = "Sugestão ID {$suggestion_id} - Status: {$new_status}, Prioridade: {$new_priority}";
                    $dummy_user_id = 0; // Para sugestões, pode usar 0 ou criar uma lógica específica
                    $log_stmt->bind_param("isi", $dummy_user_id, $details, $_SESSION['user_id']);
                    $log_stmt->execute();
                } catch (Exception $e) {
                    // Se a tabela de logs não existir, ignorar o erro
                }

                echo json_encode(['success' => true, 'message' => 'Sugestão atualizada com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar sugestão']);
            }
            break;

        case 'delete':
            // Apagar a sugestão
            $delete_stmt = $conn->prepare("DELETE FROM sugestoes WHERE id = ?");
            $delete_stmt->bind_param("i", $suggestion_id);
            
            if ($delete_stmt->execute()) {
                // Registrar no log de alterações se existir a tabela
                try {
                    $log_stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_user_id) VALUES (?, 'sugestao_apagada', ?, ?)");
                    $details = "Sugestão ID {$suggestion_id} de {$suggestion['nome']} ({$suggestion['email']}) apagada";
                    $dummy_user_id = 0;
                    $log_stmt->bind_param("isi", $dummy_user_id, $details, $_SESSION['user_id']);
                    $log_stmt->execute();
                } catch (Exception $e) {
                    // Se a tabela de logs não existir, ignorar o erro
                }

                echo json_encode(['success' => true, 'message' => 'Sugestão apagada com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao apagar sugestão']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?> 