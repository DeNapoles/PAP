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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['links'])) {
    try {
        $links = $_POST['links'];
        
        // Verificar se há dados para processar
        if (empty($links) || !is_array($links)) {
            throw new Exception("Nenhum dado de ligação foi recebido para atualizar");
        }
        
        // Prepara a query de UPDATE
        $stmt = $conn->prepare("UPDATE LigacoesRapidasInicio SET Nome = ?, Link = ?, Imagem = ?, Largura = ?, Altura = ? WHERE id = ?");
        
        foreach ($links as $index => $link) {
            $id = $index + 1; // Assumindo que os IDs são sequenciais começando em 1
            
            // Validar dados essenciais
            if (empty($link['Nome']) || empty($link['Link']) || empty($link['Imagem'])) {
                throw new Exception("Dados incompletos para a ligação {$id}. Nome, Link e Imagem são obrigatórios.");
            }
            
            $largura = !empty($link['Largura']) ? intval($link['Largura']) : null;
            $altura = !empty($link['Altura']) ? intval($link['Altura']) : null;
            
            $stmt->bind_param("ssssii", 
                $link['Nome'],
                $link['Link'],
                $link['Imagem'],
                $largura,
                $altura,
                $id
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao atualizar ligação ID {$id}: " . $stmt->error);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Ligações rápidas atualizadas com sucesso!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar ligações rápidas: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida'
    ]);
}
exit; 