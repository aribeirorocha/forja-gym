<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/equipment.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$equipments = getEquipments($db);

$is_admin = $user && ($user['role'] ?? '') === 'admin';

output_header();

if ($user) {
    output_member_equipment($equipments, $is_admin);
} else {
    output_public_equipment($equipments, $is_admin);
}
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterLocation = document.getElementById('filter-location');
    const filterMuscle = document.getElementById('filter-muscle');
    const filterStatus = document.getElementById('filter-status');
    const allCards = document.querySelectorAll('.equipment-card');

    function applyFilters() {
        const valLocation = filterLocation ? filterLocation.value : 'all';
        const valMuscle   = filterMuscle   ? filterMuscle.value   : 'all';
        const valStatus   = filterStatus   ? filterStatus.value   : 'all';

        allCards.forEach(card => {
            const matchLocation = (valLocation === 'all' || card.getAttribute('data-location') === valLocation);
            const matchMuscle   = (valMuscle   === 'all' || card.getAttribute('data-muscle')   === valMuscle);
            const matchStatus   = (valStatus   === 'all' || card.getAttribute('data-status')   === valStatus);

            card.style.display = (matchLocation && matchMuscle && matchStatus) ? 'flex' : 'none';
        });
    }

    if (filterLocation) filterLocation.addEventListener('change', applyFilters);
    if (filterMuscle)   filterMuscle.addEventListener('change', applyFilters);
    if (filterStatus)   filterStatus.addEventListener('change', applyFilters);

    // Botões "Remover" equipamento (admin)
    document.querySelectorAll('.admin-delete-equipment').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Remover este equipamento permanentemente?')) return;
            const id   = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_equipment&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover equipamento.'); });
        });
    });
});
</script>

<?php
output_footer();
?>