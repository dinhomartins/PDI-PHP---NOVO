<?php
include 'db.php';

$sql = "SELECT * FROM areas";
$result = $conn->query($sql);

$areas = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $areas[] = $row;
    }
}

echo json_encode($areas);
?>
