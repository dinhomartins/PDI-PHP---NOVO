<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;

    // Obter funcionários com paginação
    $sql_recent = "SELECT id, name, email FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result_recent = $conn->query($sql_recent);

    if (!$result_recent) {
        throw new Exception("Erro na consulta: " . $conn->error);
    }

    $recent_employees = [];
    while ($row = $result_recent->fetch_assoc()) {
        $recent_employees[] = $row;
    }

    echo json_encode([
        'recent_employees' => $recent_employees
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
