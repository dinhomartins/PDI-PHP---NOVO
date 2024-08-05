<?php
include 'db.php';

header('Content-Type: application/json');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$areas = isset($_POST['areas']) ? $_POST['areas'] : [];

if ($id <= 0 || empty($name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
    exit();
}

$conn->begin_transaction();

try {
    // Atualiza os dados do gestor
    $sql = "UPDATE managers SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $name, $email, $id);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao atualizar gestor: " . $stmt->error);
    }

    // Remove as áreas de atuação atuais do gestor
    $sql_delete_areas = "DELETE FROM manager_areas WHERE manager_id = ?";
    $stmt_delete_areas = $conn->prepare($sql_delete_areas);
    $stmt_delete_areas->bind_param('i', $id);
    if (!$stmt_delete_areas->execute()) {
        throw new Exception("Erro ao remover áreas do gestor: " . $stmt_delete_areas->error);
    }

    // Adiciona as novas áreas de atuação do gestor
    $sql_insert_area = "INSERT INTO manager_areas (manager_id, area_id) VALUES (?, ?)";
    $stmt_insert_area = $conn->prepare($sql_insert_area);
    foreach ($areas as $area_id) {
        $stmt_insert_area->bind_param('ii', $id, $area_id);
        if (!$stmt_insert_area->execute()) {
            throw new Exception("Erro ao adicionar áreas ao gestor: " . $stmt_insert_area->error);
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Gestor atualizado com sucesso']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$stmt_delete_areas->close();
$stmt_insert_area->close();
$conn->close();
