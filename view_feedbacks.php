<!-- 
<script src="https://cdn.tailwindcss.com"></script>  
-->
<?php
include 'api/db.php';

$employee_id = $_GET['employee_id'];

// Obtém todos os feedbacks do funcionário agrupados por data e hora
$sql = "SELECT id, feedback, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as feedback_datetime FROM feedback WHERE employee_id = $employee_id ORDER BY created_at DESC";
$result = $conn->query($sql);

$feedbacks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[$row['feedback_datetime']][] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedbacks do Funcionário</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>  
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Feedbacks do Funcionário</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <?php if (!empty($feedbacks)): ?>
                <ul>
                    <?php foreach ($feedbacks as $datetime => $feedbackGroup): ?>
                        <li class="mb-4 border-b pb-2">
                            <p><?= htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="feedback-summary"><?= htmlspecialchars($feedbackGroup[0]['feedback'], ENT_QUOTES, 'UTF-8') ?>...</p>
                            <button class="view-feedback-btn bg-green-500 text-white p-1 mt-2 mr-2" data-feedback-datetime="<?= $datetime ?>">Ver Completo</button>
                            <button class="delete-feedback-btn bg-red-500 text-white p-1 mt-2" data-feedback-datetime="<?= $datetime ?>">Excluir</button>
                            <button class="back-btn bg-gray-500 text-white p-1 mt-2">Voltar</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum feedback encontrado.</p>
            <?php endif; ?>
            <button id="back-to-employee-details-btn" class="bg-gray-500 text-white p-2 mt-4">Voltar</button>
        </section>
    </main>

    <script>
        document.querySelectorAll('.view-feedback-btn').forEach(button => {
            button.addEventListener('click', function() {
                const datetime = this.getAttribute('data-feedback-datetime');
                window.location.href = `view_feedback_details.php?employee_id=<?= htmlspecialchars($employee_id, ENT_QUOTES, 'UTF-8') ?>&datetime=${datetime}`;
            });
        });

        document.querySelectorAll('.delete-feedback-btn').forEach(button => {
            button.addEventListener('click', function() {
                const datetime = this.getAttribute('data-feedback-datetime');
                if (confirm('Tem certeza de que deseja excluir todos os feedbacks desse horário?')) {
                    fetch(`api/delete_feedbacks_by_datetime.php?employee_id=<?= htmlspecialchars($employee_id, ENT_QUOTES, 'UTF-8') ?>&datetime=${datetime}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Feedbacks excluídos com sucesso');
                            window.location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao tentar excluir feedbacks. Verifique o console para mais detalhes.');
                    });
                }
            });
        });

        document.getElementById('back-to-employee-details-btn').addEventListener('click', function() {
            window.location.href = `employee_details.php?id=<?= htmlspecialchars($employee_id, ENT_QUOTES, 'UTF-8') ?>`;
        });
    </script>
</body>
</html>
