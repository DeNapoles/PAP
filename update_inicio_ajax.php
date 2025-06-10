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
    echo json_encode(['success' => false, 'message' => 'Sem permissões de administrador']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_capa'])) {
    try {
        // Verificar se há dados para processar
        $requiredFields = ['LogoSeparador', 'LogoPrincipal', 'TextoBemvindo', 'Fundo'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                throw new Exception("Campo obrigatório '{$field}' não foi recebido");
            }
        }
        
        $logoseparador = $_POST['LogoSeparador'];
        $logoprincipal = $_POST['LogoPrincipal'];
        $textobemvindo = $_POST['TextoBemvindo'];
        $textoinicial = $_POST['TextoInicial'] ?? '';
        $textoinicial2 = $_POST['TextoInicial2'] ?? '';
        $botaoinicial = $_POST['BotaoInicial'] ?? '';
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
        if (!$stmt) {
            throw new Exception("Erro ao preparar SQL: " . $conn->error);
        }
        
        $stmt->bind_param("sssssss", 
            $logoseparador,
            $logoprincipal,
            $textobemvindo,
            $textoinicial,
            $textoinicial2,
            $botaoinicial,
            $fundo
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar update: " . $stmt->error);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Dados da Capa atualizados com sucesso!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar dados da Capa: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida'
    ]);
}
exit;
?>