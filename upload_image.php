<?php
header('Content-Type: application/json');
require_once 'connection.php';

// Verifica se um arquivo foi enviado
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma imagem enviada']);
    exit;
}

$file = $_FILES['image'];
$uploadDir = 'img/uploads/';

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

// Se houver uma imagem antiga para deletar
if (isset($_POST['old_image']) && !empty($_POST['old_image'])) {
    $oldImage = $_POST['old_image'];
    // Verifica se a imagem antiga existe e está na pasta img/uploads
    if (file_exists($oldImage) && strpos($oldImage, 'img/uploads/') === 0) {
        unlink($oldImage); // Deleta a imagem antiga
    }
}

// Gera o nome do arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
if (isset($_POST['custom_name']) && !empty($_POST['custom_name'])) {
    // Usa o nome personalizado, mas adiciona um timestamp para evitar conflitos
    $filename = $_POST['custom_name'] . '_' . time() . '.' . $extension;
} else {
    // Usa o nome original do arquivo com timestamp
    $filename = pathinfo($file['name'], PATHINFO_FILENAME) . '_' . time() . '.' . $extension;
}

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