<?php
include 'db.php';

$area_id = $_GET['id'];

$sql = "DELETE FROM areas WHERE id=$area_id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Área de atuação excluída com sucesso']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir a área de atuação: ' . $conn->error]);
}
?>
