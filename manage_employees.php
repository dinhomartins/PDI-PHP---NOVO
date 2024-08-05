<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script> 
    <style>
        #recent-employees-container {
            height: 400px;
            overflow-y: scroll;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Gerenciar Funcionários</h1>
        <button id="back-btn" class="bg-red-500 text-white p-2 rounded">Voltar</button>
    </header>
    <main class="p-4 w-full max-w-6xl flex lg:flex-row flex-col">
        <aside class="bg-white p-8 rounded shadow lg:w-1/3 w-full">
            <h3 class="text-lg mb-2">Últimos Funcionários Cadastrados</h3>
            <div id="recent-employees-container">
                <ul id="recent-employees" class="list">
                    <!-- Lista de funcionários será inserida aqui -->
                </ul>
            </div>
        </aside>
        <section class="bg-white p-8 rounded shadow lg:w-2/3 w-full ml-4">
            <h2 class="text-xl mb-4">Pesquisar Funcionários</h2>
            <input type="text" id="search-employee" class="border p-2 w-full mb-4" placeholder="Digite pelo menos 3 letras para pesquisar...">
            <ul id="search-results" class="list hidden">
                <!-- Resultados da pesquisa serão inseridos aqui -->
            </ul>
        </section>
    </main>

    <script>
        document.getElementById('back-btn').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });

        let offset = 0;
        const limit = 10;
        let loading = false;

        function loadRecentEmployees() {
            if (loading) return;
            loading = true;
            
            fetch(`api/get_recent_employees.php?offset=${offset}&limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                const recentEmployeesList = document.getElementById('recent-employees');
                if (data.error) {
                    recentEmployeesList.innerHTML = `<li>Erro ao carregar a lista: ${data.error}</li>`;
                } else {
                    if (data.recent_employees.length > 0) {
                        data.recent_employees.forEach(employee => {
                            const li = document.createElement('li');
                            li.classList.add('employee-item', 'py-2', 'border-b', 'flex', 'flex-col');
                            li.innerHTML = `
                                <p><strong>Nome:</strong> ${employee.name}</p>
                                <p><strong>Email:</strong> ${employee.email}</p>
                                <div class="mt-2 flex space-x-2">
                                    <button class="bg-red-500 text-white p-2 rounded hover:bg-red-600" onclick="removeEmployee(${employee.id})">Remover</button>
                                    <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" onclick="viewEmployeeDetails(${employee.id})">Ver Detalhes</button>
                                </div>
                            `;
                            recentEmployeesList.appendChild(li);
                        });
                        offset += limit;
                    } else if (offset === 0) {
                        recentEmployeesList.innerHTML = '<li>Nenhum funcionário cadastrado</li>';
                    }
                }
                loading = false;
            })
            .catch(error => {
                console.error('Erro ao carregar a lista de funcionários:', error);
                loading = false;
            });
        }

        document.getElementById('recent-employees-container').addEventListener('scroll', function() {
            const { scrollTop, scrollHeight, clientHeight } = this;
            if (scrollTop + clientHeight >= scrollHeight - 5) {
                loadRecentEmployees();
            }
        });

        document.getElementById('search-employee').addEventListener('input', function(event) {
            const query = event.target.value;

            if (query.length >= 3) {
                fetch(`api/search_employees.php?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    const searchResults = document.getElementById('search-results');
                    searchResults.innerHTML = '';
                    searchResults.classList.remove('hidden');

                    if (data.length > 0) {
                        data.forEach(employee => {
                            const li = document.createElement('li');
                            li.classList.add('employee-item', 'py-2', 'border-b', 'flex', 'flex-col');
                            li.innerHTML = `
                                <p><strong>Nome:</strong> ${employee.name}</p>
                                <p><strong>Email:</strong> ${employee.email}</p>
                                <div class="mt-2 flex space-x-2">
                                    <button class="bg-red-500 text-white p-2 rounded hover:bg-red-600" onclick="removeEmployee(${employee.id})">Remover</button>
                                    <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" onclick="viewEmployeeDetails(${employee.id})">Ver Detalhes</button>
                                </div>
                            `;
                            searchResults.appendChild(li);
                        });
                    } else {
                        searchResults.innerHTML = '<li>Nenhum funcionário encontrado</li>';
                    }
                })
                .catch(error => console.error('Erro ao pesquisar funcionários:', error));
            } else {
                document.getElementById('search-results').classList.add('hidden');
            }
        });

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
                        offset = 0;
                        document.getElementById('recent-employees').innerHTML = '';
                        loadRecentEmployees();
                        document.getElementById('search-employee').dispatchEvent(new Event('input'));
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
