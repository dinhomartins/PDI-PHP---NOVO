<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'gestor') {
    header("Location: login.php");
    exit();
}

include 'api/db.php';

$gestor_id = $_SESSION['user_id'];

// Obtém as áreas de atuação do gestor
$sql_areas = "SELECT area_id FROM manager_areas WHERE manager_id = ?";
$stmt_areas = $conn->prepare($sql_areas);
$stmt_areas->bind_param('i', $gestor_id);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

$areas = [];
while ($row = $result_areas->fetch_assoc()) {
    $areas[] = $row['area_id'];
}

$stmt_areas->close();

if (count($areas) > 0) {
    // Converte o array de áreas para uma string para a consulta SQL
    $areas_placeholder = implode(',', array_fill(0, count($areas), '?'));

    // Prepara a consulta para obter os funcionários da mesma área do gestor
    $sql = "SELECT users.id, users.name, users.email, users.position, areas.name as area_name 
            FROM users 
            JOIN areas ON users.area = areas.id 
            WHERE users.area IN ($areas_placeholder)";
    $stmt = $conn->prepare($sql);

    // Adiciona os parâmetros dinamicamente
    $types = str_repeat('i', count($areas));
    $stmt->bind_param($types, ...$areas);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    $stmt->close();
} else {
    $employees = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Equipe - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-blue-500 text-white p-4 w-full flex justify-between items-center">
        <h1 class="text-2xl">Minha Equipe - PDI Sistema</h1>
        <button id="logout-btn" class="bg-red-500 text-white p-2 rounded">Deslogar</button>
    </header>
    <main class="p-4 w-full max-w-6xl flex flex-col items-center">
        <section class="bg-white p-8 shadow w-full">
            <h2 class="text-xl mb-4">Funcionários da Minha Área</h2>
            <ul class="list">
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $employee): ?>
                        <li class="employee-item py-2 border-b">
                            Nome: <?= htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8') ?><br>
                            Email: <?= htmlspecialchars($employee['email'], ENT_QUOTES, 'UTF-8') ?><br>
                            Cargo: <?= htmlspecialchars($employee['position'], ENT_QUOTES, 'UTF-8') ?><br>
                            Área: <?= htmlspecialchars($employee['area_name'], ENT_QUOTES, 'UTF-8') ?><br>
                            <button class="bg-blue-500 text-white p-2 rounded mt-2" onclick="viewEmployeeDetails(<?= $employee['id'] ?>)">Ver Detalhes</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Nenhum funcionário encontrado na sua área.</li>
                <?php endif; ?>
            </ul>
            <button id="back-btn" class="bg-gray-500 text-white p-2 rounded mt-4">Voltar</button>
        </section>
    </main>

    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'gestor.php';
        });

        function viewEmployeeDetails(id) {
            window.location.href = `employee_details.php?id=${id}`;
        }
    </script>
</body>
</html>
