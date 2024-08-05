<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id === 0) {
        throw new Exception("ID inválido");
    }

    $sql = "DELETE FROM users WHERE id = $id";
    if (!$conn->query($sql)) {
        throw new Exception("Erro ao remover funcionário: " . $conn->error);
    }

    echo json_encode(['status' => 'success', 'message' => 'Funcionário removido com sucesso']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
