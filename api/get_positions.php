<?php
include 'db.php';

$sql = "SELECT * FROM positions";
$result = $conn->query($sql);

$positions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
}

echo json_encode($positions);
?>
