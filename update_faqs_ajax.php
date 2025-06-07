<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Sem permissões de administrador']);
    exit;
}

// Processamento do formulário de FAQs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_faqs'])) {
    try {
        $faqs = $_POST['faqs'] ?? [];
        
        if (empty($faqs)) {
            echo json_encode(['success' => false, 'message' => 'Nenhuma FAQ recebida']);
            exit;
        }
        
        // Limpa a tabela
        $conn->query("TRUNCATE TABLE FaqPrevisualizacaoInicio");
        
        // Insere as FAQs na nova ordem
        $stmt = $conn->prepare("INSERT INTO FaqPrevisualizacaoInicio (titulofaq, textofaq, Link, imagemfaq) VALUES (?, ?, ?, ?)");
        
        $faqsInseridas = 0;
        foreach ($faqs as $faq) {
            if (!empty($faq['titulofaq']) && !empty($faq['textofaq'])) {
                $titulo = $faq['titulofaq'] ?? '';
                $texto = $faq['textofaq'] ?? '';
                $link = $faq['Link'] ?? '';
                $imagem = $faq['imagemfaq'] ?? '';
                
                $stmt->bind_param("ssss", $titulo, $texto, $link, $imagem);
                $stmt->execute();
                $faqsInseridas++;
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => "FAQs atualizadas com sucesso! ($faqsInseridas FAQs salvas)",
            'faqs_count' => $faqsInseridas
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao atualizar FAQs: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}
?> 