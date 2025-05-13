<?php
require_once 'connection.php';

header('Content-Type: application/json');

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$tipo = $_POST['tipo'] ?? '';

if (!$nome || !$email || !$senha || !$tipo) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}

// Verificar se o email já existe
$stmt = $conn->prepare("SELECT ID_Utilizador FROM Utilizadores WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'O email já está registado.']);
    exit;
}
$stmt->close();

// Inserir novo utilizador
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO Utilizadores (Nome, Email, Senha, Tipo_Utilizador) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);

if ($stmt->execute()) {
    $new_id = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Registo efetuado com sucesso!',
        'id' => $new_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao registar.']);
}
$stmt->close();
$conn->close();
?>
