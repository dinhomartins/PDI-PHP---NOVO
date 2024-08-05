<!-- 
<script src="https://cdn.tailwindcss.com"></script>  
-->
<?php
include 'api/db.php';

$employee_id = $_GET['employee_id'];
$datetime = $_GET['datetime'];

// Obtém todos os feedbacks do funcionário na data e hora especificadas
$sql = "SELECT id, feedback, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as feedback_datetime FROM feedback WHERE employee_id = $employee_id AND DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') = '$datetime'";
$result = $conn->query($sql);

$feedbacks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Feedback</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0"></script>
    <script src="https://cdn.tailwindcss.com"></script>  
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Detalhes do Feedback</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <?php if (!empty($feedbacks)): ?>
                <h2 class="text-xl mb-4">Feedbacks de <?= htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8') ?></h2>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="mb-4">
                        <textarea id="feedback-content-<?= $feedback['id'] ?>" class="border p-4 mb-2 w-full" readonly><?= htmlspecialchars($feedback['feedback'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        <button class="edit-feedback-btn bg-blue-500 text-white p-2 mt-2" data-feedback-id="<?= $feedback['id'] ?>">Editar</button>
                        <button class="save-feedback-btn bg-green-500 text-white p-2 mt-2" data-feedback-id="<?= $feedback['id'] ?>" style="display:none;">Salvar</button>
                    </div>
                <?php endforeach; ?>
                <button onclick="window.history.back()" class="bg-gray-500 text-white p-2 mt-4">Voltar</button>
            <?php else: ?>
                <p>Nenhum feedback encontrado.</p>
                <button onclick="window.history.back()" class="bg-gray-500 text-white p-2 mt-4">Voltar</button>
            <?php endif; ?>
        </section>
    </main>

    <script>
        document.querySelectorAll('.edit-feedback-btn').forEach(button => {
            button.addEventListener('click', function() {
                const feedbackId = this.getAttribute('data-feedback-id');
                const textarea = document.getElementById('feedback-content-' + feedbackId);
                textarea.readOnly = false;
                textarea.focus();
                this.style.display = 'none';
                document.querySelector('.save-feedback-btn[data-feedback-id="' + feedbackId + '"]').style.display = 'inline-block';
            });
        });

        document.querySelectorAll('.save-feedback-btn').forEach(button => {
            button.addEventListener('click', function() {
                const feedbackId = this.getAttribute('data-feedback-id');
                const textarea = document.getElementById('feedback-content-' + feedbackId);
                const updatedFeedback = textarea.value;

                fetch('api/update_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        feedback_id: feedbackId,
                        feedback: updatedFeedback
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Feedback atualizado com sucesso');
                        textarea.readOnly = true;
                        document.querySelector('.edit-feedback-btn[data-feedback-id="' + feedbackId + '"]').style.display = 'inline-block';
                        this.style.display = 'none';
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao tentar atualizar feedback. Verifique o console para mais detalhes.');
                });
            });
        });
    </script>
</body>
</html>

