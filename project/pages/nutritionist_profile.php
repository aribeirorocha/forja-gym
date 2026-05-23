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
    $date = $_POST['date'];
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

// ==========================================
// PREPARAR DADOS DO CALENDÁRIO
// ==========================================
$slots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
$dias_semana = ['Sun'=>'Dom', 'Mon'=>'Seg', 'Tue'=>'Ter', 'Wed'=>'Qua', 'Thu'=>'Qui', 'Fri'=>'Sex', 'Sat'=>'Sáb'];
$next_days = [];
for ($i = 0; $i < 7; $i++) {
    $timestamp = strtotime("+$i days");
    $next_days[] = [
        'date_val' => date('Y-m-d', $timestamp),
        'label' => date('d/m', $timestamp),
        'day_pt' => $dias_semana[date('D', $timestamp)]
    ];
}

$photo_src = !empty($nutriInfo['photo']) ? "../imgs/" . htmlspecialchars($nutriInfo['photo']) : "../imgs/default-avatar.png";

output_header();
?>

<link rel="stylesheet" href="../css/trainer_profile.css">
<link rel="stylesheet" href="../css/nutrition.css">

<main class="profile-page-wrapper">
    <article class="profile-login-card" style="max-width: 600px;"> <?php if (isset($_GET['success']) && $_GET['success'] === 'appointment'): ?>
            <div class="success-banner" style="margin-top: 0;">
                Obrigado! A tua consulta foi marcada com sucesso.
            </div>
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

        <h4 style="font-size: 0.95rem; margin-bottom: 15px; font-weight: 700; color: var(--text-main); text-align: left;">Agendar Consulta</h4>
        
        <form method="post" action="nutritionist_profile.php?id=<?= $id ?>">
            <input type="hidden" name="action" value="schedule_appointment">
            
            <div class="calendar-wrapper">
                <div class="calendar-grid">
                    <?php foreach($next_days as $day): ?>
                        <div class="calendar-day-col">
                            <div class="calendar-header">
                                <span class="day-name"><?= $day['day_pt'] ?></span>
                                <span class="day-date"><?= $day['label'] ?></span>
                            </div>
                            <div class="calendar-slots">
                                <?php foreach($slots as $time): 
                                    $slotValue = $day['date_val'] . ' ' . $time;
                                    $slotId = 'slot_' . $id . '_' . $day['date_val'] . '_' . str_replace(':','',$time);
                                ?>
                                    <div class="time-slot">
                                        <input type="radio" name="date" value="<?= $slotValue ?>" id="<?= $slotId ?>" required>
                                        <label for="<?= $slotId ?>"><?= $time ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" class="btn-schedule" style="margin-bottom: 20px;">Confirmar Marcação</button>
        </form>

        <a href="nutrition.php" class="btn-text btn-back-team">Voltar à Equipa</a>
        
    </article>
</main>

<?php
output_footer();
?>