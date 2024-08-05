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
    <title>Gerenciar Cargos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Gerenciar Cargos</h1>
        <button id="back-btn" class="bg-red-500 text-white p-2 rounded">Voltar</button>
    </header>
    <main class="p-4 w-full max-w-2xl">
        <div class="bg-white p-8 rounded shadow-md">
            <h2 class="text-xl mb-4">Adicionar Novo Cargo</h2>
            <form id="add-position-form" class="mb-4">
                <input type="text" name="position_name" id="position_name" class="border p-2 mb-4 w-full" placeholder="Nome do Cargo" required>
                <button type="submit" class="bg-green-500 text-white p-2 w-full rounded">Salvar</button>
            </form>
            <h2 class="text-xl mb-4">Cargos Cadastrados</h2>
            <ul id="positions-list" class="list">
                <!-- Lista de cargos será inserida aqui -->
            </ul>
        </div>
    </main>

    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });

        document.getElementById('add-position-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            fetch('api/add_position.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    loadPositions(); // Atualiza a lista de cargos
                } else {
                    alert('Erro: ' + data.message);
                }
                document.getElementById('position_name').value = '';
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao tentar adicionar o cargo. Verifique o console para mais detalhes.');
            });
        });

        function loadPositions() {
            fetch('api/get_positions.php')
            .then(response => response.json())
            .then(data => {
                const positionsList = document.getElementById('positions-list');
                positionsList.innerHTML = '';
                data.forEach(position => {
                    const li = document.createElement('li');
                    li.classList.add('flex', 'justify-between', 'items-center', 'mb-2');
                    li.innerHTML = `
                        <span>${position.name}</span>
                        
                        <div>
                            <button class="edit-btn bg-blue-500 text-white p-1 rounded mr-2" data-id="${position.id}">Editar</button>
                            <button class="delete-btn bg-red-500 text-white p-1 rounded" data-id="${position.id}">Excluir</button>
                            
                        </div>
                    `;
                    positionsList.appendChild(li);
                });
                attachEventListeners();
            })
            .catch(error => console.error('Erro ao carregar os cargos:', error));
        }

        function attachEventListeners() {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const positionId = this.getAttribute('data-id');
                    const newName = prompt('Digite o novo nome do cargo:');
                    if (newName) {
                        fetch(`api/edit_position.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: positionId, name: newName })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                loadPositions();
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Erro ao editar o cargo:', error));
                    }
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const positionId = this.getAttribute('data-id');
                    if (confirm('Tem certeza de que deseja excluir este cargo?')) {
                        fetch(`api/delete_position.php?id=${positionId}`, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                loadPositions();
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Erro ao excluir o cargo:', error));
                    }
                });
            });
        }

        // Carrega os cargos quando a página é carregada
        document.addEventListener('DOMContentLoaded', loadPositions);
    </script>
</body>
</html>
