<?php

// Este ficheiro processa a submissão do formulário de ticket.

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
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado. Por favor, faça login para submeter um ticket.']);
    exit;
}

$id_utilizador = $_SESSION['user_id'];

// Obtém e valida os campos obrigatórios
$numero_processo_aluno = $_POST['numero_processo_aluno'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$tipo_equipamento = $_POST['tipo_equipamento'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$data_hora_agendamento = $_POST['data_hora_agendamento'] ?? '';

// Campos não obrigatórios
$numero_serie = $_POST['numero_serie'] ?? '';

if (empty($numero_processo_aluno) || empty($titulo) || empty($tipo_equipamento) || empty($descricao) || empty($data_hora_agendamento)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
    exit;
}

// Define o estado inicial e a data de submissão
$estado = 'Pendente';
$data_submissao = date('Y-m-d H:i:s'); // Data e hora atuais

// Prepara a query SQL para inserir os dados
$sql = "INSERT INTO Tickets (ID_Utilizador, Numero_Processo_Aluno, Titulo, Descricao, Tipo_Equipamento, Numero_Serie, Estado, Data_Submissao, Data_Marcada) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Erro ao preparar a query
    echo json_encode(['success' => false, 'message' => 'Erro interno ao preparar a submissão do ticket.', 'error' => $conn->error]);
    exit;
}

// Liga os parâmetros à query preparada
// Tipos: i = integer, s = string
$stmt->bind_param("iisssssss",
    $id_utilizador,
    $numero_processo_aluno,
    $titulo,
    $descricao,
    $tipo_equipamento,
    $numero_serie,
    $estado,
    $data_submissao,
    $data_hora_agendamento // Usamos o valor do input datetime-local
);

// Executa a query
if ($stmt->execute()) {
    // Inserção bem sucedida
    echo json_encode(['success' => true, 'message' => 'Ticket submetido com sucesso!']);
} else {
    // Erro na inserção
    echo json_encode(['success' => false, 'message' => 'Erro ao submeter o ticket. Por favor, tente novamente.', 'error' => $stmt->error]);
}

// Fecha o statement e a ligação à base de dados
$stmt->close();
$conn->close();

?> 