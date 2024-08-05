// add_user.php
<?php
include 'api/db.php'; // Caminho correto para db.php

$name = 'Ricard';
$email = 'ricardo2@wedocare.com.br';
$password = '123456'; // Senha em texto simples
$role = 'administrador';

$sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
if ($conn->query($sql) === TRUE) {
    echo "Usuário cadastrado com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
