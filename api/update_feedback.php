<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$feedback_id = $data['feedback_id'];
$feedback = $data['feedback'];

$response = [];

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

$feedback = $conn->real_escape_string($feedback);
$sql = "UPDATE feedback SET feedback='$feedback' WHERE id=$feedback_id";

if ($conn->query($sql) === TRUE) {
    $response['status'] = 'success';
    $response['message'] = 'Feedback atualizado com sucesso';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Erro: ' . $conn->error;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
