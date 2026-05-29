<?php

require_once('../templates/common.php');
require_once('../templates/trainers.php');
require_once('../database/trainer.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$trainers = Trainer::getTrainers($db, 99);

$is_admin = $user && ($user['role'] ?? '') === 'admin';

output_header();
output_trainers_hero();
output_trainers($trainers, $is_admin);
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterGender = document.getElementById('filter-gender');
    const sortTrainers = document.getElementById('sort-trainers');
    const grid = document.getElementById('trainers-grid-container');

    // Exclui o card "+" da ordenação/filtragem
    let cards = Array.from(grid.querySelectorAll('.trainer-card'));

    function updateGrid() {
        const valGender = filterGender.value;
        const valSort   = sortTrainers.value;

        // 1. FILTRAR POR GÉNERO
        cards.forEach(card => {
            const cardGender = card.getAttribute('data-gender');
            card.style.display = (valGender === 'all' || cardGender === valGender) ? 'block' : 'none';
        });

        // 2. ORDENAR
        if (valSort !== 'default') {
            cards.sort((a, b) => {
                const ageA  = parseInt(a.getAttribute('data-age'));
                const ageB  = parseInt(b.getAttribute('data-age'));
                const yearA = parseInt(a.getAttribute('data-year'));
                const yearB = parseInt(b.getAttribute('data-year'));
                switch (valSort) {
                    case 'age-asc':   return ageA  - ageB;
                    case 'age-desc':  return ageB  - ageA;
                    case 'time-desc': return yearA - yearB;
                    case 'time-asc':  return yearB - yearA;
                    default:          return 0;
                }
            });
        }

        // 3. APLICAR VISUALMENTE (o card "+" fica sempre no fim por estar fora do array)
        cards.forEach(card => grid.insertBefore(card, grid.querySelector('#trigger-add-trainer')));
    }

    filterGender.addEventListener('change', updateGrid);
    sortTrainers.addEventListener('change', updateGrid);
});
</script>

<?php
output_footer();
?>