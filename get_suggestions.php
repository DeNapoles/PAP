<?php
require_once 'connection.php';
header('Content-Type: application/json');

// Verificar se o utilizador está autenticado e é Admin
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
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
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    // Se é para buscar uma sugestão específica
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM sugestoes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $suggestion = $result->fetch_assoc();
        
        if ($suggestion) {
            echo json_encode(['success' => true, 'suggestion' => $suggestion]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sugestão não encontrada']);
        }
        exit;
    }

    // Parâmetros de paginação e filtros
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
    $priority_filter = isset($_GET['priority']) ? trim($_GET['priority']) : '';

    // Construir a query base
    $where_conditions = [];
    $params = [];
    $types = '';

    // Adicionar condições de pesquisa
    if (!empty($search)) {
        $where_conditions[] = "(nome LIKE ? OR email LIKE ? OR mensagem LIKE ? OR tipo_sugestao LIKE ?)";
        $search_term = "%$search%";
        $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        $types .= 'ssss';
    }

    // Adicionar filtro de status
    if (!empty($status_filter)) {
        $where_conditions[] = "status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }

    // Adicionar filtro de prioridade
    if (!empty($priority_filter)) {
        $where_conditions[] = "prioridade = ?";
        $params[] = $priority_filter;
        $types .= 's';
    }

    // Montar a cláusula WHERE
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }

    // Contar total de registros
    $count_sql = "SELECT COUNT(*) as total FROM sugestoes $where_clause";
    if (!empty($params)) {
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
    } else {
        $count_result = $conn->query($count_sql);
    }
    
    $total_records = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $per_page);

    // Buscar registros com paginação
    $sql = "SELECT * FROM sugestoes $where_clause ORDER BY data_criacao DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    // Preparar resposta
    $response = [
        'success' => true,
        'suggestions' => $suggestions,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records,
            'per_page' => $per_page
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?> 