<?php
include 'api/db.php';

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
    <title>Cadastrar Gestor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-purple-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Cadastrar Gestor</h1>
    </header>
    <main class="p-4 w-full max-w-4xl">
        <section class="bg-white p-8 shadow">
            <form id="register-manager-form">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" id="name" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Áreas de Atuação</label>
                    <?php foreach ($areas as $area): ?>
                        <div>
                            <input type="checkbox" name="areas[]" id="area_<?= $area['id'] ?>" value="<?= $area['id'] ?>" class="mr-2">
                            <label for="area_<?= $area['id'] ?>"><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 mt-4">
                    <button type="submit" class="bg-purple-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Salvar</button>
                    <button type="button" id="back-btn" class="bg-gray-500 text-white p-2 w-full md:w-auto rounded lg:w-[200px]">Voltar</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });

        document.getElementById('register-manager-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            fetch('api/register_manager.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da rede');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Gestor cadastrado com sucesso.');
                    window.location.href = 'admin.php';
                } else {
                    alert('Erro ao cadastrar gestor: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao cadastrar gestor:', error);
                alert('Erro ao cadastrar gestor. Verifique o console para mais detalhes.');
            });
        });
    </script>
</body>
</html>
