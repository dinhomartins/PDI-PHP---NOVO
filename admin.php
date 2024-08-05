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
    <title>Gerenciamento de Funcionários - PDI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script> 
    <style>
        .custom-button {
            width: 200px; /* largura fixa */
            height: 50px; /* altura fixa */
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px; /* cantos arredondados */
            margin-bottom: 10px; /* margem inferior para espaçamento */
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center flex justify-between">
        <h1 class="text-2xl">Gerenciamento de Funcionários - PDI</h1>
        <button id="logout-btn" class="bg-red-500 text-white p-2 rounded">Deslogar</button>
    </header>
    <main class="p-4 w-full max-w-6xl flex lg:flex-row flex-col">
        <section class="bg-white p-8 shadow lg:w-2/3 w-full mr-4 flex-col lg:flex-row">
            <h2 class="text-xl mb-4">Admin Dashboard</h2>
            <div class="mb-4 flex flex-wrap gap-4">
                <button id="open-register-page-btn" class="custom-button bg-blue-500 text-white">Cadastrar Funcionário</button>
                <button id="manage-employees-btn" class="custom-button bg-yellow-500 text-white">Gerenciar Funcionários</button>
                <button id="open-register-manager-page-btn" class="custom-button bg-purple-500 text-white">Cadastrar Gestor</button>
                <button id="manage-managers-btn" class="custom-button bg-orange-500 text-white">Gerenciar Gestores</button>
                <button id="manage-areas-btn" class="custom-button bg-green-500 text-white">Gerenciar Áreas de Atuação</button>
                <button id="manage-positions-btn" class="custom-button bg-purple-500 text-white">Gerenciar Cargos</button>
            </div>
        </section>
        <aside class="bg-white p-8 shadow lg:w-1/3 mt-2 lg:mt-0">
            <h3 class="text-lg mb-2">Últimos Funcionários Cadastrados</h3>
            <p id="total-employees" class="mb-4"></p>
            <div id="employees-by-position" class="mb-4"></div>
            <ul id="recent-employees" class="list">
                <!-- Lista de funcionários será inserida aqui -->
            </ul>
        </aside>
    </main>

    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('open-register-page-btn').addEventListener('click', function() {
            window.location.href = 'register_employee.php';
        });

        document.getElementById('open-register-manager-page-btn').addEventListener('click', function() {
            window.location.href = 'register_manager.php';
        });

        document.getElementById('manage-employees-btn').addEventListener('click', function() {
            window.location.href = 'manage_employees.php';
        });

        document.getElementById('manage-managers-btn').addEventListener('click', function() {
            window.location.href = 'manage_managers.php';
        });

        document.getElementById('manage-areas-btn').addEventListener('click', function() {
            window.location.href = 'manage_areas.php';
        });

        document.getElementById('manage-positions-btn').addEventListener('click', function() {
            window.location.href = 'manage_positions.php';
        });

        function loadRecentEmployees() {
            fetch('api/get_recent_employees.php')
            .then(response => response.json())
            .then(data => {
                const recentEmployeesList = document.getElementById('recent-employees');
                const totalEmployeesElement = document.getElementById('total-employees');
                const employeesByPositionElement = document.getElementById('employees-by-position');

                recentEmployeesList.innerHTML = '';
                if (data.error) {
                    recentEmployeesList.innerHTML = `<li>Erro ao carregar a lista: ${data.error}</li>`;
                } else {
                    // Exibir o número total de funcionários
                    totalEmployeesElement.textContent = `Total de funcionários: ${data.total_employees}`;

                    // Exibir o número de funcionários por cargo
                    employeesByPositionElement.innerHTML = '<h4 class="text-lg mb-2">Funcionários por Cargo:</h4>';
                    data.employees_by_position.forEach(position => {
                        employeesByPositionElement.innerHTML += `<p>${position.position}: ${position.count}</p>`;
                    });

                    // Exibir os 10 funcionários mais recentes
                    if (data.recent_employees.length > 0) {
                        data.recent_employees.forEach(employee => {
                            const li = document.createElement('li');
                            li.classList.add('employee-item', 'py-2', 'border-b');
                            li.innerHTML = `
                                ${employee.name} - ${employee.email}
                                <button class="ml-4 text-blue-500 hover:underline" onclick="viewEmployeeDetails(${employee.id})">Ver Detalhes</button>
                                <button class="ml-4 text-red-500 hover:underline" onclick="removeEmployee(${employee.id})">Remover</button>
                            `;
                            recentEmployeesList.appendChild(li);
                        });
                    } else {
                        recentEmployeesList.innerHTML = '<li>Nenhum funcionário cadastrado</li>';
                    }
                }
            })
            .catch(error => console.error('Erro ao carregar a lista de funcionários:', error));
        }

        function viewEmployeeDetails(id) {
            window.location.href = `employee_details.php?id=${id}`;
        }

        function removeEmployee(id) {
            if (confirm('Tem certeza de que deseja remover este funcionário?')) {
                fetch(`api/remove_employee.php?id=${id}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        loadRecentEmployees();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => console.error('Erro ao remover o funcionário:', error));
            }
        }

        // Carrega os funcionários recentes quando a página é carregada
        document.addEventListener('DOMContentLoaded', loadRecentEmployees);
    </script>
</body>
</html>
