<?php function output_nutritionist_patients(array $patients) { ?>
<link rel="stylesheet" href="../css/nutrition.css">
<div class="patients-dashboard">
    <h1 class="patients-dashboard-title">Os Meus Pacientes</h1>
    
    <?php if (empty($patients)): ?>
        <div class="alert alert-info">Ainda não tens pacientes associados. Confirma as tuas consultas no teu perfil.</div>
    <?php else: ?>
        <div class="patients-grid">
            <?php foreach ($patients as $p): 
                $photo_src = !empty($p['photo']) 
                             ? "../imgs/" . htmlspecialchars($p['photo']) 
                             : "https://ui-avatars.com/api/?name=" . urlencode($p['name']) . "&background=2ea043&color=fff";
            ?>
            <div class="patient-card">
                <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="patient-photo" onerror="this.src='https://ui-avatars.com/api/?name=P&background=2ea043&color=fff'">
                <h3 class="patient-name"><?= htmlspecialchars($p['name']) ?></h3>
                <p class="patient-email"><?= htmlspecialchars($p['email']) ?></p>
                <a href="edit_nutrition_plan.php?member_id=<?= $p['id'] ?>" class="btn-manage">Gerir Plano</a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php } ?>
