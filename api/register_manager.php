<?php
include 'db.php';

header('Content-Type: application/json');

$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$areas = isset($_POST['areas']) ? $_POST['areas'] : [];

if (empty($name) || empty($email) || empty($areas)) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit();
}

try {
    // Iniciar transação
    $conn->begin_transaction();

    // Inserir gestor
    $sql = "INSERT INTO managers (name, email) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $name, $email);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao inserir gestor: " . $stmt->error);
    }
    $manager_id = $stmt->insert_id;

    // Inserir áreas de atuação do gestor
    $sql_areas = "INSERT INTO manager_areas (manager_id, area_id) VALUES (?, ?)";
    $stmt_areas = $conn->prepare($sql_areas);
    foreach ($areas as $area_id) {
        $stmt_areas->bind_param('ii', $manager_id, $area_id);
        if (!$stmt_areas->execute()) {
            throw new Exception("Erro ao inserir áreas do gestor: " . $stmt_areas->error);
        }
    }

    // Confirmar transação
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Gestor cadastrado com sucesso']);
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$stmt_areas->close();
$conn->close();
?>
