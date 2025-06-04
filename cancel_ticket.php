<?php
require_once 'functions.php';
require_once 'connection.php';

// Verificar se o utilizador está autenticado e é um aluno
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirecionar para a página de login ou mostrar erro
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Obter o ID do ticket a cancelar
// Usar GET por enquanto para simplificar o exemplo com window.location.href
// Em um cenário real, usar POST seria mais seguro e apropriado para uma operação de exclusão.
$ticket_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID do ticket é válido
if (!$ticket_id) {
    // Redirecionar ou mostrar erro
    header('Location: view_tickets.php?error=invalid_id');
    exit;
}

// Verificar se o ticket pertence ao utilizador logado e se o estado permite cancelamento
$stmt = $conn->prepare("SELECT ID_Ticket, Estado FROM Tickets WHERE ID_Ticket = ? AND ID_Utilizador = ?");
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    // Ticket não encontrado ou não pertence a este utilizador
    header('Location: view_tickets.php?error=not_found_or_unauthorized');
    exit;
}

// Verificar se o estado permite cancelamento
if ($ticket['Estado'] !== 'Pendente' && $ticket['Estado'] !== 'Em Progresso') {
    // Estado não permite cancelamento
    header('Location: view_tickets.php?error=cannot_cancel');
    exit;
}

// Remover o ticket da base de dados
$stmt = $conn->prepare("DELETE FROM Tickets WHERE ID_Ticket = ?");
$stmt->bind_param("i", $ticket_id);

if ($stmt->execute()) {
    // Redirecionar de volta para a página de visualização com mensagem de sucesso
    header('Location: view_tickets.php?success=cancelled');
    exit;
} else {
    // Erro ao remover o ticket
    header('Location: view_tickets.php?error=db_error');
    exit;
}

$stmt->close();
$conn->close();
?> 