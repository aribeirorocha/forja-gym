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
<div class="plan-dashboard">
    <a href="nutritionist_patients.php" class="btn-text back-link">&larr; Voltar à lista de pacientes</a>
    
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
        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>
    ">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
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
                    <textarea name="notes" rows="4" class="form-textarea"><?= $plan ? htmlspecialchars($plan['notes']) : '' ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn-save-nutrition w-100">Guardar Detalhes do Plano</button>
        </form>
    </div>

    <?php if ($plan): ?>
    <div class="plan-card">
        <h3 class="plan-card-title">Adicionar Refeição</h3>
        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>
    ">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="action" value="add_meal">
            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Dia da Semana</label>
                    <select name="day_of_week" required class="form-select-lg">
                        <?php foreach($days as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipo de Refeição</label>
                    <select name="meal_type" required class="form-select-lg">
                        <?php foreach($meal_types as $mt): ?>
                            <option value="<?= $mt ?>"><?= $mt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Descrição da Refeição (Alimentos, Porções)</label>
                    <textarea name="description" rows="3" required class="form-textarea"></textarea>
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
            <button type="submit" class="btn-save-nutrition w-100">Adicionar Refeição</button>
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
                        <form action="edit_nutrition_plan.php?member_id=<?= $patient['id'] ?>
    ">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                            <input type="hidden" name="action" value="delete_meal">
                            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                            <input type="hidden" name="meal_id" value="<?= $meal['id'] ?>">
                            <button type="submit" class="btn-delete-meal" title="Remover Refeição">&times;</button>
                        </form>
                        <h4><?= htmlspecialchars($meal['meal_type']) ?></h4>
                        <p class="meal-desc"><?= htmlspecialchars($meal['description']) ?></p>
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
