<?php
require_once 'functions.php';
require_once 'functions_posts.php';

header('Content-Type: application/json');

// Por enquanto, permitir comentários sem login
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $comentario_pai_id = !empty($_POST['comentario_pai_id']) ? (int)$_POST['comentario_pai_id'] : null;
    $assunto = isset($_POST['assunto']) ? trim($_POST['assunto']) : '';
    $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
    
    // Por enquanto, usar um utilizador_id fixo (1 = Admin)
    $utilizador_id = 1;
    
    if (empty($texto)) {
        $response['message'] = 'O texto do comentário é obrigatório';
    } else {
        $sql = "INSERT INTO comentarios (post_id, utilizador_id, assunto, texto, comentario_pai_id, data_criacao) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $post_id, $utilizador_id, $assunto, $texto, $comentario_pai_id);
        
        if ($stmt->execute()) {
            $comment_id = $conn->insert_id;
            
            // Buscar o comentário recém-criado com informações do autor
            $sql = "SELECT c.*, u.Nome as autor_nome 
                    FROM comentarios c 
                    JOIN Utilizadores u ON c.utilizador_id = u.ID_Utilizador 
                    WHERE c.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $comment = $result->fetch_assoc();
            
            // Formatar a data
            $comment['data_formatada'] = formatDate($comment['data_criacao']);
            
            $response = [
                'success' => true,
                'comment' => $comment
            ];
        } else {
            $response['message'] = 'Erro ao salvar o comentário';
        }
    }
}

echo json_encode($response); 