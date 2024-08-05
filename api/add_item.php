// api/add_item.php
<?php
include 'db.php';

$name = $_POST['name'];

$sql = "INSERT INTO items (name) VALUES ('$name')";
if ($conn->query($sql) === TRUE) {
    echo "Item adicionado com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
