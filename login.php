<?php
require_once 'connection.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}

$stmt = $conn->prepare("SELECT ID_Utilizador, Nome, Email, Senha, Tipo_Utilizador FROM Utilizadores WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Verifica a senha (simples, sem hash)
    if ($user['Senha'] === $senha) {
        echo json_encode([
            'success' => true,
            'message' => 'Login efetuado com sucesso!',
            'user' => [
                'id' => $user['ID_Utilizador'],
                'nome' => $user['Nome'],
                'email' => $user['Email'],
                'tipo' => $user['Tipo_Utilizador']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Utilizador não encontrado.']);
}

$stmt->close();
$conn->close();
?>
