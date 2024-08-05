<?php
$servername = "localhost";
$username = "root";
$password = "02121985";
$dbname = "pdi_sistema";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
