// js/scripts.js
document.addEventListener('DOMContentLoaded', function() {
    const adminContent = document.getElementById('admin-content');
    if (adminContent) {
        loadItems();

        const form = document.getElementById('add-item-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const itemName = document.getElementById('item-name').value;
            fetch('api/add_item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `name=${itemName}`
            })
            .then(response => response.text())
            .then(() => {
                document.getElementById('item-name').value = '';
                loadItems();
            });
        });
    }
});

function loadItems() {
    fetch('api/get_data.php')
        .then(response => response.json())
        .then(data => {
            const itemsList = document.getElementById('items-list');
            itemsList.innerHTML = '';
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'p-4 border-b border-gray-200 flex justify-between items-center';
                div.innerHTML = `
                    <span>${item.name}</span>
                    <div>
                        <button class="bg-yellow-500 text-white p-2 mr-2" onclick="editItem(${item.id}, '${item.name}')">Editar</button>
                        <button class="bg-red-500 text-white p-2" onclick="deleteItem(${item.id})">Excluir</button>
                    </div>
                `;
                itemsList.appendChild(div);
            });
        });
}

function editItem(id, name) {
    const newName = prompt('Novo nome:', name);
    if (newName) {
        fetch('api/edit_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&name=${newName}`
        })
        .then(response => response.text())
        .then(() => {
            loadItems();
        });
    }
}

function deleteItem(id) {
    if (confirm('Tem certeza que deseja excluir este item?')) {
        fetch('api/delete_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(response => response.text())
        .then(() => {
            loadItems();
        });
    }
}
