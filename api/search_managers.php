<?php
include 'db.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? $_GET['q'] : '';

if (empty($query)) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id, name, email FROM managers WHERE name LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($sql);
$searchQuery = "%$query%";
$stmt->bind_param('ss', $searchQuery, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

$managers = [];
while ($row = $result->fetch_assoc()) {
    $managers[] = $row;
}

echo json_encode($managers);

$stmt->close();
$conn->close();
