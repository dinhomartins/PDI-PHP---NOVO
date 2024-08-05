// api/delete_item.php
<?php
include 'db.php';

$id = $_POST['id'];

$sql = "DELETE FROM items WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Item excluído com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
