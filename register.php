<?php
require_once 'connection.php';

header('Content-Type: application/json');

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$nome || !$email || !$senha) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}

// Validar força da senha
$hasLength = strlen($senha) >= 8;
$hasNumbers = preg_match_all('/[0-9]/', $senha) >= 2;
$hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $senha);
$hasUppercase = preg_match('/[A-Z]/', $senha);
$hasLowercase = preg_match('/[a-z]/', $senha);

if (!$hasLength || !$hasNumbers || !$hasSpecial || !$hasUppercase || !$hasLowercase) {
    echo json_encode(['success' => false, 'message' => 'A senha não cumpre todos os requisitos de segurança.']);
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

// Determinar o tipo de utilizador com base no email
$email_parts = explode('@', $email);
$local_part = $email_parts[0];
$tipo = preg_match('/[0-9]/', $local_part) ? 'Aluno' : 'Professor';

// Criptografar a senha
$senha_hash = password_hash($senha, PASSWORD_BCRYPT);

// Inserir novo utilizador
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
