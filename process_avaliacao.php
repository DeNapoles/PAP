<?php
session_start();
require_once 'functions.php';

header('Content-Type: application/json');

// Verificar se o utilizador está autenticado
$response = ['success' => false, 'message' => ''];

// Verificar autenticação
$user = null;
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
} else if (isset($_COOKIE['user'])) {
    $userData = json_decode($_COOKIE['user'], true);
    if ($userData && isset($userData['id'])) {
        $user = $userData['id'];
    }
}

if (!$user) {
    $response['message'] = 'Por favor, inicie sessão para comentar.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $estrelas = isset($_POST['estrelas']) ? (int)$_POST['estrelas'] : 0;
    $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
    
    // Validações
    if (empty($nome)) {
        $response['message'] = 'O nome é obrigatório';
    } elseif (empty($email)) {
        $response['message'] = 'O email é obrigatório';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email inválido';
    } elseif ($estrelas < 1 || $estrelas > 5) {
        $response['message'] = 'Selecione uma classificação de 1 a 5 estrelas';
    } elseif (empty($texto)) {
        $response['message'] = 'O comentário é obrigatório';
    } else {
        // Inserir na tabela TabelaAvaliacoesInicio
        $sql = "INSERT INTO TabelaAvaliacoesInicio (Nome, Email, Estrelas, Texto, data_criacao) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $nome, $email, $estrelas, $texto);
        
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
        } else {
            $response['message'] = 'Erro ao salvar a avaliação. Tente novamente.';
        }
    }
}

echo json_encode($response);
?> 