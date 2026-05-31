<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/nutrition.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

// Bloqueia visitantes sem login
if (!$user) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==========================================
// PROCESSAR MARCAÇÃO
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'schedule_appointment') {
    $booking_date = trim($_POST['booking_date'] ?? '');
    $booking_time = trim($_POST['booking_time'] ?? '');
    $date = $booking_date . ' ' . $booking_time . ':00';

    // Verifica limites do plano para Nutrição
    $membership = $user['membership'] ?? 'basico';
    if ($membership === 'basico') {
        header("Location: nutritionist_profile.php?id=$id&error=nutrition_not_included");
        exit;
    }
    if ($membership === 'normal') {
        $monthly_count = getMonthlyNutritionCount($db, $user['id']);
        if ($monthly_count >= 1) {
            header("Location: nutritionist_profile.php?id=$id&error=nutrition_limit_reached");
            exit;
        }
    }

    scheduleAppointment($db, $user['id'], $id, $date);
    header("Location: nutritionist_profile.php?id=$id&success=appointment");
    exit;
}

// ==========================================
// BUSCAR DADOS DO NUTRICIONISTA
// ==========================================
$stmt = $db->prepare('
    SELECT u.name, u.photo, u.email, np.specialization, np.license_number
    FROM user u
    JOIN nutritionist_profile np ON u.id = np.id_user
    WHERE u.id = ?
');
$stmt->execute([$id]);
$nutriInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nutriInfo) {
    header('Location: nutrition.php');
    exit;
}

// O calendário antigo foi removido para usar o mesmo formato dos trainers

$photo_src = !empty($nutriInfo['photo']) ? "../imgs/" . htmlspecialchars($nutriInfo['photo']) : "../imgs/default-avatar.png";

output_header();
?>

<link rel="stylesheet" href="../css/trainer_profile.css">
<link rel="stylesheet" href="../css/nutrition.css">

<main class="profile-page-wrapper">
    <article class="profile-login-card" style="max-width: 600px;"> 
        <?php if (isset($_GET['success']) && $_GET['success'] === 'appointment'): ?>
            <div class="success-banner" style="margin-top: 0;">
                Obrigado! A tua consulta foi marcada com sucesso.
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'nutrition_not_included'): ?>
            <div class="form-error" style="margin-bottom: 20px; background: #fff0f0; border: 1px solid #ffcdd2; color: #d32f2f; text-align: center;">O plano Básico não inclui consultas de nutrição. Faz upgrade ao teu plano no Perfil!</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'nutrition_limit_reached'): ?>
            <div class="form-error" style="margin-bottom: 20px; background: #fff0f0; border: 1px solid #ffcdd2; color: #d32f2f; text-align: center;">Atingiste o limite mensal (1 consulta) para o plano Normal. Faz upgrade para VIP se precisares de acompanhamento extra!</div>
        <?php endif; ?>

        <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($nutriInfo['name']) ?>" class="profile-big-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
        
        <h1 class="profile-name"><?= htmlspecialchars($nutriInfo['name']) ?></h1>
        <p class="profile-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutriInfo['specialization'])) ?></p>

        <div class="profile-details-list" style="margin-bottom: 20px;">
            <div class="profile-detail-item">
                <strong>Cédula Profissional</strong>
                <span><?= htmlspecialchars($nutriInfo['license_number']) ?></span>
            </div>
            <div class="profile-detail-item">
                <strong>Contacto</strong>
                <span><?= htmlspecialchars($nutriInfo['email']) ?></span>
            </div>
        </div>

        <section class="booking-section">
            <h4 class="review-box-title" style="margin-bottom: 15px;">Marcar Consulta de Nutrição</h4>

            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Envia a tua preferência de horário e o nutricionista confirmará o teu pedido no perfil.</p>

            <form class="booking-form" method="post" action="nutritionist_profile.php?id=<?= $id ?>" style="margin-bottom: 20px;">
                <input type="hidden" name="action" value="schedule_appointment">

                <div class="booking-row">
                    <input type="date" name="booking_date" class="booking-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    <input type="time" name="booking_time" class="booking-input" required>
                </div>

                <button type="submit" class="btn-review-submit">Pedir Marcação</button>
            </form>
        </section>

        <a href="nutrition.php" class="btn-text btn-back-team">Voltar à Equipa</a>
        
    </article>
</main>

<?php
output_footer();
?>