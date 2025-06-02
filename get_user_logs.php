<?php
require_once 'connection.php';

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Configuração da paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$logs_per_page = 10;
$offset = ($page - 1) * $logs_per_page;

// Construir a query base
$sql = "SELECT l.*, u.Nome as user_name, a.Nome as admin_name 
        FROM user_logs l 
        LEFT JOIN Utilizadores u ON l.user_id = u.ID_Utilizador 
        LEFT JOIN Utilizadores a ON l.admin_id = a.ID_Utilizador";

$count_sql = "SELECT COUNT(*) as total FROM user_logs l 
              LEFT JOIN Utilizadores u ON l.user_id = u.ID_Utilizador 
              LEFT JOIN Utilizadores a ON l.admin_id = a.ID_Utilizador";

$params = [];
$types = "";

// Adicionar busca se houver termo de pesquisa
if (!empty($search)) {
    $search_term = "%$search%";
    $sql .= " WHERE u.Nome LIKE ? OR a.Nome LIKE ? OR l.action LIKE ? OR l.details LIKE ?";
    $count_sql .= " WHERE u.Nome LIKE ? OR a.Nome LIKE ? OR l.action LIKE ? OR l.details LIKE ?";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
    $types .= "ssss";
}

// Adicionar ordenação e paginação
$sql .= " ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
$params = array_merge($params, [$logs_per_page, $offset]);
$types .= "ii";

// Executar query de contagem
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_logs = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_logs / $logs_per_page);

// Executar query principal
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Preparar o HTML da tabela
$html = '';
if ($result->num_rows > 0) {
    while($log = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($log['user_name'] ?? 'Usuário excluído') . '</td>';
        $html .= '<td>' . htmlspecialchars($log['action']) . '</td>';
        $html .= '<td>' . htmlspecialchars($log['details']) . '</td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($log['created_at'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($log['admin_name']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html = '<tr><td colspan="5" class="text-center">Nenhum registro encontrado.</td></tr>';
}

// Preparar a paginação
$pagination = '';
if ($total_pages > 1) {
    $pagination .= '<nav aria-label="Navegação de logs">';
    $pagination .= '<ul class="pagination justify-content-center">';
    
    if ($page > 1) {
        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="#" data-page="' . ($page - 1) . '" aria-label="Anterior">';
        $pagination .= '<span aria-hidden="true">&laquo;</span>';
        $pagination .= '</a></li>';
    }
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination .= '<li class="page-item ' . ($i === $page ? 'active' : '') . '">';
        $pagination .= '<a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a>';
        $pagination .= '</li>';
    }
    
    if ($page < $total_pages) {
        $pagination .= '<li class="page-item">';
        $pagination .= '<a class="page-link" href="#" data-page="' . ($page + 1) . '" aria-label="Próximo">';
        $pagination .= '<span aria-hidden="true">&raquo;</span>';
        $pagination .= '</a></li>';
    }
    
    $pagination .= '</ul></nav>';
}

// Retornar o HTML
echo json_encode([
    'success' => true,
    'html' => $html,
    'pagination' => $pagination,
    'total' => $total_logs,
    'total_pages' => $total_pages
]); 