<?php
include 'db.php';

$employee_id = $_POST['employee_id'];
$feedbacks = $_POST['feedback'];

$response = [];

// Verifique se a conexão com o banco de dados foi bem-sucedida
if ($conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

foreach ($feedbacks as $feedback) {
    $feedback = $conn->real_escape_string($feedback);
    $sql = "INSERT INTO feedback (employee_id, feedback) VALUES ('$employee_id', '$feedback')";

    if ($conn->query($sql) !== TRUE) {
        $response['status'] = 'error';
        $response['message'] = 'Erro: ' . $conn->error;
        echo json_encode($response);
        $conn->close();
        exit();
    }
}

$response['status'] = 'success';
$response['message'] = 'Feedback adicionado com sucesso';
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
