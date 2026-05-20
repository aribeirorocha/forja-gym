<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/nutrition.php'); // Vamos criar este a seguir
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/nutrition.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não

// Se o utilizador submeter um formulário (marcação ou mensagem)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'schedule_appointment') {
        $nutri_id = intval($_POST['nutritionist_id']);
        $date = $_POST['date'];
        scheduleAppointment($db, $user['id'], $nutri_id, $date);
        header('Location: nutrition.php?success=appointment');
        exit;
    } 
    elseif ($action === 'send_message') {
        $nutri_id = intval($_POST['nutritionist_id']);
        $content = trim($_POST['message']);
        if (!empty($content)) {
            sendMessage($db, $user['id'], $nutri_id, $content);
        }
    }
}

// Buscar dados comuns
$nutritionists = getNutritionists($db);
$plan = $user ? getNutritionPlan($db, $user['id']) : null;

output_header();
// Lógica de visualização baseada no estado de login
if ($user) {
    // Membro logado vê tudo
    output_member_nutrition_view($plan, $nutritionists);
} else {
    // Visitante vê apenas a equipa
    output_public_nutritionists($nutritionists);
}
?> <script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleButtons = document.querySelectorAll('.btn-toggle-booking');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const nutriId = this.getAttribute('data-id');
            const panel = document.getElementById('booking-' + nutriId);
            
            // Alterna a classe 'hidden' no painel
            panel.classList.toggle('hidden');
            
            // Muda o texto do botão consoante o estado
            if (panel.classList.contains('hidden')) {
                this.textContent = 'Marcar Consulta';
                this.style.backgroundColor = 'transparent';
                this.style.color = '#1a1a1a';
            } else {
                this.textContent = 'Fechar perfil';
                this.style.backgroundColor = '#1a1a1a'; // Fica preto quando ativo
                this.style.color = '#fff';
            }
        });
    });
});
</script>

<?php 
output_footer();
?>