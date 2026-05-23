<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

// Se não tiver login, bloqueia o acesso à página detalhada
if (!$user) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Reutilizamos a tua função para procurar a máquina certa
$equipments = getEquipments($db);
$equipmentInfo = null;

foreach ($equipments as $eq) {
    if ($eq['id'] === $id) {
        $equipmentInfo = $eq;
        break;
    }
}

// Se o ID for inválido, volta para a lista
if (!$equipmentInfo) {
    header('Location: equipment.php');
    exit;
}

$photo_src = !empty($equipmentInfo['photo']) ? "../imgs/" . htmlspecialchars($equipmentInfo['photo']) : "../imgs/default-equipment.png";
$muscle = $equipmentInfo['muscle_name'] ?? 'Corpo Inteiro';
$location = $equipmentInfo['location'] ?? 'Não especificada';
$status_text = $equipmentInfo['status'] ?? 'Disponível';
$status_class = ($status_text === 'Disponível') ? 'status-available' : 'status-maintenance';

output_header();
?>

<link rel="stylesheet" href="../css/trainer_profile.css">
<link rel="stylesheet" href="../css/equipment_info.css">

<main class="profile-page-wrapper">
    <article class="profile-login-card">
        
        <div class="eq-badge-container">
            <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($status_text) ?></span>
        </div>

        <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($equipmentInfo['name']) ?>" class="eq-profile-photo" onerror="this.onerror=null; this.src='../imgs/default-equipment.png';">
        
        <h1 class="profile-name"><?= htmlspecialchars($equipmentInfo['name']) ?></h1>
        
        <div class="profile-details-list">
            <div class="profile-detail-item">
                <strong>Localização</strong>
                <span><?= htmlspecialchars($location) ?></span>
            </div>
            <div class="profile-detail-item">
                <strong>Foco Muscular</strong>
                <span><?= htmlspecialchars($muscle) ?></span>
            </div>
        </div>

        <?php if(!empty($equipmentInfo['video_url'])): ?>
            <div class="video-wrapper">
                <iframe src="<?= htmlspecialchars($equipmentInfo['video_url']) ?>" allowfullscreen></iframe>
            </div>
        <?php endif; ?>

        <a href="equipment.php" class="btn-text btn-back-team">Voltar aos Equipamentos</a>
        
    </article>
</main>

<?php
output_footer();
?>