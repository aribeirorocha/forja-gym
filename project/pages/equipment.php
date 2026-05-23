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

            const matchLocation = (valLocation === 'all' || cardLocation === valLocation);
            const matchMuscle = (valMuscle === 'all' || cardMuscle === valMuscle);
            const matchStatus = (valStatus === 'all' || cardStatus === valStatus);

            if (matchLocation && matchMuscle && matchStatus) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    filterLocation.addEventListener('change', applyFilters);
    filterMuscle.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
});
</script>

<?php 

output_footer();
?>