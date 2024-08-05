<?php
include 'api/db.php';

$id = $_GET['id'];

// Obtém os detalhes do funcionário com o nome do cargo
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Funcionário</title>
    <script src="https://cdn.tailwindcss.com"></script>  
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Detalhes do Funcionário</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <?php if ($employee): ?>
                <h2 class="text-xl mb-4"><?= htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p><strong>Email:</strong> <?= htmlspecialchars($employee['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Cargo:</strong> <?= htmlspecialchars($employee['position_name'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Área de Atuação:</strong> <?= htmlspecialchars($employee['area'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-4">
                    <button id="edit-employee-btn" class="bg-blue-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Editar Dados</button>
                    <button id="add-feedback-btn" class="bg-green-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Adicionar Feedback</button>
                    <button id="view-feedbacks-btn" class="bg-yellow-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Ver Feedbacks</button>
                    <button id="back-btn" class="bg-gray-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Voltar</button>
                </div>
            <?php else: ?>
                <p>Funcionário não encontrado.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php if ($employee): ?>
    <script>
        document.getElementById('edit-employee-btn').addEventListener('click', function() {
            window.location.href = `edit_employee.php?id=<?= htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') ?>`;
        });

        document.getElementById('add-feedback-btn').addEventListener('click', function() {
            window.location.href = `add_feedback.php?employee_id=<?= htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') ?>`;
        });

        document.getElementById('view-feedbacks-btn').addEventListener('click', function() {
            window.location.href = `view_feedbacks.php?employee_id=<?= htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') ?>`;
        });

        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = `admin.php`;
        });
    </script>
    <?php endif; ?>
</body>
</html>
