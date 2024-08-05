<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'api/db.php';

// Obtém os 10 gestores mais recentes
$sql_recent = "SELECT id, name, email FROM managers ORDER BY id DESC LIMIT 10";
$result_recent = $conn->query($sql_recent);

if (!$result_recent) {
    die("Erro na consulta: " . $conn->error);
}

$recent_managers = [];
while ($row = $result_recent->fetch_assoc()) {
    $recent_managers[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Gestores - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script> 
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center flex justify-between">
        <h1 class="text-2xl">Gerenciar Gestores</h1>
        <button id="logout-btn" class="bg-red-500 text-white p-2 rounded">Deslogar</button>
    </header>
    <main class="p-4 w-full max-w-6xl flex lg:flex-row flex-col">
        <section class="bg-white p-8 shadow lg:w-2/3 w-full mr-4 flex-col lg:flex-row">
            <h2 class="text-xl mb-4">Gestores Cadastrados</h2>
            <input type="text" id="search-manager" class="border p-2 w-full mb-4" placeholder="Pesquisar Gestor">
            <ul id="search-results" class="mb-4 list hidden">
                <!-- Resultados da pesquisa serão inseridos aqui -->
            </ul>
            <ul id="recent-managers" class="list">
                <!-- Lista de gestores será inserida aqui -->
                <?php foreach ($recent_managers as $manager): ?>
                    <li class="manager-item py-2 border-b">
                        Nome: <?= htmlspecialchars($manager['name'], ENT_QUOTES, 'UTF-8') ?><br>
                        Email: <?= htmlspecialchars($manager['email'], ENT_QUOTES, 'UTF-8') ?><br>
                        <button class="bg-blue-500 text-white p-2 rounded" onclick="viewManagerDetails(<?= $manager['id'] ?>)">Ver Detalhes</button>
                        <button class="bg-red-500 text-white p-2 rounded" onclick="removeManager(<?= $manager['id'] ?>)">Remover</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button id="back-btn" class="bg-gray-500 text-white p-2 rounded mt-4">Voltar</button>
        </section>
    </main>

    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('search-manager').addEventListener('input', function(event) {
            const query = event.target.value;

            if (query.length >= 3) {
                fetch(`api/search_managers.php?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    const searchResults = document.getElementById('search-results');
                    searchResults.innerHTML = '';
                    searchResults.classList.remove('hidden');

                    if (data.length > 0) {
                        data.forEach(manager => {
                            const li = document.createElement('li');
                            li.classList.add('manager-item');
                            li.textContent = manager.name;
                            li.addEventListener('click', function() {
                                window.location.href = `manager_details.php?id=${manager.id}`;
                            });
                            searchResults.appendChild(li);
                        });
                    } else {
                        searchResults.innerHTML = '<li>Nenhum gestor encontrado</li>';
                    }
                })
                .catch(error => console.error('Erro ao pesquisar gestores:', error));
            } else {
                document.getElementById('search-results').classList.add('hidden');
            }
        });

        function viewManagerDetails(id) {
            window.location.href = `manager_details.php?id=${id}`;
        }

        function removeManager(id) {
            if (confirm('Tem certeza de que deseja remover este gestor?')) {
                fetch(`api/remove_manager.php?id=${id}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => console.error('Erro ao remover o gestor:', error));
            }
        }

        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });
    </script>
</body>
</html>
