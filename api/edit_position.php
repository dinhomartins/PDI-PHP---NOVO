<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$position_id = $data['id'];
$position_name = $data['name'];

$sql = "UPDATE positions SET name='$position_name' WHERE id=$position_id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Cargo editado com sucesso']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao editar o cargo: ' . $conn->error]);
}
?>
