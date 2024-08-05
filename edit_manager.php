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

// Obtém todas as áreas de atuação para exibir no formulário
$sql_areas = "SELECT id, name FROM areas";
$result_areas = $conn->query($sql_areas);

$areas = [];
while ($row = $result_areas->fetch_assoc()) {
    $areas[] = $row;
}

// Obtém as áreas de atuação do gestor
$sql_manager_areas = "SELECT area_id FROM manager_areas WHERE manager_id = ?";
$stmt_manager_areas = $conn->prepare($sql_manager_areas);
$stmt_manager_areas->bind_param('i', $id);
$stmt_manager_areas->execute();
$result_manager_areas = $stmt_manager_areas->get_result();

$manager_areas = [];
while ($row = $result_manager_areas->fetch_assoc()) {
    $manager_areas[] = $row['area_id'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Gestor - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Editar Gestor</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <form id="edit-manager-form" class="mb-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($manager['id'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" id="name" class="border p-2 w-full" value="<?= htmlspecialchars($manager['name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="border p-2 w-full" value="<?= htmlspecialchars($manager['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Áreas de Atuação</label>
                    <?php foreach ($areas as $area): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="areas[]" value="<?= htmlspecialchars($area['id'], ENT_QUOTES, 'UTF-8') ?>" <?= in_array($area['id'], $manager_areas) ? 'checked' : '' ?> class="mr-2">
                            <label><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 w-full">Salvar</button>
            </form>
            <button id="back-btn" class="bg-gray-500 text-white p-2 w-full mt-2">Voltar</button>
        </section>
    </main>

    <script>
        document.getElementById('edit-manager-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            fetch('api/update_manager.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = `manager_details.php?id=<?= htmlspecialchars($manager['id'], ENT_QUOTES, 'UTF-8') ?>`;
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao tentar atualizar o gestor. Verifique o console para mais detalhes.');
            });
        });

        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = `manager_details.php?id=<?= htmlspecialchars($manager['id'], ENT_QUOTES, 'UTF-8') ?>`;
        });
    </script>
</body>
</html>
