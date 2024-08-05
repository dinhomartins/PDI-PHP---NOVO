<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$area_id = $data['id'];
$area_name = $data['name'];

$sql = "UPDATE areas SET name='$area_name' WHERE id=$area_id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Área de atuação editada com sucesso']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao editar a área de atuação: ' . $conn->error]);
}
?>
