<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $position_name = $_POST['position_name'];

    $sql = "INSERT INTO positions (name) VALUES ('$position_name')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Cargo adicionado com sucesso']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar o cargo: ' . $conn->error]);
    }
}
?>
