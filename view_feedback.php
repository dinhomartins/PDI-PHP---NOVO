<?php
include 'api/db.php';

$id = $_GET['id'];

// Obtém o feedback pelo ID
$sql = "SELECT feedback, DATE_FORMAT(created_at, '%d/%m/%Y') as feedback_date FROM feedback WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $feedback = $result->fetch_assoc();
    $feedback_date = $feedback['feedback_date'];
} else {
    $feedback = null;
}

// Obtém todos os feedbacks da mesma data
$sql_all_feedbacks = "SELECT id, feedback FROM feedback WHERE DATE(created_at) = (SELECT DATE(created_at) FROM feedback WHERE id = $id)";
$result_all_feedbacks = $conn->query($sql_all_feedbacks);

$all_feedbacks = [];
if ($result_all_feedbacks->num_rows > 0) {
    while ($row = $result_all_feedbacks->fetch_assoc()) {
        $all_feedbacks[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Completo</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script> 
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Feedback Completo</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <?php if ($feedback): ?>
                <h2 class="text-xl mb-4">Feedback: <?= htmlspecialchars($feedback_date, ENT_QUOTES, 'UTF-8') ?></h2>
                <?php foreach ($all_feedbacks as $fb): ?>
                    <div class="mb-4">
                        <textarea id="feedback-content-<?= $fb['id'] ?>" class="border p-4 mb-2 w-full" readonly><?= htmlspecialchars($fb['feedback'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        <button class="edit-feedback-btn bg-blue-500 text-white p-2 mt-2" data-feedback-id="<?= $fb['id'] ?>">Editar</button>
                        <button class="save-feedback-btn bg-green-500 text-white p-2 mt-2" data-feedback-id="<?= $fb['id'] ?>" style="display:none;">Salvar</button>
                    </div>
                <?php endforeach; ?>
                <button onclick="window.history.back()" class="bg-gray-500 text-white p-2 mt-4">Voltar</button>
            <?php else: ?>
                <p>Feedback não encontrado.</p>
                <button onclick="window.history.back()" class="bg-gray-500 text-white p-2 mt-4">Voltar</button>
            <?php endif; ?>
        </section>
    </main>

    <?php if ($feedback): ?>
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
    <?php endif; ?>
</body>
</html>
