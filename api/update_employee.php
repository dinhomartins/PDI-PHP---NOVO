<?php
include 'db.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$position = isset($_POST['position']) ? (int)$_POST['position'] : 0;
$area = isset($_POST['area']) ? $_POST['area'] : '';

if ($id === 0 || empty($name) || empty($email) || $position === 0 || empty($area)) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit();
}

$sql = "UPDATE users SET name = ?, email = ?, position = ?, area = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssisi', $name, $email, $position, $area, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Dados do funcionário atualizados com sucesso']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar os dados do funcionário: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
