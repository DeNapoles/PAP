<?php
require_once 'connection.php';
require_once 'functions_posts.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = (int)$_POST['post_id'];
    
    if (deletePost($post_id)) {
        echo json_encode(['success' => true, 'message' => 'Post apagado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao apagar o post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
} 