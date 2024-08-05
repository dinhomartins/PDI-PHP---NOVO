<?php
session_start();
include 'api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET password='$hashed_password', password_set=1 WHERE id=$user_id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Erro ao definir a senha. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        <h2 class="text-2xl mb-4 text-center">Definir Senha</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="set_password.php">
            <div class="mb-4">
                <label for="password" class="block text-sm font-bold mb-2">Nova Senha:</label>
                <input type="password" id="password" name="password" class="border p-2 w-full" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white p-2 w-full rounded">Definir Senha</button>
        </form>
    </div>
</body>
</html>
