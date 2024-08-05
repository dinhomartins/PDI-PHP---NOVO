<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'gestor') {
    header("Location: login.php");
    exit();
}

include 'api/db.php';

$gestor_id = $_SESSION['user_id'];

// Obtém o nome do gestor logado
$sql_gestor = "SELECT name FROM managers WHERE id = ?";
$stmt_gestor = $conn->prepare($sql_gestor);
$stmt_gestor->bind_param('i', $gestor_id);
$stmt_gestor->execute();
$result_gestor = $stmt_gestor->get_result();
$gestor_name = $result_gestor->fetch_assoc()['name'];

$stmt_gestor->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-blue-500 text-white p-4 w-full flex justify-between items-center">
        <h1 class="text-2xl">Área do Gestor - PDI Sistema</h1>
        <button id="logout-btn" class="bg-red-500 text-white p-2 rounded">Deslogar</button>
    </header>
    <main class="p-4 w-full max-w-6xl flex flex-col items-center">
        <section class="bg-white p-8 shadow w-full">
            <h2 class="text-xl mb-4">Bem-vindo, <?= htmlspecialchars($gestor_name, ENT_QUOTES, 'UTF-8') ?></h2>
            <h3 class="text-lg mb-4">Minha Equipe</h3>
            <button id="view-team-btn" class="bg-blue-500 text-white p-2 rounded mt-2">Ver Minha Equipe</button>
        </section>
    </main>

    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('view-team-btn').addEventListener('click', function() {
            window.location.href = 'gestor_team.php';
        });
    </script>
</body>
</html>
