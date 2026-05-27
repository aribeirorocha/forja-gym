document.querySelectorAll('.delete-class-btn').forEach(button => {
    button.addEventListener('click', function () {
        const classId = this.dataset.classId;

        if (!confirm('Tem a certeza que quer remover esta aula?')) {
            return;
        }

        fetch('create_class.php', {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },
            body:
                'action=remove_class' + 
                '&id=' + encodeURIComponent(classId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao remover a aula');
            }

            this.closest('article').remove();
        })
        .catch(error => {
            alert('Não foi possível remover a aula.');
            console.error(error);
        });
    });
});