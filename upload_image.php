<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Verifica se um arquivo foi enviado
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma imagem enviada']);
    exit;
}

$file = $_FILES['image'];
$uploadDir = 'uploads/';

// Cria o diretório de uploads se não existir
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Verifica o tipo do arquivo
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
    exit;
}

// Gera um nome único para o arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$targetPath = $uploadDir . $filename;

// Move o arquivo para o diretório de uploads
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode([
        'success' => true,
        'url' => $targetPath,
        'message' => 'Imagem enviada com sucesso'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar a imagem'
    ]);
} 