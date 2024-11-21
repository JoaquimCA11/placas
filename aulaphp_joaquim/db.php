<?php
$host = 'localhost'; // Host do banco de dados
$port = '3307'; // Porta do MySQL
$dbname = 'sistema_placas'; // Nome do banco de dados
$username = 'root'; // Usuário do banco de dados
$password = ''; // Senha do banco de dados (deixe vazio no XAMPP)

try {
    // Conexão usando PDO
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
