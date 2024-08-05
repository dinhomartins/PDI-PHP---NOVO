<?php
include 'db.php';

$employee_id = $_GET['employee_id'];
$date = $_GET['date'];

$response = [];

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Obtém todos os feedbacks do funcionário na data especificada
$sql = "SELECT feedback FROM feedback WHERE employee_id = $employee_id AND DATE_FORMAT(created_at, '%d/%m/%Y') = '$date'";
$result = $conn->query($sql);

$feedbacks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    $response['status'] = 'success';
    $response['feedbacks'] = $feedbacks;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Nenhum feedback encontrado para a data especificada.';
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
