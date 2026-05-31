<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


$db   = getDatabaseConnection();
$user = UserAuth::currentUser();

if (!$user) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$equipments    = getEquipments($db);
$equipmentInfo = null;

foreach ($equipments as $eq) {
    if ($eq['id'] === $id) {
        $equipmentInfo = $eq;
        break;
    }
}

if (!$equipmentInfo) {
    header('Location: equipment.php');
    exit;
}

$photo_src    = !empty($equipmentInfo['photo']) ? "../imgs/" . htmlspecialchars($equipmentInfo['photo']) : "../imgs/default-equipment.png";
$muscle       = $equipmentInfo['muscle_name'] ?? 'Corpo Inteiro';
$location     = $equipmentInfo['location']    ?? 'Não especificada';
$status_text  = $equipmentInfo['status']      ?? 'Disponível';
$status_class = ($status_text === 'Disponível') ? 'status-available' : 'status-maintenance';

$is_trainer = in_array($user['role'], ['trainer', 'admin']);

output_header();
?>

<link rel="stylesheet" href="../css/trainer_profile.css">
<link rel="stylesheet" href="../css/equipment_info.css">

<main class="profile-page-wrapper">
    <article class="profile-login-card">

        <div class="eq-badge-container">
            <span class="status-badge <?= $status_class ?>" id="status-badge">
                <?= htmlspecialchars($status_text) ?>
            </span>
            <?php if ($is_trainer): ?>
            <button
                id="btn-toggle-status"
                class="btn-toggle-eq <?= ($status_text === 'Disponível') ? 'btn-danger' : 'btn-success' ?>"
                data-id="<?= $id ?>"
                data-current="<?= htmlspecialchars($status_text) ?>">
                <?= ($status_text === 'Disponível') ? 'Marcar como Em Manutenção' : 'Marcar como Disponível' ?>
            </button>
            <p class="eq-toggle-feedback" id="toggle-feedback" aria-live="polite"></p>
            <?php endif; ?>
        </div>

        <img src="<?= $photo_src ?>"
             alt="<?= htmlspecialchars($equipmentInfo['name']) ?>"
             class="eq-profile-photo"
             onerror="this.onerror=null; this.src='../imgs/default-equipment.png';">

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

        <?php if (!empty($equipmentInfo['video_url'])): ?>
            <div class="video-wrapper">
                <iframe src="<?= htmlspecialchars($equipmentInfo['video_url']) ?>" allowfullscreen></iframe>
            </div>
        <?php endif; ?>

        <a href="equipment.php" class="btn-text btn-back-team">Voltar aos Equipamentos</a>

    </article>
</main>

<?php if ($is_trainer): ?>
<script>
document.getElementById('btn-toggle-status').addEventListener('click', async function () {
    const btn        = this;
    const id         = btn.dataset.id;
    const current    = btn.dataset.current;
    const newStatus  = (current === 'Disponível') ? 'Manutenção' : 'Disponível';
    const feedback   = document.getElementById('toggle-feedback');

    btn.disabled = true;
    feedback.textContent = 'A atualizar…';
    feedback.style.color = 'var(--text-muted, #888)';

    try {
        const body = new URLSearchParams({ id, status: newStatus });
        const res  = await fetch('update_equipment_status.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body.toString()
        });

        const data = await res.json();

        if (!data.success) throw new Error(data.message || 'Erro desconhecido');
        // Atualizar badge
        const badge = document.getElementById('status-badge');
        badge.textContent = newStatus;
        badge.className   = 'status-badge ' + (newStatus === 'Disponível' ? 'status-available' : 'status-maintenance');

        // Atualizar botão
        btn.dataset.current = newStatus;
        btn.textContent     = (newStatus === 'Disponível') ? 'Marcar como Em Manutenção' : 'Marcar como Disponível';
        btn.className       = 'btn-toggle-eq ' + (newStatus === 'Disponível' ? 'btn-danger' : 'btn-success');

        feedback.textContent = '✓ Estado atualizado com sucesso!';
        feedback.style.color = 'var(--green, #2e7d32)';

    } catch (err) {
        feedback.textContent = '✗ ' + (err.message || 'Não foi possível atualizar.');
        feedback.style.color = 'var(--red, #c62828)';
        console.error(err);
    } finally {
        btn.disabled = false;
        setTimeout(() => { feedback.textContent = ''; }, 4000);
    }
});
</script>
<?php endif; ?>

<?php
output_footer();
?>