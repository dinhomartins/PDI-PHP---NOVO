<?php
include 'db.php';

$employee_id = $_GET['employee_id'];
$datetime = $_GET['datetime'];

$response = [];

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Exclui todos os feedbacks do funcionário na data e hora especificadas
$sql = "DELETE FROM feedback WHERE employee_id = $employee_id AND DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') = '$datetime'";

if ($conn->query($sql) === TRUE) {
    $response['status'] = 'success';
    $response['message'] = 'Feedbacks excluídos com sucesso';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Erro: ' . $conn->error;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
