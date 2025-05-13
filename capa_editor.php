<?php
require_once 'connection.php';

// Função para buscar os dados atuais
function getCapaData() {
    global $conn;
    $sql = "SELECT * FROM InicioInicio LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Função para atualizar os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_capa'])) {
    $logoSeparador = $_POST['LogoSeparador'];
    $logoPrincipal = $_POST['LogoPrincipal'];
    $textoBemvindo = $_POST['TextoBemvindo'];
    $textoInicial = $_POST['TextoInicial'];
    $textoInicial2 = $_POST['TextoInicial2'];
    $botaoInicial = $_POST['BotaoInicial'];
    $fundo = $_POST['Fundo'];

    $sql = "UPDATE InicioInicio SET 
            LogoSeparador = ?,
            LogoPrincipal = ?,
            TextoBemvindo = ?,
            TextoInicial = ?,
            TextoInicial2 = ?,
            BotaoInicial = ?,
            Fundo = ?
            WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", 
        $logoSeparador,
        $logoPrincipal,
        $textoBemvindo,
        $textoInicial,
        $textoInicial2,
        $botaoInicial,
        $fundo
    );

    if ($stmt->execute()) {
        $message = "Dados atualizados com sucesso!";
        $messageType = "success";
    } else {
        $message = "Erro ao atualizar dados: " . $conn->error;
        $messageType = "danger";
    }
}

$capaData = getCapaData();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Conteúdo da Capa</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="LogoSeparador" class="form-label">Logo Separador</label>
                            <input type="text" class="form-control" id="LogoSeparador" name="LogoSeparador" 
                                   value="<?php echo htmlspecialchars($capaData['LogoSeparador']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="LogoPrincipal" class="form-label">Logo Principal</label>
                            <input type="text" class="form-control" id="LogoPrincipal" name="LogoPrincipal" 
                                   value="<?php echo htmlspecialchars($capaData['LogoPrincipal']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="TextoBemvindo" class="form-label">Texto de Boas-vindas</label>
                            <textarea class="form-control" id="TextoBemvindo" name="TextoBemvindo" rows="2"><?php echo htmlspecialchars($capaData['TextoBemvindo']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="TextoInicial" class="form-label">Texto Inicial</label>
                            <textarea class="form-control" id="TextoInicial" name="TextoInicial" rows="2"><?php echo htmlspecialchars($capaData['TextoInicial']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="TextoInicial2" class="form-label">Texto Inicial 2</label>
                            <textarea class="form-control" id="TextoInicial2" name="TextoInicial2" rows="3"><?php echo htmlspecialchars($capaData['TextoInicial2']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="BotaoInicial" class="form-label">Texto do Botão</label>
                            <input type="text" class="form-control" id="BotaoInicial" name="BotaoInicial" 
                                   value="<?php echo htmlspecialchars($capaData['BotaoInicial']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Fundo" class="form-label">Imagem de Fundo</label>
                            <input type="text" class="form-control" id="Fundo" name="Fundo" 
                                   value="<?php echo htmlspecialchars($capaData['Fundo']); ?>">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" name="update_capa" class="btn btn-primary">Salvar Alterações</button>
                            <button type="reset" class="btn btn-secondary">Restaurar Valores</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.alert {
    margin-bottom: 20px;
}
</style> 