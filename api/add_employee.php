<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$position = $_POST['position'];
$area = $_POST['area'];
$role = $_POST['role'];

// Certifique-se de que os dados estão seguros para uso em SQL
$name = $conn->real_escape_string($name);
$email = $conn->real_escape_string($email);
$position = $conn->real_escape_string($position);
$area = $conn->real_escape_string($area);
$role = $conn->real_escape_string($role);

$response = [];

$sql = "INSERT INTO users (name, email, password, role, position, area) VALUES ('$name', '$email', 'password_placeholder', '$role', '$position', '$area')";

if ($conn->query($sql) === TRUE) {
    $response['status'] = 'success';
    $response['message'] = 'Usuário cadastrado com sucesso';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Erro: ' . $conn->error;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
