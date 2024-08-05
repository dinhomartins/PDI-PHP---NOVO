<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id === 0) {
        throw new Exception("ID inválido");
    }

    // Recuperar os detalhes do funcionário junto com o nome do cargo
    $sql = "
        SELECT users.id, users.name, users.email, users.area, positions.name as position
        FROM users
        LEFT JOIN positions ON users.position = positions.id
        WHERE users.id = $id
    ";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erro na consulta: " . $conn->error);
    }

    if ($result->num_rows === 0) {
        throw new Exception("Funcionário não encontrado");
    }

    $employee = $result->fetch_assoc();

    echo json_encode(['employee' => $employee]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
