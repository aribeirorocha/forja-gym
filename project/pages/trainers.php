<?php 

require_once('../templates/common.php');
require_once('../templates/trainers.php');

require_once('../database/trainer.php');
require_once('../database/connection.php');

$db = getDatabaseConnection();

$trainers = Trainer::getTrainers($db, 99);

// ... 
output_header();
output_trainers_hero();
output_trainers($trainers);
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterGender = document.getElementById('filter-gender');
    const sortTrainers = document.getElementById('sort-trainers');
    const grid = document.getElementById('trainers-grid-container');
    
    // Converte os cartões para um Array (lista) para os podermos ordenar
    let cards = Array.from(grid.querySelectorAll('.trainer-card'));

    function updateGrid() {
        const valGender = filterGender.value;
        const valSort = sortTrainers.value;

        // 1. FILTRAR POR GÉNERO
        cards.forEach(card => {
            const cardGender = card.getAttribute('data-gender');
            if (valGender === 'all' || cardGender === valGender) {
                card.style.display = 'block'; // Mostra
            } else {
                card.style.display = 'none'; // Esconde
            }
        });

        // 2. ORDENAR OS CARTÕES
        if (valSort !== 'default') {
            cards.sort((a, b) => {
                const ageA = parseInt(a.getAttribute('data-age'));
                const ageB = parseInt(b.getAttribute('data-age'));
                const yearA = parseInt(a.getAttribute('data-year'));
                const yearB = parseInt(b.getAttribute('data-year'));

                switch(valSort) {
                    case 'age-asc':
                        return ageA - ageB; // Crescente (Mais novos)
                    case 'age-desc':
                        return ageB - ageA; // Decrescente (Mais velhos)
                    case 'time-desc':
                        // Mais tempo = Ano mais antigo (ex: 2020 vem antes de 2026)
                        return yearA - yearB; 
                    case 'time-asc':
                        // Menos tempo = Ano mais recente (ex: 2026 vem antes de 2020)
                        return yearB - yearA; 
                    default:
                        return 0;
                }
            });
        }

        // 3. APLICAR A ORDENAÇÃO VISUALMENTE
        // Ao fazermos 'appendChild', o JS move os elementos no ecrã para a nova ordem!
        cards.forEach(card => grid.appendChild(card));
    }

    // Ouve as alterações nos dropdowns
    filterGender.addEventListener('change', updateGrid);
    sortTrainers.addEventListener('change', updateGrid);
});
</script>

<?php
output_footer();
?>