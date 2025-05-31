<?php
require_once 'connection.php';
require_once 'functions_posts.php';

header('Content-Type: application/json');

// Se for uma requisição para buscar um post específico
if (isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $post = getPostById($post_id);
    
    if ($post) {
        // Formatar a data para exibição
        $post['data_criacao'] = date('Y-m-d H:i:s', strtotime($post['data_criacao']));
        
        echo json_encode([
            'success' => true,
            'post' => $post
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Post não encontrado'
        ]);
    }
    exit;
}

// Se for uma requisição para listar posts com paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$posts_per_page = 8;
$offset = ($page - 1) * $posts_per_page;

$sql = "SELECT p.*, u.Nome as autor_nome, 
       (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as num_comentarios 
       FROM posts p 
       LEFT JOIN Utilizadores u ON p.autor_id = u.ID_Utilizador 
       ORDER BY p.data_criacao DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $posts_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$total_sql = "SELECT COUNT(*) as total FROM posts";
$total_result = $conn->query($total_sql);
$total_posts = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

?>
<div id="postsContainer" class="row">
<?php
if ($result->num_rows > 0) {
    while($post = $result->fetch_assoc()) {
        ?>
        <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
            <div class="post-image">
                <img src="<?php echo htmlspecialchars($post['img_principal']); ?>" 
                     alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                     loading="lazy">
            </div>
            <div class="post-content">
                <h3 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h3>
                <p class="post-excerpt"><?php echo htmlspecialchars(substr($post['texto'], 0, 150)) . '...'; ?></p>
                <?php if (!empty($post['tags'])): ?>
                <div class="post-tags">
                    <?php 
                    $tags = explode(',', $post['tags']);
                    foreach ($tags as $tag): 
                    ?>
                        <span class="tag-badge"><?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="post-meta">
                    <span class="post-date">
                        Data de criação: <?php echo date('d/m/Y', strtotime($post['data_criacao'])); ?>
                    </span>
                    <span class="post-comments">
                        Comentários: <?php echo $post['num_comentarios']; ?>
                    </span>
                </div>
                <div class="post-actions">
                    <button class="btn btn-primary" onclick="editPost(<?php echo $post['id']; ?>)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn btn-danger" onclick="deletePost(<?php echo $post['id']; ?>)">
                        <i class="fas fa-trash"></i> Apagar
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="col-12"><p class="text-center">Nenhum post encontrado.</p></div>';
}
?>
</div>
<div id="paginationContainer" class="pagination-container" style="display: flex; justify-content: center; margin-top: 32px;">
<?php if ($total_pages > 1): ?>
    <nav aria-label="Navegação de posts">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="#" data-page="<?php echo $page - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="#" data-page="<?php echo $page + 1; ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?> 