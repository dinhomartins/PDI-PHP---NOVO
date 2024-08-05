<?php
include 'db.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit();
}

// Iniciar transação
$conn->begin_transaction();

try {
    // Remover as áreas de atuação do gestor
    $sql_areas = "DELETE FROM manager_areas WHERE manager_id = ?";
    $stmt_areas = $conn->prepare($sql_areas);
    $stmt_areas->bind_param('i', $id);
    if (!$stmt_areas->execute()) {
        throw new Exception("Erro ao remover áreas do gestor: " . $stmt_areas->error);
    }

    // Remover o gestor
    $sql = "DELETE FROM managers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao remover gestor: " . $stmt->error);
    }

    // Confirmar transação
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Gestor removido com sucesso']);
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt_areas->close();
$stmt->close();
$conn->close();
