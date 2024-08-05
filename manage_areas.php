<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Áreas de Atuação</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Gerenciar Áreas de Atuação</h1>
        <button id="back-btn" class="bg-red-500 text-white p-2 rounded">Voltar</button>
    </header>
    <main class="p-4 w-full max-w-2xl">
        <div class="bg-white p-8 rounded shadow-md">
            <h2 class="text-xl mb-4">Adicionar Nova Área de Atuação</h2>
            <form id="add-area-form" class="mb-4">
                <input type="text" name="area_name" id="area_name" class="border p-2 mb-4 w-full" placeholder="Área de Atuação" required>
                <button type="submit" class="bg-green-500 text-white p-2 w-full rounded">Salvar</button>
            </form>
            <h2 class="text-xl mb-4">Áreas de Atuação Cadastradas</h2>
            <ul id="areas-list" class="list">
                <!-- Lista de áreas de atuação será inserida aqui -->
            </ul>
        </div>
    </main>

    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });

        document.getElementById('add-area-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            fetch('api/add_area.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    loadAreas(); // Atualiza a lista de áreas de atuação
                } else {
                    alert('Erro: ' + data.message);
                }
                document.getElementById('area_name').value = '';
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao tentar adicionar a área de atuação. Verifique o console para mais detalhes.');
            });
        });

        function loadAreas() {
            fetch('api/get_areas.php')
            .then(response => response.json())
            .then(data => {
                const areasList = document.getElementById('areas-list');
                areasList.innerHTML = '';
                data.forEach(area => {
                    const li = document.createElement('li');
                    li.classList.add('flex', 'justify-between', 'items-center', 'mb-2');
                    li.innerHTML = `
                        <span>${area.name}</span>
                        <div>
                            <button class="edit-btn bg-blue-500 text-white p-1 rounded mr-2" data-id="${area.id}">Editar</button>
                            <button class="delete-btn bg-red-500 text-white p-1 rounded" data-id="${area.id}">Excluir</button>
                        </div>
                    `;
                    areasList.appendChild(li);
                });
                attachEventListeners();
            })
            .catch(error => console.error('Erro ao carregar as áreas de atuação:', error));
        }

        function attachEventListeners() {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const areaId = this.getAttribute('data-id');
                    const newName = prompt('Digite o novo nome da área de atuação:');
                    if (newName) {
                        fetch(`api/edit_area.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: areaId, name: newName })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                loadAreas();
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Erro ao editar a área de atuação:', error));
                    }
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const areaId = this.getAttribute('data-id');
                    if (confirm('Tem certeza de que deseja excluir esta área de atuação?')) {
                        fetch(`api/delete_area.php?id=${areaId}`, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                loadAreas();
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Erro ao excluir a área de atuação:', error));
                    }
                });
            });
        }

        // Carrega as áreas de atuação quando a página é carregada
        document.addEventListener('DOMContentLoaded', loadAreas);
    </script>
</body>
</html>