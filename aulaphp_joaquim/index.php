<?php
// Incluir arquivo de conexão
require_once 'db.php';

// Recebe a placa da URL (GET) ou do formulário (POST)
$placa = null;

if (isset($_GET['placa'])) {
    $placa = $_GET['placa'];
} elseif (isset($_POST['placa'])) {
    $placa = $_POST['placa'];
}

// Variável para mensagem ao usuário
$mensagem = '';

if ($placa !== null) {
    try {
        // Verifica se a placa existe na tabela veiculos
        $sql_check = "SELECT id_veiculo FROM veiculos WHERE placa = :placa";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // Placa encontrada, pega o ID do veículo
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $id_veiculo = $row['id_veiculo'];

            // Registra o acesso na tabela acessos
            $sql_insert_access = "INSERT INTO acessos (id_veiculo, data_hora) VALUES (:id_veiculo, NOW())";
            $stmt_insert_access = $conn->prepare($sql_insert_access);
            $stmt_insert_access->bindParam(':id_veiculo', $id_veiculo, PDO::PARAM_INT);
            $stmt_insert_access->execute();

            $mensagem = "Acesso registrado com sucesso para a placa já existente: " . htmlspecialchars($placa);
        } else {
            // Placa não encontrada, insere na tabela veiculos
            $sql_insert_vehicle = "INSERT INTO veiculos (placa) VALUES (:placa)";
            $stmt_insert_vehicle = $conn->prepare($sql_insert_vehicle);
            $stmt_insert_vehicle->bindParam(':placa', $placa, PDO::PARAM_STR);
            $stmt_insert_vehicle->execute();

            // Pega o ID do veículo recém-criado
            $id_veiculo = $conn->lastInsertId();

            // Registra o acesso na tabela acessos
            $sql_insert_access = "INSERT INTO acessos (id_veiculo, data_hora) VALUES (:id_veiculo, NOW())";
            $stmt_insert_access = $conn->prepare($sql_insert_access);
            $stmt_insert_access->bindParam(':id_veiculo', $id_veiculo, PDO::PARAM_INT);
            $stmt_insert_access->execute();

            $mensagem = "Placa não encontrada. Nova placa cadastrada e acesso registrado: " . htmlspecialchars($placa);
        }
    } catch (PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de Placa de Carro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Envio de Placa de Carro</h1>
        
        <!-- Exibe mensagem de sucesso ou erro -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info mt-4">
                <?= htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário para enviar a placa por POST -->
        <form action="index.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="placa" class="form-label">Placa de Carro</label>
                <input type="text" class="form-control" id="placa" name="placa" required placeholder="Exemplo: ABC-1234">
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        <hr>

        <h3>Ou, envie a placa pela URL:</h3>
        <p>Exemplo: <code>index.php?placa=ABC-1234</code></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
