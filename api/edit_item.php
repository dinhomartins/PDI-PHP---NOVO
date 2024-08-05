// api/edit_item.php
<?php
include 'db.php';

$id = $_POST['id'];
$name = $_POST['name'];

$sql = "UPDATE items SET name='$name' WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Item atualizado com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
