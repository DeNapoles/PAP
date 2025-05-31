<?php
require_once 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['links'])) {
    try {
        $links = $_POST['links'];
        
        // Prepara a query de UPDATE
        $stmt = $conn->prepare("UPDATE LigacoesRapidasInicio SET Nome = ?, Link = ?, Imagem = ?, Largura = ?, Altura = ? WHERE id = ?");
        
        foreach ($links as $index => $link) {
            $id = $index + 1; // Assumindo que os IDs são sequenciais começando em 1
            $largura = !empty($link['Largura']) ? $link['Largura'] : null;
            $altura = !empty($link['Altura']) ? $link['Altura'] : null;
            
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