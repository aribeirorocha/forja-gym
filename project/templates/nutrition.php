<?php
declare(strict_types=1);

/**
 * Vista para visitantes: apenas lista os nutricionistas
 */
function output_public_nutritionists(array $nutritionists): void {
?>
    <section class="nutrition-section">
        <h2>A nossa Equipa de Nutrição</h2>
        <div class="nutritionist-grid">
            <?php foreach ($nutritionists as $n): ?>
                <article class="card">
                    <h3><?= htmlspecialchars($n['name']) ?></h3>
                    <p><strong>Especialidade:</strong> <?= htmlspecialchars($n['specialization']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php
}

/**
 * Vista para membros logados: plano, marcações e mensagens
 */
function output_member_nutrition_view(?array $plan, array $nutritionists): void {
?>
    <section class="nutrition-section">
        <h1>O teu Plano Nutricional</h1>
        
        <?php if ($plan): ?>
            <div class="card">
                <h2>Plano Atual</h2>
                <p>Nutricionista: <?= htmlspecialchars($plan['nutritionist_name']) ?></p>
                <div class="plan-content"><?= nl2br(htmlspecialchars($plan['content'])) ?></div>
            </div>
        <?php else: ?>
            <p>Ainda não tens um plano ativo.</p>
        <?php endif; ?>

        <hr>

        <h2>Marcar Consulta</h2>
        <form method="post" action="nutrition.php" class="nutrition-form">
            <input type="hidden" name="action" value="schedule_appointment">
            <label>Escolhe a nutricionista:</label>
            <select name="nutritionist_id" required>
                <?php foreach ($nutritionists as $n): ?>
                    <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Data e hora:</label>
            <input type="datetime-local" name="date" required>
            <button type="submit">Agendar</button>
        </form>

        <h2>Enviar Mensagem</h2>
        <form method="post" action="nutrition.php">
            <input type="hidden" name="action" value="send_message">
            <select name="nutritionist_id" required>
                <?php foreach ($nutritionists as $n): ?>
                    <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="message" required placeholder="Escreve a tua dúvida..."></textarea>
            <button type="submit">Enviar</button>
        </form>
    </section>
<?php
}
?>