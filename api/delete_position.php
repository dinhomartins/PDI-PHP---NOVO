<?php
include 'db.php';

$position_id = $_GET['id'];

$sql = "DELETE FROM positions WHERE id=$position_id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Cargo excluído com sucesso']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o cargo: ' . $conn->error]);
}
?>
