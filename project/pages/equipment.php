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
?> <script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleButtons = document.querySelectorAll('.btn-toggle-eq');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eqId = this.getAttribute('data-id');
            const panel = document.getElementById('eq-info-' + eqId);

            panel.classList.toggle('hidden');
            

            if (panel.classList.contains('hidden')) {
                this.textContent = 'Mais informações';
                this.style.backgroundColor = 'transparent';
                this.style.color = 'var(--black)';
            } else {
                this.textContent = 'Ocultar informações';
                this.style.backgroundColor = 'var(--black)';
                this.style.color = 'var(--white)';
            }
        });
    });
});
</script>

<?php 

output_footer();
?>