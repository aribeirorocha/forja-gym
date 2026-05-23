<?php
declare(strict_types=1);

function output_public_nutritionists(array $nutritionists): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">nutricionistas</span></h1>
        <p class="trainers-hero-subtitle">Especialistas focados na tua alimentação, para potenciar os teus resultados.</p>
    </section>

    <section class="trainers-list nutrition-list" id="nutritionists">
        <div class="nutrition-grid">
            <?php foreach ($nutritionists as $nutri): 
                $photo_src = !empty($nutri['photo']) ? "../imgs/" . htmlspecialchars($nutri['photo']) : "../imgs/default-avatar.png";
            ?>
                <article class="nutrition-card-member">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Foto de <?= htmlspecialchars($nutri['name']) ?>" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <a href="login.php" class="btn-text" style="margin-top: 15px; display: inline-block;">Fazer login para contactar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php
}

function output_member_nutrition_view(?array $plan, array $nutritionists): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">nutricionistas</span></h1>
        <p class="trainers-hero-subtitle">Especialistas focados na tua alimentação, para potenciar os teus resultados.</p>
    </section>

    <div class="nutrition-dashboard">
        
        <?php if ($plan): ?>
            <div class="class-card" style="margin-bottom: 50px; text-align: center;">
                <h2>O teu Plano Nutricional</h2>
                <p style="color: var(--text-muted);">Prescrito por: <strong><?= htmlspecialchars($plan['nutritionist_name']) ?></strong></p>
                <div style="margin-top: 15px;">
                    <?= nl2br(htmlspecialchars($plan['content'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="nutrition-grid">
            <?php foreach ($nutritionists as $nutri): 
                $photo_src = !empty($nutri['photo']) ? "../imgs/" . htmlspecialchars($nutri['photo']) : "../imgs/default-avatar.png";
            ?>
                <article class="nutrition-card-member">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Nutricionista" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <a href="nutritionist_profile.php?id=<?= $nutri['id'] ?>" class="btn-text" style="margin-top: 15px; display: inline-block;">Ver perfil e Marcar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php } ?>