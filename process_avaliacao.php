<?php
// Ativar debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Log para debug
error_log("process_avaliacao.php iniciado");

$response = ['success' => false, 'message' => ''];

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método não permitido';
    echo json_encode($response);
    exit;
}

// Log dos dados recebidos
error_log("POST data: " . print_r($_POST, true));

// Coletar e validar dados
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$estrelas = isset($_POST['estrelas']) ? (int)$_POST['estrelas'] : 0;
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';

error_log("Dados processados - Nome: $nome, Email: $email, Estrelas: $estrelas, Texto: $texto");

// Validações (email não é obrigatório pois não é armazenado na BD)
if (empty($nome)) {
    $response['message'] = 'O nome é obrigatório';
} elseif ($estrelas < 1 || $estrelas > 5) {
    $response['message'] = 'Selecione uma classificação de 1 a 5 estrelas';
} elseif (empty($texto)) {
    $response['message'] = 'O comentário é obrigatório';
} else {
    try {
        error_log("Tentando inserir na tabela TabelaAvaliacoesInicio");
        
        // Inserir na tabela TabelaAvaliacoesInicio (apenas colunas que existem)
        $sql = "INSERT INTO TabelaAvaliacoesInicio (Nome, Estrelas, Texto) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }
        
        $stmt->bind_param("sis", $nome, $estrelas, $texto);
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Avaliação enviada com sucesso! Obrigado pelo seu feedback.',
                'avaliacao' => [
                    'Nome' => $nome,
                    'Estrelas' => $estrelas,
                    'Texto' => $texto
                ]
            ];
            error_log("Avaliação inserida com sucesso. ID: " . $conn->insert_id);
        } else {
            throw new Exception("Erro ao executar query: " . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Erro no process_avaliacao.php: " . $e->getMessage());
        $response['message'] = 'Erro interno: ' . $e->getMessage();
    }
}

echo json_encode($response);
?> 