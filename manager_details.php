<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'api/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido");
}

// Obtém os detalhes do gestor
$sql = "SELECT * FROM managers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $manager = $result->fetch_assoc();
} else {
    die("Gestor não encontrado");
}

// Obtém as áreas de atuação do gestor
$sql_areas = "SELECT a.name FROM manager_areas ma JOIN areas a ON ma.area_id = a.id WHERE ma.manager_id = ?";
$stmt_areas = $conn->prepare($sql_areas);
$stmt_areas->bind_param('i', $id);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

$areas = [];
while ($row = $result_areas->fetch_assoc()) {
    $areas[] = $row['name'];
}

$stmt_areas->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Gestor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Detalhes do Gestor</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <h2 class="text-xl mb-4"><?= htmlspecialchars($manager['name'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($manager['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Áreas de Atuação:</strong></p>
            <ul>
                <?php foreach ($areas as $area): ?>
                    <li><?= htmlspecialchars($area, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-4">
                <button id="edit-manager-btn" class="bg-blue-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Editar Dados</button>
                <button id="remove-manager-btn" class="bg-red-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Remover Gestor</button>
                <button id="back-btn" class="bg-gray-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Voltar</button>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('edit-manager-btn').addEventListener('click', function() {
            window.location.href = `edit_manager.php?id=<?= htmlspecialchars($manager['id'], ENT_QUOTES, 'UTF-8') ?>`;
        });

        document.getElementById('remove-manager-btn').addEventListener('click', function() {
            if (confirm('Tem certeza de que deseja remover este gestor?')) {
                fetch(`api/remove_manager.php?id=<?= htmlspecialchars($manager['id'], ENT_QUOTES, 'UTF-8') ?>`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = 'manage_managers.php';
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => console.error('Erro ao remover o gestor:', error));
            }
        });

        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'manage_managers.php';
        });
    </script>
</body>
</html>
