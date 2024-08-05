// api/authenticate.php
<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if ($password === $user['password']) { // Comparando a senha em texto simples
        $_SESSION['user_role'] = $user['role'];
        if ($user['role'] == 'administrador') {
            header('Location: ../admin.php');
        } elseif ($user['role'] == 'gestor') {
            header('Location: ../gestor.php');
        } else {
            header('Location: ../usuario.php');
        }
        exit(); // Certifique-se de sair após o redirecionamento
    } else {
        echo "Senha incorreta";
    }
} else {
    echo "Usuário não encontrado";
}

$conn->close();
?>
