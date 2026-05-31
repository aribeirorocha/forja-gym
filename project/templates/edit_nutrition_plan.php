<?php function output_edit_nutrition_plan(array $patient, $plan, array $meals, string $success_msg, string $error_msg) { 
    $photo_src = !empty($patient['photo']) 
                 ? "../imgs/" . htmlspecialchars($patient['photo']) 
                 : "https://ui-avatars.com/api/?name=" . urlencode($patient['name']) . "&background=2ea043&color=fff";
                 
    $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    $meal_types = ['Pequeno-Almoço', 'Almoço', 'Lanche', 'Jantar'];
    
    $meals_by_day = [];
    foreach ($days as $d) {
        $meals_by_day[$d] = [];
    }
    foreach ($meals as $m) {
        if (isset($meals_by_day[$m['day_of_week']])) {
            $meals_by_day[$m['day_of_week']][] = $m;
        }
    }
?>
<link rel="stylesheet" href="../css/nutrition.css">
<style>
    .plan-dashboard {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .patient-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        background: var(--bg-card, #fff);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid var(--border-color, #e0e0e0);
    }
    .patient-header img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }
    .patient-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--text-main);
    }
    .patient-header p {
        margin: 4px 0 0;
        color: var(--text-muted);
    }
    .plan-card {
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .plan-card-title {
        font-size: 1.25rem;
        color: var(--text-main);
        margin-bottom: 20px;
        font-weight: 700;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px;
    }
    .day-section {
        margin-bottom: 30px;
    }
    .day-title {
        background: var(--bg-elevated, #f9f9f9);
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 16px;
        border-left: 4px solid var(--primary-color, #2ea043);
    }
    .meal-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .meal-item {
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e0e0e0);
        padding: 16px;
        border-radius: 8px;
        position: relative;
    }
    .meal-item h4 {
        margin: 0 0 8px 0;
        color: var(--primary-color, #2ea043);
        font-size: 1.05rem;
    }
    .meal-macros {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 10px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .meal-macros span {
        background: var(--bg-elevated, #f0f0f5);
        padding: 2px 6px;
        border-radius: 4px;
    }
    .btn-delete-meal {
        position: absolute;
        top: 12px;
        right: 12px;
        background: none;
        border: none;
        color: #ff3b30;
        cursor: pointer;
        font-size: 1.2rem;
    }
</style>

<div class="plan-dashboard">
    <a href="nutritionist_patients.php" class="btn-text" style="display:inline-block; margin-bottom: 20px; color: var(--text-muted); text-decoration: none;">&larr; Voltar à lista de pacientes</a>
    
    <div class="patient-header">
        <img src="<?= $photo_src ?>" alt="Paciente">
        <div>
            <h2><?= htmlspecialchars($patient['name']) ?></h2>
            <p><?= htmlspecialchars($patient['email']) ?></p>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div class="plan-card">
        <h3 class="plan-card-title">Detalhes do Plano</h3>
        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>" method="post">
            <input type="hidden" name="action" value="save_plan">
            <input type="hidden" name="plan_id" value="<?= $plan ? $plan['id'] : 0 ?>">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Objetivo</label>
                    <input type="text" name="goal" value="<?= $plan ? htmlspecialchars($plan['goal']) : '' ?>" placeholder="Ex: Emagrecimento, Ganho de Massa Muscular" required>
                </div>
                <div class="form-group">
                    <label>Data de Início</label>
                    <input type="date" name="start_date" value="<?= $plan ? htmlspecialchars($plan['start_date']) : date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Data de Fim (Opcional)</label>
                    <input type="date" name="end_date" value="<?= $plan && $plan['end_date'] ? htmlspecialchars($plan['end_date']) : '' ?>">
                </div>
                <div class="form-group full-width">
                    <label>Notas e Recomendações Adicionais</label>
                    <textarea name="notes" rows="4" style="width:100%; border:1px solid var(--border-color); border-radius:8px; padding:12px; font-family:inherit;"><?= $plan ? htmlspecialchars($plan['notes']) : '' ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn-save" style="background-color: var(--primary-color, #2ea043);">Guardar Detalhes do Plano</button>
        </form>
    </div>

    <?php if ($plan): ?>
    <div class="plan-card">
        <h3 class="plan-card-title">Adicionar Refeição</h3>
        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>" method="post">
            <input type="hidden" name="action" value="add_meal">
            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Dia da Semana</label>
                    <select name="day_of_week" required style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border-color);">
                        <?php foreach($days as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipo de Refeição</label>
                    <select name="meal_type" required style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border-color);">
                        <?php foreach($meal_types as $mt): ?>
                            <option value="<?= $mt ?>"><?= $mt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Descrição da Refeição (Alimentos, Porções)</label>
                    <textarea name="description" rows="3" required style="width:100%; border:1px solid var(--border-color); border-radius:8px; padding:12px; font-family:inherit;"></textarea>
                </div>
                <div class="form-group">
                    <label>Calorias (kcal)</label>
                    <input type="number" name="calories" min="0">
                </div>
                <div class="form-group">
                    <label>Proteína (g)</label>
                    <input type="number" name="protein_g" min="0">
                </div>
                <div class="form-group">
                    <label>Carboidratos (g)</label>
                    <input type="number" name="carbs_g" min="0">
                </div>
                <div class="form-group">
                    <label>Gorduras (g)</label>
                    <input type="number" name="fat_g" min="0">
                </div>
            </div>
            <button type="submit" class="btn-save" style="background-color: var(--primary-color, #2ea043);">Adicionar Refeição</button>
        </form>
    </div>

    <div class="plan-card">
        <h3 class="plan-card-title">Refeições do Plano</h3>
        <?php foreach ($days as $day): ?>
            <?php if (!empty($meals_by_day[$day])): ?>
            <div class="day-section">
                <div class="day-title"><?= $day ?></div>
                <div class="meal-list">
                    <?php foreach ($meals_by_day[$day] as $meal): ?>
                    <div class="meal-item">
                        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>" method="post">
                            <input type="hidden" name="action" value="delete_meal">
                            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                            <input type="hidden" name="meal_id" value="<?= $meal['id'] ?>">
                            <button type="submit" class="btn-delete-meal" title="Remover Refeição">&times;</button>
                        </form>
                        <h4><?= htmlspecialchars($meal['meal_type']) ?></h4>
                        <p style="margin:0; color:var(--text-main); font-size:0.95rem; white-space:pre-wrap;"><?= htmlspecialchars($meal['description']) ?></p>
                        <div class="meal-macros">
                            <?php if ($meal['calories']): ?><span><?= $meal['calories'] ?> kcal</span><?php endif; ?>
                            <?php if ($meal['protein_g']): ?><span>P: <?= $meal['protein_g'] ?>g</span><?php endif; ?>
                            <?php if ($meal['carbs_g']): ?><span>C: <?= $meal['carbs_g'] ?>g</span><?php endif; ?>
                            <?php if ($meal['fat_g']): ?><span>G: <?= $meal['fat_g'] ?>g</span><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (empty($meals)): ?>
            <p class="weight-hint">Ainda não foram adicionadas refeições a este plano.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
<?php } ?>
