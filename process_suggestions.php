<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Validar e sanitizar dados
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$tipo_sugestao = trim($_POST['tipo_sugestao'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');
$prioridade = trim($_POST['prioridade'] ?? 'baixa');

// Validações básicas
if (empty($nome) || empty($email) || empty($tipo_sugestao) || empty($mensagem)) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos obrigatórios devem ser preenchidos']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// Criar tabela se não existir
$createTableSQL = "CREATE TABLE IF NOT EXISTS sugestoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tipo_sugestao VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'baixa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'em_analise', 'resolvido', 'rejeitado') DEFAULT 'pendente'
)";

if (!$conn->query($createTableSQL)) {
    echo json_encode(['success' => false, 'message' => 'Erro ao criar tabela: ' . $conn->error]);
    exit;
}

// Preparar e executar a inserção
$stmt = $conn->prepare("INSERT INTO sugestoes (nome, email, tipo_sugestao, mensagem, prioridade) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssss", $nome, $email, $tipo_sugestao, $mensagem, $prioridade);

if ($stmt->execute()) {
    // Opcional: Enviar email de notificação para administradores
    $sugestao_id = $conn->insert_id;
    
    // Log da sugestão (opcional)
    error_log("Nova sugestão recebida - ID: $sugestao_id, De: $email, Tipo: $tipo_sugestao");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Sugestão enviada com sucesso!',
        'id' => $sugestao_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar sugestão: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 