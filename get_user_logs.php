<?php
// Enable error reporting for debugging (should be disabled in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Do not display errors directly on the page

require_once 'connection.php';

// Set header to indicate JSON content
header('Content-Type: application/json');

// Function to send JSON error response
function sendErrorResponse($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

try {
    // Get pagination and search parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $logs_per_page = 15; // Define how many logs per page
    $offset = ($page - 1) * $logs_per_page;

    // Prepare SQL query for fetching logs with search and pagination
    // Assuming 'user_logs' table has columns: id, user_id, action, details, admin_id, created_at
    // We need to join with 'Utilizadores' to get user and admin names
    $sql = "SELECT ul.*, u.Nome AS user_name, a.Nome AS admin_name ";
    $sql .= "FROM user_logs ul ";
    $sql .= "LEFT JOIN Utilizadores u ON ul.user_id = u.ID_Utilizador "; // User who was affected
    $sql .= "LEFT JOIN Utilizadores a ON ul.admin_id = a.ID_Utilizador "; // Admin who performed the action

    $count_sql = "SELECT COUNT(*) AS total FROM user_logs ul ";
    $count_sql .= "LEFT JOIN Utilizadores u ON ul.user_id = u.ID_Utilizador ";
    $count_sql .= "LEFT JOIN Utilizadores a ON ul.admin_id = a.ID_Utilizador ";

    $where_clauses = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        // Search in user_name, admin_name, action, details
        $where_clauses[] = "(u.Nome LIKE ? OR a.Nome LIKE ? OR ul.action LIKE ? OR ul.details LIKE ?)";
        $like_search = '%' . $search . '%';
        $params = [$like_search, $like_search, $like_search, $like_search];
        $types = 'ssss';
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
        $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    // Order by creation date descending
    $sql .= " ORDER BY ul.created_at DESC";

    // Add pagination to the main query
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $logs_per_page;
    $params[] = $offset;
    $types .= 'ii';

    // Get total number of logs for pagination
    $count_stmt = $conn->prepare($count_sql);
     if (!empty($search)) {
        $count_stmt->bind_param(str_repeat('s', count($params) - 2), ...array_slice($params, 0, count($params) - 2));
    }
    $count_stmt->execute();
    $total_logs = $count_stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_logs / $logs_per_page);

    // Execute the main query
    $stmt = $conn->prepare($sql);
     if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $logs = [];
    while ($row = $result->fetch_assoc()) {
        // Format created_at if needed (optional)
        $row['created_at_formatted'] = date('d/m/Y H:i:s', strtotime($row['created_at']));
        $logs[] = $row;
    }

    // Generate pagination HTML
    $pagination_html = '<nav><ul class="pagination justify-content-center">';
    // Previous button
    $pagination_html .= '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="#" data-page="' . ($page - 1) . '">Anterior</a></li>';
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination_html .= '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
    }
    // Next button
    $pagination_html .= '<li class="page-item ' . ($page >= $total_pages ? 'disabled' : '') . '"><a class="page-link" href="#" data-page="' . ($page + 1) . '">Próximo</a></li>';
    $pagination_html .= '</ul></nav>';

    // Generate table rows HTML
    $table_html = '';
    if (count($logs) > 0) {
        foreach ($logs as $log) {
            $table_html .= '<tr>';
            $table_html .= '<td>' . htmlspecialchars($log['user_name'] ?? 'N/A') . '</td>'; // User affected
            $table_html .= '<td>' . htmlspecialchars($log['action']) . '</td>';
            $table_html .= '<td>' . htmlspecialchars($log['details']) . '</td>';
            $table_html .= '<td>' . htmlspecialchars($log['created_at_formatted']) . '</td>';
            $table_html .= '<td>' . htmlspecialchars($log['admin_name'] ?? 'N/A') . '</td>'; // Admin who did it
            $table_html .= '</tr>';
        }
    } else {
        $table_html .= '<tr><td colspan="5" class="text-center">Nenhum registo de alteração encontrado.</td></tr>';
    }

    // Send JSON response with HTML and pagination
    echo json_encode(['success' => true, 'html' => $table_html, 'pagination' => $pagination_html]);

} catch (Exception $e) {
    // Log the error (optional)
    error_log("Error in get_user_logs.php: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching user logs.');
}

$conn->close();
?> 