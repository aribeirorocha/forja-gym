<?php function output_nutritionist_patients(array $patients) { ?>
<link rel="stylesheet" href="../css/nutrition.css">
<style>
    .patients-dashboard {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .patients-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }
    .patient-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 12px;
        padding: 24px 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .patient-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .patient-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 16px;
        border: 2px solid var(--primary-color, #2ea043);
    }
    .patient-name {
        font-size: 1.15rem;
        color: var(--text-main, #1d1d1f);
        margin-bottom: 6px;
        font-weight: 600;
    }
    .patient-email {
        font-size: 0.9rem;
        color: var(--text-muted, #86868b);
        margin-bottom: 20px;
    }
    .btn-manage {
        display: inline-block;
        width: 100%;
        background-color: var(--primary-color, #2ea043);
        color: #fff;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-manage:hover {
        opacity: 0.9;
    }
</style>

<div class="patients-dashboard">
    <h1 style="color: var(--text-main); margin-bottom: 24px; font-weight: 700;">Os Meus Pacientes</h1>
    
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
