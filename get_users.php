<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Não mostrar erros diretamente na página, mas logar ou capturar

require_once 'connection.php';

// Configurar o cabeçalho para JSON no início
header('Content-Type: application/json');

// Função para enviar resposta de erro em JSON
function sendErrorResponse($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Capturar erros fatais que não são pegos pelo try-catch (ex: require falhou)
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR || $error['type'] === E_CORE_ERROR)) {
        // Log do erro (opcional)
        // error_log("Fatal error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']);
        // Tenta enviar uma resposta JSON de erro se nada foi enviado ainda
        if (!headers_sent()) {
            sendErrorResponse('Ocorreu um erro interno no servidor.');
        }
    }
});

// Habilitar tratamento de exceções para mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    sendErrorResponse('Não autorizado');
}

// Buscar informações do utilizador
$stmt = $conn->prepare("SELECT Tipo_Utilizador FROM Utilizadores WHERE ID_Utilizador = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Tipo_Utilizador'] !== 'Admin') {
    sendErrorResponse('Não autorizado');
}

// Função para registrar alterações no log - Esta função deve estar fora do try/catch principal se for usada em outros arquivos
// Para manter a coesão com o código existente, vou deixá-la aqui por enquanto, mas idealmente estaria em um arquivo de funções global.
function logUserChange($userId, $action, $details) {
    global $conn; // Certifique-se de que $conn está acessível
    // Adicionado tratamento de erro básico para o log
    try {
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, details, admin_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $userId, $action, $details, $_SESSION['user_id']);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        // Log do erro de log (para evitar loops ou falhas no log)
        error_log("Erro ao registrar log de usuário: " . $e->getMessage());
        return false;
    }
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
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        sendErrorResponse('Utilizador não encontrado');
    }
    exit;
}

// Se for uma requisição para listar usuários com paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$users_per_page = 10;
$offset = ($page - 1) * $users_per_page;

// Construir a query base
$sql = "SELECT ID_Utilizador, Nome, Email, Tipo_Utilizador, Estado FROM Utilizadores";
$count_sql = "SELECT COUNT(*) as total FROM Utilizadores";
$params = [];
$types = "";

// Adicionar busca se houver termo de pesquisa
if (!empty($search)) {
    $search_term = "%$search%";
    $sql .= " WHERE Nome LIKE ? OR Email LIKE ? OR Tipo_Utilizador LIKE ? OR Estado LIKE ?";
    $count_sql .= " WHERE Nome LIKE ? OR Email LIKE ? OR Tipo_Utilizador LIKE ? OR Estado LIKE ?";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
    $types .= "ssss";
}

// Adicionar ordenação e paginação
$sql .= " ORDER BY ID_Utilizador DESC LIMIT ? OFFSET ?";
$params = array_merge($params, [$users_per_page, $offset]);
$types .= "ii";

// Executar query de contagem
$stmt = $conn->prepare($count_sql);
// Bind parameters only if search term exists for count query
if (!empty($search)) {
    $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $users_per_page);

// Executar query principal
$stmt = $conn->prepare($sql);
// Bind parameters based on whether search term exists
if (!empty($search)) {
    $stmt->bind_param("ssssii", $search_term, $search_term, $search_term, $search_term, $users_per_page, $offset);
} else {
    $stmt->bind_param("ii", $users_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// Preparar o HTML da tabela
$html = '';
if ($result->num_rows > 0) {
    while($user = $result->fetch_assoc()) {
        $html .= '<tr class="user-row">';
        // Coluna Nome (avatar, nome)
        $html .= '<td class="align-middle">';
        $html .= '<div class="d-flex align-items-center gap-3">';
        $html .= '<div class="avatar-wrapper">';
        $html .= '<span class="avatar bg-primary text-white rounded-circle" style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:1.2rem;">';
        $html .= strtoupper(substr($user['Nome'],0,1));
        $html .= '</span>';
        $html .= '</div>';
        $html .= '<div class="user-info">';
        $html .= '<span class="user-name fw-bold">' . htmlspecialchars($user['Nome']) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</td>';
        // Coluna Email
        $html .= '<td class="align-middle">';
        $html .= '<span class="user-email text-muted d-block small">' . htmlspecialchars($user['Email']) . '</span>';
        $html .= '</td>';
        // Coluna Tipo (Dropdown)
        $html .= '<td class="align-middle">';
        $html .= '<select class="form-select form-select-sm user-type-select" data-id="' . $user['ID_Utilizador'] . '" style="min-width:120px;">';
        $html .= '<option value="Aluno"' . ($user['Tipo_Utilizador'] == 'Aluno' ? ' selected' : '') . '>Aluno</option>';
        $html .= '<option value="Professor"' . ($user['Tipo_Utilizador'] == 'Professor' ? ' selected' : '') . '>Professor</option>';
        $html .= '<option value="Admin"' . ($user['Tipo_Utilizador'] == 'Admin' ? ' selected' : '') . '>Admin</option>';
        $html .= '</select>';
        $html .= '</td>';
        // Coluna Estado (botão)
        $html .= '<td class="align-middle">';
        $estadoBtnClass = $user['Estado'] == 'Ativo' ? 'btn-success' : 'btn-secondary';
        $estadoBtnText = $user['Estado'] == 'Ativo' ? 'Ativo' : 'Inativo';
        $html .= '<button type="button" class="btn btn-sm btn-estado-toggle ' . $estadoBtnClass . '" data-id="' . $user['ID_Utilizador'] . '" data-estado="' . $user['Estado'] . '">' . $estadoBtnText . '</button>';
        $html .= '</td>';
        // Coluna Ações
        $html .= '<td class="align-middle">';
        $html .= '<div class="d-flex gap-2">';
        $html .= '<button type="button" class="btn btn-sm btn-icon btn-delete delete-user-btn" style="color:#fff;background:#dc3545;" data-id="' . $user['ID_Utilizador'] . '" title="Apagar">';
        $html .= '<i class="fas fa-trash"></i>';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
    }
} else {
    $html = '<tr><td colspan="5" class="text-center py-4"><div class="text-muted">Nenhum utilizador encontrado.</div></td></tr>';
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

} catch (mysqli_sql_exception $e) {
    // Log any SQL errors
    error_log("SQL Error in get_users.php: " . $e->getMessage());
    // Send a generic error response to the client
    sendErrorResponse('Ocorreu um erro ao carregar os utilizadores. Detalhes: ' . $e->getMessage()); // Pode remover o $e->getMessage() em produção
} catch (Exception $e) {
    // Catch any other unexpected errors
    error_log("Unexpected Error in get_users.php: " . $e->getMessage());
    sendErrorResponse('Ocorreu um erro inesperado ao carregar os utilizadores.');
}
?> 