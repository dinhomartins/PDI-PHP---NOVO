<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $area_name = $_POST['area_name'];

    $sql = "INSERT INTO areas (name) VALUES ('$area_name')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Área de atuação adicionada com sucesso']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar a área de atuação: ' . $conn->error]);
    }
}
?>
