<?php
// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Buscar informações do utilizador
require_once 'connection.php';
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sem permissões de administrador']);
    exit;
}

// Configurações de resposta JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Inicializar resposta
$response = ['success' => false, 'message' => ''];

try {
    // Verificar se é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }
    
    // Verificar se os dados foram enviados
    if (!isset($_POST['update_sobre'])) {
        throw new Exception('Dados não encontrados');
    }
    
    // Validar campos obrigatórios
    $texto1 = isset($_POST['Texto1']) ? trim($_POST['Texto1']) : '';
    $texto2 = isset($_POST['Texto2']) ? trim($_POST['Texto2']) : '';
    $imagem = isset($_POST['Imagem']) ? trim($_POST['Imagem']) : '';
    
    // Atualizar na base de dados
    $sql = "UPDATE SobreNosInicio SET 
            Texto1 = ?,
            Texto2 = ?,
            Imagem = ?
            WHERE id = 1";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Erro na preparação da query: ' . $conn->error);
    }
    
    $stmt->bind_param("sss", $texto1, $texto2, $imagem);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Conteúdo "Sobre Nós" atualizado com sucesso!';
    } else {
        throw new Exception('Erro ao atualizar: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Erro: ' . $e->getMessage();
}

// Retornar resposta JSON
echo json_encode($response);
exit;
?> 