<?php
// Script de debug para testar login
require_once 'connection.php';

echo "<h2>Debug do Sistema de Login</h2>";

// Testar conexão com a base de dados
try {
    $result = $conn->query("SELECT COUNT(*) as total FROM Utilizadores");
    $total = $result->fetch_assoc();
    echo "<p>✅ Conexão com BD OK - Total de utilizadores: " . $total['total'] . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Erro na conexão: " . $e->getMessage() . "</p>";
}

// Mostrar estrutura da tabela
try {
    $result = $conn->query("DESCRIBE Utilizadores");
    echo "<h3>Estrutura da tabela Utilizadores:</h3>";
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . "</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p>❌ Erro ao verificar estrutura: " . $e->getMessage() . "</p>";
}

// Mostrar alguns utilizadores (sem senhas)
try {
    $result = $conn->query("SELECT ID_Utilizador, Nome, Email, Tipo_Utilizador FROM Utilizadores LIMIT 5");
    echo "<h3>Primeiros 5 utilizadores:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['ID_Utilizador'] . "</td>";
        echo "<td>" . htmlspecialchars($row['Nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Tipo_Utilizador']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p>❌ Erro ao listar utilizadores: " . $e->getMessage() . "</p>";
}

// Verificar se um email específico existe
if (isset($_GET['test_email'])) {
    $email = $_GET['test_email'];
    $stmt = $conn->prepare("SELECT ID_Utilizador, Nome, Email FROM Utilizadores WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        echo "<h3>✅ Email '$email' encontrado:</h3>";
        echo "<p>ID: " . $user['ID_Utilizador'] . "</p>";
        echo "<p>Nome: " . htmlspecialchars($user['Nome']) . "</p>";
        echo "<p>Email: " . htmlspecialchars($user['Email']) . "</p>";
    } else {
        echo "<h3>❌ Email '$email' NÃO encontrado na base de dados</h3>";
    }
}

echo "<hr>";
echo "<p>Para testar um email específico, adicione ?test_email=seuemail@exemplo.com à URL</p>";
?> 