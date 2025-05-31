<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Habilita o log de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log dos dados recebidos
error_log("Dados recebidos: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['faqs'])) {
    try {
        $conn->begin_transaction();

        // Limpa a tabela atual
        $conn->query("TRUNCATE TABLE FaqPrevisualizacaoInicio");

        // Prepara a query de inserção
        $stmt = $conn->prepare("INSERT INTO FaqPrevisualizacaoInicio (titulofaq, textofaq, Link, imagemfaq) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Erro ao preparar a query: " . $conn->error);
        }

        // Processa cada FAQ
        foreach ($_POST['faqs'] as $index => $faq) {
            error_log("Processando FAQ $index: " . print_r($faq, true));
            
            if (!empty($faq['titulofaq']) && !empty($faq['textofaq'])) {
                $titulo = $faq['titulofaq'];
                $texto = $faq['textofaq'];
                $link = $faq['Link'] ?? '';
                $imagem = $faq['imagemfaq'] ?? '';
                
                if (!$stmt->bind_param("ssss", $titulo, $texto, $link, $imagem)) {
                    throw new Exception("Erro ao vincular parâmetros: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Erro ao executar a query: " . $stmt->error);
                }
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'FAQs atualizadas com sucesso!']);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Erro ao atualizar FAQs: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar FAQs: ' . $e->getMessage()]);
    }
} else {
    error_log("Dados inválidos recebidos: " . print_r($_POST, true));
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
} 