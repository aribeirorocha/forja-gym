document.querySelectorAll('.role-select').forEach(select => {

    select.addEventListener('change', function () {

        const role = this.value;
        const userId = this.dataset.userId;

        fetch('admin.php', {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },
            body:
                'role=' + encodeURIComponent(role) +
                '&user_id=' + encodeURIComponent(userId)
        });

    });

});

document.querySelectorAll('.delete-user-btn').forEach(button => {
    button.addEventListener('click', function () {
        const userId = this.dataset.userId;

        if (!confirm('Tem a certeza que quer remover este utilizador?')) {
            return;
        }

        fetch('admin.php', {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },
            body:
                'action=delete_user' + 
                '&id=' + encodeURIComponent(userId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao remover o utilizador');
            }

            this.closest('tr').remove();
        })
        .catch(error => {
            alert('Não foi possível remover o utilizador.');
            console.error(error);
        });
    });
});

document.querySelectorAll('.equipment-card').forEach(card => {
    const button = document.createElement('button');

    button.classList.add('btn-delete');
    button.textContent = 'x';
    button.style.margin = '10px';

    card.appendChild(button);

    button.addEventListener('click', function () {
        const equipmentId = card.dataset.id;

        if (!confirm('Tem a certeza que quer remover este equipamento?')) {
            return;
        }

        fetch('admin.php', {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },
            body:
                'action=delete_equipment' + 
                '&id=' + encodeURIComponent(userId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao remover o equipamento');
            }

            this.closest('article').remove();
        })
        .catch(error => {
            alert('Não foi possível remover o equipamento.');
            console.error(error);
        });
    });
});