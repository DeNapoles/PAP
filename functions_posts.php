<?php
require_once 'functions.php';

/**
 * Obtém todos os posts com informações do autor
 */
function getPosts() {
    global $conn;
    $sql = "SELECT p.*, u.nome as autor_nome 
            FROM posts p 
            LEFT JOIN Utilizadores u ON p.autor_id = u.id 
            ORDER BY p.data_criacao DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Obtém um post específico pelo ID
 */
function getPostById($id) {
    global $conn;
    $id = (int)$id;
    $sql = "SELECT p.*, u.Nome as autor_nome, 
            (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as num_comentarios 
            FROM posts p 
            LEFT JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador 
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    if ($post) {
        // Processar as tags
        $post['tags'] = array_map('trim', explode(',', $post['tags']));
    }
    
    return $post;
}

/**
 * Salva um novo post
 */
function savePost($titulo, $texto, $tags, $img_principal, $autor_id) {
    global $conn;
    
    $sql = "INSERT INTO posts (titulo, texto, tags, img_principal, autor_id, data_criacao) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $titulo, $texto, $tags, $img_principal, $autor_id);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

/**
 * Atualiza um post existente
 */
function updatePost($id, $titulo, $texto, $tags, $img_principal) {
    global $conn;
    
    $sql = "UPDATE posts 
            SET titulo = ?, texto = ?, tags = ?, img_principal = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $titulo, $texto, $tags, $img_principal, $id);
    
    return $stmt->execute();
}

/**
 * Exclui um post
 */
function deletePost($id) {
    global $conn;
    
    // Primeiro, excluir os comentários relacionados
    $sql = "DELETE FROM comentarios WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Depois, excluir o post
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

/**
 * Formata a data para exibição
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Obtém posts paginados
 */
function getPaginatedPosts($page = 1, $per_page = 4) {
    global $conn;
    
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT p.*, u.Nome as autor_nome, 
            (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as num_comentarios 
            FROM posts p 
            LEFT JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador 
            ORDER BY p.data_criacao DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while($row = $result->fetch_assoc()) {
        // Processar as tags
        $row['tags'] = array_map('trim', explode(',', $row['tags']));
        $posts[] = $row;
    }
    
    return $posts;
}

/**
 * Obtém o total de posts para paginação
 */
function getTotalPosts() {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM posts";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

/**
 * Busca posts por título
 */
function searchPosts($query) {
    global $conn;
    
    $search = "%$query%";
    $sql = "SELECT p.*, u.Nome as autor_nome 
            FROM posts p 
            LEFT JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador 
            WHERE p.titulo LIKE ? 
            ORDER BY p.data_criacao DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while($row = $result->fetch_assoc()) {
        // Processar as tags
        $row['tags'] = array_map('trim', explode(',', $row['tags']));
        $posts[] = $row;
    }
    
    return $posts;
}

// Função para buscar posts por tag
function getPostsByTag($tag_id) {
    global $conn;
    // Agora $tag_id é o nome da tag
    $sql = "SELECT p.*, u.Nome as autor_nome, (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as num_comentarios FROM posts p JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador WHERE FIND_IN_SET(?, p.tags) ORDER BY p.data_criacao DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tag_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while($row = $result->fetch_assoc()) {
        $row['tags'] = array_map('trim', explode(',', $row['tags']));
        $posts[] = $row;
    }
    return $posts;
}

// Função para buscar todas as tags
function getAllTags() {
    global $conn;
    $sql = "SELECT tags FROM posts";
    $result = $conn->query($sql);
    $tags = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tags = array_merge($tags, array_map('trim', explode(',', $row['tags'])));
        }
    }
    $tags = array_filter(array_unique($tags));
    $tagObjs = [];
    foreach ($tags as $tag) {
        $tagObjs[] = ['id' => $tag, 'nome' => $tag];
    }
    return $tagObjs;
}
?> 