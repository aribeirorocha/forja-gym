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

document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('trigger-add-nutritionist');
    const modal = document.getElementById('add-nutritionist-modal');
    const closeBtn = document.getElementById('close-add-nutritionist');
    if (trigger && modal) {
        trigger.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
    }
    const inp = document.getElementById('nu-photo-input');
    const lbl = document.getElementById('nu-photo-label');
    if (inp) inp.addEventListener('change', function () { lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto'; });

    document.querySelectorAll('.admin-delete-nutritionist').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Remover este nutricionista permanentemente?')) return;
            const id = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_user&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover nutricionista.'); });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.admin-delete-class').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Remover esta aula permanentemente?')) return;
            const id = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_class&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover aula.'); });
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('trigger-add-equipment');
    const modal = document.getElementById('add-equipment-modal');
    const closeBtn = document.getElementById('close-add-equipment');
    if (trigger && modal) {
        trigger.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
    }
    const inp = document.getElementById('eq-photo-input');
    const lbl = document.getElementById('eq-photo-label');
    if (inp) inp.addEventListener('change', function () { lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto'; });

    // Botões Remover equipamento
    document.querySelectorAll('.admin-delete-equipment').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Remover este equipamento permanentemente?')) return;
            const id = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_equipment&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover equipamento.'); });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Abrir/fechar modal
    const trigger = document.getElementById('trigger-add-trainer');
    const modal = document.getElementById('add-trainer-modal');
    const closeBtn = document.getElementById('close-add-trainer');
    if (trigger && modal) {
        trigger.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
    }

    // Preview nome da foto
    const inp = document.getElementById('trainer-photo-input');
    const lbl = document.getElementById('trainer-photo-label');
    if (inp) inp.addEventListener('change', function () {
        lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto';
    });

    // Remover trainer via fetch
    document.querySelectorAll('.admin-delete-trainer').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Remover este trainer permanentemente?')) return;
            const id = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_user&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover trainer.'); });
        });
    });
});