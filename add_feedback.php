<?php
include 'api/db.php';

$employee_id = $_GET['employee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbacks = $_POST['feedback'];

    foreach ($feedbacks as $feedback) {
        $feedback = $conn->real_escape_string($feedback);
        $sql = "INSERT INTO feedback (employee_id, feedback) VALUES ('$employee_id', '$feedback')";
        $conn->query($sql);
    }

    header("Location: employee_details.php?id=$employee_id");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>  
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Adicionar Feedback</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <form method="POST">
                <div id="feedback-section">
                    <div class="feedback-field">
                        <textarea name="feedback[]" class="border p-2 mb-4 w-full" placeholder="Insira o feedback" required></textarea>
                    </div>
                </div>
                <button type="button" id="add-feedback-field" class="bg-green-500 text-white p-2">Adicionar Novo Campo</button>
                <button type="submit" class="bg-blue-500 text-white p-2 mt-2">Salvar Feedback</button>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('add-feedback-field').addEventListener('click', function() {
            const feedbackSection = document.querySelector('#feedback-section');
            const feedbackField = document.querySelector('.feedback-field');
            const newFeedbackField = feedbackField.cloneNode(true);
            newFeedbackField.querySelector('textarea').value = '';
            feedbackSection.appendChild(newFeedbackField);
        });
    </script>
</body>
</html>
