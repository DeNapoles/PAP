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

// Função para registrar alterações no log
function logUserChange($userId, $action, $details) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userId, $action, $details, $_SESSION['user_id']);
    return $stmt->execute();
}

// Se for uma requisição para buscar um usuário específico
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT ID_Utilizador, Nome, Email, Tipo_Utilizador, Estado, Data_Registo FROM Utilizadores WHERE ID_Utilizador = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Utilizador não encontrado'
        ]);
    }
    exit;
}

// Se for uma requisição para listar usuários com paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$users_per_page = 10;
$offset = ($page - 1) * $users_per_page;

// Construir a query base
$sql = "SELECT ID_Utilizador, Nome, Email, Tipo_Utilizador, Estado, Data_Registo FROM Utilizadores";
$count_sql = "SELECT COUNT(*) as total FROM Utilizadores";
$params = [];
$types = "";

// Adicionar busca se houver termo de pesquisa
if (!empty($search)) {
    $search_term = "%$search%";
    $sql .= " WHERE Nome LIKE ? OR Email LIKE ? OR Tipo_Utilizador LIKE ?";
    $count_sql .= " WHERE Nome LIKE ? OR Email LIKE ? OR Tipo_Utilizador LIKE ?";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= "sss";
}

// Adicionar ordenação e paginação
$sql .= " ORDER BY Data_Registo DESC LIMIT ? OFFSET ?";
$params = array_merge($params, [$users_per_page, $offset]);
$types .= "ii";

// Executar query de contagem
$stmt = $conn->prepare($count_sql);
if (!empty($search)) {
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $users_per_page);

// Executar query principal
$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("sssii", $search_term, $search_term, $search_term, $users_per_page, $offset);
} else {
    $stmt->bind_param("ii", $users_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Preparar o HTML da tabela
$html = '';
if ($result->num_rows > 0) {
    while($user = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td class="align-middle"><div class="d-flex align-items-center gap-2">'
               . '<span class="avatar bg-primary text-white rounded-circle" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">'
               . strtoupper(substr($user['Nome'],0,1))
               . '</span>'
               . '<span>' . htmlspecialchars($user['Nome']) . '</span></div></td>';
        $html .= '<td class="align-middle">' . htmlspecialchars($user['Email']) . '</td>';
        $html .= '<td class="align-middle"><span class="badge bg-info text-dark">' . htmlspecialchars($user['Tipo_Utilizador']) . '</span></td>';
        $html .= '<td class="align-middle">'
               . '<div class="form-check form-switch">'
               . '<input class="form-check-input" type="checkbox" role="switch" id="status_' . $user['ID_Utilizador'] . '" '
               . ($user['Estado'] == 'Ativo' ? 'checked' : '') . ' onchange="updateUserStatus(' . $user['ID_Utilizador'] . ', this.checked)">' 
               . '<label class="form-check-label ms-2" for="status_' . $user['ID_Utilizador'] . '">' 
               . ($user['Estado'] == 'Ativo' ? '<span class=\'badge bg-success\'>Ativo</span>' : '<span class=\'badge bg-secondary\'>Inativo</span>')
               . '</label></div></td>';
        $html .= '<td class="align-middle">' . date('d/m/Y H:i', strtotime($user['Data_Registo'])) . '</td>';
        $html .= '<td class="align-middle">'
               . '<div class="btn-group" role="group">'
               . '<button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser(' . $user['ID_Utilizador'] . ')" title="Editar"><i class="fas fa-edit"></i></button>'
               . '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser(' . $user['ID_Utilizador'] . ')" title="Apagar"><i class="fas fa-trash"></i></button>'
               . '</div></td>';
        $html .= '</tr>';
    }
} else {
    $html = '<tr><td colspan="6" class="text-center">Nenhum utilizador encontrado.</td></tr>';
}

// Preparar a paginação
$pagination = '';
if ($total_pages > 1) {
    $pagination .= '<nav aria-label="Navegação de utilizadores">';
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
    'total' => $total_users,
    'total_pages' => $total_pages
]); 