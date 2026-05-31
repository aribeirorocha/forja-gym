const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

setupModal('trigger-add-equipment', 'add-equipment-modal', 'close-add-equipment');
setupFileLabel('eq-photo-input', 'eq-photo-label');
setupModal('trigger-add-trainer', 'add-trainer-modal', 'close-add-trainer');
setupModal('trigger-add-trainer-admin', 'add-trainer-modal', 'close-add-trainer');
setupFileLabel('trainer-photo-input', 'trainer-photo-label');
setupModal('trigger-add-nutritionist-admin', 'add-nutritionist-modal', 'close-add-nutritionist');
setupModal('trigger-add-nutritionist', 'add-nutritionist-modal', 'close-add-nutritionist');
setupFileLabel('nu-photo-input', 'nu-photo-label');

setupDeleteButtons('.admin-delete-equipment', {
    action: 'delete_equipment',
    message: 'Remover este equipamento permanentemente?',
    error: 'Erro ao remover equipamento.',
});

setupDeleteButtons('.admin-delete-class', {
    action: 'delete_class',
    message: 'Remover esta aula e todas as suas sessões permanentemente?',
    error: 'Erro ao remover aula.',
});

setupDeleteButtons('.delete-user-btn', {
    action: 'delete_user',
    message: 'Tem a certeza que quer remover este utilizador?',
    error: 'Não foi possível remover o utilizador.',
    idDataset: 'userId'
});

setupDeleteButtons('.admin-delete-trainer', {
    action: 'delete_user',
    message: 'Remover este trainer permanentemente?',
    error: 'Erro ao remover trainer.'
});

setupDeleteButtons('.admin-delete-nutritionist', {
    action: 'delete_user',
    message: 'Remover este nutricionista permanentemente?',
    error: 'Erro ao remover nutricionista.'
});


function setupModal(triggerId, modalId, closeId) {
    const trigger = document.getElementById(triggerId);
    const modal = document.getElementById(modalId);
    const closeBtn = document.getElementById(closeId);

    if (!trigger || !modal || !closeBtn) return;

    trigger.addEventListener('click', () => modal.classList.remove('hidden'));
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.add('hidden');
    });
}

function setupFileLabel(inputId, labelId) {
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);

    if (!input || !label) return;

    input.addEventListener('change', function () {
        label.textContent = this.files[0]
            ? '✓ ' + this.files[0].name
            : '📷 Escolher foto';
    });
}

function setupDeleteButtons(selector, options) {
    document.querySelectorAll(selector).forEach(button => {
        button.addEventListener('click', function () {
            if (!confirm(options.message)) return;

            const id = this.dataset[options.idDataset ?? 'id'];
            const element = this.closest('tr') ?? this.closest('article');

            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:
                    'csrf=' + encodeURIComponent(csrfToken) + '&action=' + encodeURIComponent(options.action) +
                    '&id=' + encodeURIComponent(id)
            }).then(response => {
                if (response.ok) {
                    element?.remove();
                } else {
                    alert(options.error);
                }
            });
        });
    });
}


const tabs = document.querySelectorAll('.admin-tab');
const panels = document.querySelectorAll('.admin-tab-content');

const urlParams = new URLSearchParams(window.location.search);
const activeTab = urlParams.get('tab') || location.hash.replace('#', '') || 'users';

tabs.forEach(tab => {
    tab.classList.toggle('active', tab.dataset.tab === activeTab);
});

panels.forEach(panel => {
    panel.classList.toggle('active', panel.id === 'tab-' + activeTab);
});

tabs.forEach(tab => {
    tab.addEventListener('click', function () {
        tabs.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

document.querySelectorAll('[id^="trigger-reviews-"]').forEach(btn => {
    const entityId = btn.id.replace('trigger-reviews-', '');
    setupModal(btn.id, 'modal-reviews-' + entityId, 'close-modal-reviews-' + entityId);
});