<?php
include 'api/db.php';

$id = $_GET['id'];

// Obtém os detalhes do funcionário
$sql = "
    SELECT users.*, positions.name as position_name
    FROM users
    LEFT JOIN positions ON users.position = positions.id
    WHERE users.id = $id
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
} else {
    $employee = null;
}

// Obtém a lista de cargos
$sql_positions = "SELECT id, name FROM positions";
$result_positions = $conn->query($sql_positions);

$positions = [];
if ($result_positions->num_rows > 0) {
    while ($row = $result_positions->fetch_assoc()) {
        $positions[] = $row;
    }
}

// Obtém a lista de áreas de atuação
$sql_areas = "SELECT id, name FROM areas";
$result_areas = $conn->query($sql_areas);

$areas = [];
if ($result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Editar Funcionário</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <?php if ($employee): ?>
                <form id="edit-employee-form">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8') ?>" class="border p-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($employee['email'], ENT_QUOTES, 'UTF-8') ?>" class="border p-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="position" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select name="position" id="position" class="border p-2 w-full">
                            <?php foreach ($positions as $position): ?>
                                <option value="<?= $position['id'] ?>" <?= $position['id'] == $employee['position'] ? 'selected' : '' ?>><?= htmlspecialchars($position['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="area" class="block text-sm font-medium text-gray-700">Área de Atuação</label>
                        <select name="area" id="area" class="border p-2 w-full">
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?>" <?= $employee['area'] === $area['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-4">
                        <button type="submit" class="bg-blue-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Salvar</button>
                        <button type="button" id="back-btn" class="bg-gray-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Voltar</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Funcionário não encontrado.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php if ($employee): ?>
    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'manage_employees.php';
        });

        document.getElementById('edit-employee-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            fetch(`api/update_employee.php?id=<?= htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') ?>`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Dados do funcionário atualizados com sucesso.');
                    window.location.href = 'manage_employees.php';
                } else {
                    alert('Erro ao atualizar os dados do funcionário: ' + data.message);
                }
            })
            .catch(error => console.error('Erro ao atualizar os dados do funcionário:', error));
        });
    </script>
    <?php endif; ?>
</body>
</html>
