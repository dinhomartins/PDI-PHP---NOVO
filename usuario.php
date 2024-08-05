<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Conteúdo da página do usuário
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuário - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
</head>
<body>
    <header class="bg-blue-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Usuário - PDI Sistema</h1>
    </header>
    <main class="p-4">
        <p>Bem-vindo, usuário!</p>
    </main>
</body>
</html>
