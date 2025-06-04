<?php

// Este ficheiro processa a atualização da data agendada de um ticket.

session_start(); // Inicia a sessão para aceder ao ID do utilizador

require_once 'connection.php'; // Inclui o ficheiro de ligação à base de dados

header('Content-Type: application/json'); // Define o cabeçalho para JSON

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit;
}

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado.']);
    exit;
}

$id_utilizador = $_SESSION['user_id'];
$ticket_id = $_POST['ticket_id'] ?? null;
$new_data_marcada = $_POST['new_data_marcada'] ?? null;

// Valida os inputs
if ($ticket_id === null || $new_data_marcada === null) {
    echo json_encode(['success' => false, 'message' => 'Dados insuficientes para a atualização.']);
    exit;
}

// Valida se o ticket pertence ao utilizador logado antes de atualizar
$stmt = $conn->prepare("SELECT ID_Utilizador FROM Tickets WHERE ID_Ticket = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo json_encode(['success' => false, 'message' => 'Ticket não encontrado.']);
    $stmt->close();
    $conn->close();
    exit;
}

if ($ticket['ID_Utilizador'] != $id_utilizador) {
    echo json_encode(['success' => false, 'message' => 'Não tem permissão para editar este ticket.']);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Define o novo estado como 'Pendente'
$novo_estado = 'Pendente';

// Prepara a query SQL para atualizar a data agendada e o estado
// Certifica-se que a nova data está no formato correto para a base de dados (YYYY-MM-DD HH:MM:SS)
$new_data_marcada_formatted = date('Y-m-d H:i:s', strtotime($new_data_marcada));

$sql = "UPDATE Tickets SET Data_Marcada = ?, Estado = ? WHERE ID_Ticket = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Erro ao preparar a query
    echo json_encode(['success' => false, 'message' => 'Erro interno ao preparar a atualização do ticket.', 'error' => $conn->error]);
    $conn->close();
    exit;
}

// Liga os parâmetros à query preparada
// Tipos: s = string, i = integer
$stmt->bind_param("ssi",
    $new_data_marcada_formatted,
    $novo_estado,
    $ticket_id
);

// Executa a query
if ($stmt->execute()) {
    // Atualização bem sucedida
    echo json_encode(['success' => true, 'message' => 'Data agendada atualizada com sucesso. O técnico irá rever a alteração.']);
} else {
    // Erro na atualização
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar a data agendada. Por favor, tente novamente.', 'error' => $stmt->error]);
}

// Fecha o statement e a ligação à base de dados
$stmt->close();
$conn->close();

?> 