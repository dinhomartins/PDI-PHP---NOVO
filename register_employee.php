<!-- 
    <script src="https://cdn.tailwindcss.com"></script> 
-->

<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script> 
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-blue-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Cadastrar Funcionário</h1>
        <button id="back-btn" class="bg-red-500 text-white p-2 rounded">Voltar</button>
    </header>
    <main class="p-4 w-full max-w-2xl">
        <div class="bg-white p-8 rounded shadow-md">
            <form id="add-employee-form" class="mb-4">
                <input type="text" name="name" id="name" class="border p-2 mb-4 w-full" placeholder="Nome" required>
                <input type="email" name="email" id="email" class="border p-2 mb-4 w-full" placeholder="Email" required>
                <select name="position" id="position" class="border p-2 mb-4 w-full" required>
                    <option value="">Selecione o cargo</option>
                    <!-- Cargos serão carregados aqui -->
                </select>
                <select name="area" id="area" class="border p-2 mb-4 w-full" required>
                    <option value="">Selecione a área de atuação</option>
                    <!-- Áreas de atuação serão carregadas aqui -->
                </select>
                <input type="hidden" name="role" value="usuario">
                <button type="submit" class="bg-blue-500 text-white p-2 w-full rounded">Cadastrar</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });

        document.getElementById('add-employee-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            fetch('api/add_employee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    document.getElementById('add-employee-form').reset();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao tentar cadastrar o usuário. Verifique o console para mais detalhes.');
            });
        });

        function loadPositions() {
            fetch('api/get_positions.php')
            .then(response => response.json())
            .then(data => {
                const positionSelect = document.getElementById('position');
                positionSelect.innerHTML = '<option value="">Selecione o cargo</option>';
                data.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.textContent = position.name;
                    positionSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Erro ao carregar os cargos:', error));
        }

        function loadAreas() {
            fetch('api/get_areas.php')
            .then(response => response.json())
            .then(data => {
                const areaSelect = document.getElementById('area');
                areaSelect.innerHTML = '<option value="">Selecione a área de atuação</option>';
                data.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.name;
                    areaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Erro ao carregar as áreas de atuação:', error));
        }

        // Carrega as áreas de atuação e os cargos quando a página é carregada
        document.addEventListener('DOMContentLoaded', function() {
            loadPositions();
            loadAreas();
        });
    </script>
</body>
</html>
