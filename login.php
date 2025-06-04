<?php
require_once 'connection.php';

// Ativar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT ID_Utilizador, Nome, Email, Senha, Tipo_Utilizador FROM Utilizadores WHERE Email = ?");
    if (!$stmt) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Erro na execução da query: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verifica a senha usando password_verify
        if (password_verify($senha, $user['Senha'])) {
            session_start();
            $_SESSION['user_id'] = $user['ID_Utilizador'];
            echo json_encode([
                'success' => true,
                'message' => 'Login efetuado com sucesso!',
                'user' => [
                    'id' => $user['ID_Utilizador'],
                    'nome' => $user['Nome'],
                    'email' => $user['Email'],
                    'tipo' => $user['Tipo_Utilizador']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilizador não encontrado.']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar o login. Por favor, tente novamente.',
        'debug' => $e->getMessage() // Remover em produção
    ]);
}
?>
