<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/classes.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não

// Buscar dados comuns
$classes = GymClass::getClasses($db, 24);

// ...
output_header();
output_classes($classes);
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterTrainer = document.getElementById('filter-trainer');
    const filterType = document.getElementById('filter-type');
    const filterPeriod = document.getElementById('filter-period');
    const allCards = document.querySelectorAll('.class-card'); // Usa a classe dos teus cartões

    function applyFilters() {
        const valTrainer = filterTrainer.value;
        const valType = filterType.value;
        const valPeriod = filterPeriod.value;

        allCards.forEach(card => {
            const cardTrainer = card.getAttribute('data-trainer');
            const cardType = card.getAttribute('data-type');
            const cardPeriod = card.getAttribute('data-period');

            // Verifica se as condições do filtro são cumpridas
            const matchTrainer = (valTrainer === 'all' || cardTrainer === valTrainer);
            const matchType = (valType === 'all' || cardType === valType);
            const matchPeriod = (valPeriod === 'all' || cardPeriod === valPeriod);

            // Esconde se falhar algum dos filtros
            if (matchTrainer && matchType && matchPeriod) {
                card.style.display = ''; // Volta ao default (flex ou block)
            } else {
                card.style.display = 'none'; // Esconde
            }
        });
    }

    // Ouvir alterações em todos os select
    filterTrainer.addEventListener('change', applyFilters);
    filterType.addEventListener('change', applyFilters);
    filterPeriod.addEventListener('change', applyFilters);
});
</script>

<?php
output_footer();
?>