<?php
include 'db.php';

$query = $_GET['q'];

$sql = "SELECT id, name, email FROM users WHERE name LIKE '%$query%' OR email LIKE '%$query%'";
$result = $conn->query($sql);

$employees = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

echo json_encode($employees);
?>
