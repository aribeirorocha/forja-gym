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


output_header();


if ($user) {

    output_member_equipment($equipments);
} else {

    output_public_equipment($equipments);
}
?> 
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. LÓGICA DO BOTÃO "MAIS INFORMAÇÕES" (Que já tinhas)
    const toggleButtons = document.querySelectorAll('.btn-toggle-eq');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eqId = this.getAttribute('data-id');
            const panel = document.getElementById('eq-info-' + eqId);
            panel.classList.toggle('hidden');
            if (panel.classList.contains('hidden')) {
                this.textContent = 'Mais informações';
            } else {
                this.textContent = 'Ocultar informações';
            }
        });
    });

    // 2. NOVA LÓGICA DE FILTRAGEM
    const filterLocation = document.getElementById('filter-location');
    const filterMuscle = document.getElementById('filter-muscle');
    const filterStatus = document.getElementById('filter-status');
    const allCards = document.querySelectorAll('.equipment-card');

    function applyFilters() {
        const valLocation = filterLocation.value;
        const valMuscle = filterMuscle.value;
        const valStatus = filterStatus.value;

        allCards.forEach(card => {
            const cardLocation = card.getAttribute('data-location');
            const cardMuscle = card.getAttribute('data-muscle');
            const cardStatus = card.getAttribute('data-status');

            // Verifica se o cartão passa nos 3 filtros
            const matchLocation = (valLocation === 'all' || cardLocation === valLocation);
            const matchMuscle = (valMuscle === 'all' || cardMuscle === valMuscle);
            const matchStatus = (valStatus === 'all' || cardStatus === valStatus);

            // Se passar nos 3, mostra. Se falhar em algum, esconde.
            if (matchLocation && matchMuscle && matchStatus) {
                card.style.display = 'flex'; // Mantém o flex-direction column a funcionar
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Ouvir alterações em qualquer um dos filtros
    filterLocation.addEventListener('change', applyFilters);
    filterMuscle.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
});
</script>

<?php 

output_footer();
?>