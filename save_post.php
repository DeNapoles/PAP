<?php
require_once 'connection.php';
require_once 'functions_posts.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber e validar os dados
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $titulo = $_POST['titulo'] ?? '';
    $texto = $_POST['texto'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $img_principal = $_POST['img_principal'] ?? '';
    $autor_id = isset($_POST['autor_id']) ? (int)$_POST['autor_id'] : null;

    // Validar campos obrigatórios
    if (empty($titulo) || empty($texto) || empty($img_principal) || !$autor_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Os campos Título, Texto, Imagem Principal e Autor são obrigatórios'
        ]);
        exit;
    }

    // Processar imagens adicionais (opcionais)
    $imagens_adicionais = [];
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($_POST["img_$i"])) {
            $imagens_adicionais["img_$i"] = $_POST["img_$i"];
        }
    }

    // Processar o salvamento
    if ($id) {
        // Atualizar post existente
        $success = updatePost($id, $titulo, $texto, $tags, $img_principal, $imagens_adicionais);
        $message = $success ? 'Post atualizado com sucesso' : 'Erro ao atualizar o post';
    } else {
        // Criar novo post
        $new_id = savePost($titulo, $texto, $tags, $img_principal, $autor_id, $imagens_adicionais);
        $success = $new_id !== false;
        $message = $success ? 'Post criado com sucesso' : 'Erro ao criar o post';
    }

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'post_id' => $id ?? $new_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requisição inválida'
    ]);
} 