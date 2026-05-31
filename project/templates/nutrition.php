<?php

declare(strict_types=1);

function output_public_nutritionists(array $nutritionists, bool $is_admin = false): void
{
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
                <article class="nutrition-card-member" data-id="<?= $nutri['id'] ?>">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Foto de <?= htmlspecialchars($nutri['name']) ?>" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>

                        <a href="login.php" class="btn-text" style="margin-top: 15px; display: inline-block;">Fazer login para contactar</a>

                        <?php if ($is_admin): ?>
                            <button type="button" class="admin-delete-nutritionist" data-id="<?= $nutri['id'] ?>">
                                Remover
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
                <article id="trigger-add-nutritionist" class="nutrition-card-member">
                    <div>
                        <span>+</span>
                        <span>Adicionar Nutricionista</span>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($is_admin) output_nutritionist_modal(); ?>
<?php
}

function output_member_nutrition_view(?array $plan, array $nutritionists, bool $is_admin = false): void
{
?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">nutricionistas</span></h1>
        <p class="trainers-hero-subtitle">Especialistas focados na tua alimentação, para potenciar os teus resultados.</p>
    </section>

    <div class="nutrition-dashboard">

        <?php if ($plan): ?>
            <div class="class-card" style="margin-bottom: 60px; max-width: 600px; margin-left: auto; margin-right: auto; text-align: center; padding: 40px 30px; border-top: 4px solid #f27a38; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 12px; background: var(--bg-card, #fff);">
                <h2 style="font-size: 1.8rem; color: var(--text-main); margin-bottom: 10px;">O teu Plano Nutricional</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 25px;">Prescrito por: <strong><?= htmlspecialchars($plan['nutritionist_name']) ?></strong></p>
                
                <div style="background: var(--bg-elevated, #f9f9f9); padding: 25px; border-radius: 12px; text-align: left; margin-bottom: 30px; border-left: 3px solid #f27a38;">
                    <p style="margin: 0 0 10px 0; font-size: 1.1rem; color: var(--text-main);"><strong>Objetivo:</strong> <?= htmlspecialchars($plan['goal'] ?? '') ?></p>
                    <?php if (!empty($plan['notes'])): ?>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap;"><?= htmlspecialchars($plan['notes']) ?></p>
                    <?php endif; ?>
                </div>

                <a href="profile.php#nutrition-plan" style="display: inline-block; background-color: #f27a38; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; box-shadow: 0 4px 10px rgba(242, 122, 56, 0.3); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(242, 122, 56, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(242, 122, 56, 0.3)';">
                    Ver Plano Completo no Perfil
                </a>
            </div>
        <?php endif; ?>

        <div class="nutrition-grid">
            <?php foreach ($nutritionists as $nutri):
                $photo_src = !empty($nutri['photo']) ? "../imgs/" . htmlspecialchars($nutri['photo']) : "../imgs/default-avatar.png";
            ?>
                <article class="nutrition-card-member" data-id="<?= $nutri['id'] ?>">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Nutricionista" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>

                        <?php if (!$is_admin): ?>
                            <a href="nutritionist_profile.php?id=<?= $nutri['id'] ?>" class="btn-text" style="margin-top: 15px; display: inline-block;">Ver perfil e Marcar</a>
                        <?php endif; ?>

                        <?php if ($is_admin): ?>
                            <button type="button" class="admin-delete-nutritionist" data-id="<?= $nutri['id'] ?>">
                                Remover
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
                <article id="trigger-add-nutritionist" class="nutrition-card-member">
                    <div>
                        <span>+</span>
                        <span>Adicionar Nutricionista</span>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($is_admin) output_nutritionist_modal(); ?>
<?php } 

function output_nutritionist_modal(): void
{ ?>
    <!-- ══ MODAL ADICIONAR NUTRICIONISTA ══ -->
    <div id="add-nutritionist-modal" class="add-class-modal-overlay hidden">
        <article>
            <button type="button" id="close-add-nutritionist">&times;</button>
            <p>NOVO NUTRICIONISTA</p>

            <form action="admin.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="add_nutritionist">

                <div>
                    <input type="text" name="nu_name" placeholder="Nome completo" required>
                </div>

                <div>

                    <div>
                        <strong>Username</strong>
                        <input type="text" name="nu_username" placeholder="username" required>
                    </div>

                    <div>
                        <strong>Email</strong>
                        <input type="email" name="nu_email" placeholder="email@exemplo.com" required>
                    </div>

                    <div>
                        <strong>Password</strong>
                        <input type="password" name="nu_password" placeholder="mínimo 6 caracteres" required minlength="6">
                    </div>

                    <div>
                        <strong>Especialização</strong>
                        <input type="text" name="nu_specialization" placeholder="ex: Desportiva, Clínica">
                    </div>

                    <div>
                        <strong>Nº Cédula</strong>
                        <input type="text" name="nu_license" placeholder="ex: ON-12345">
                    </div>

                    <div>
                        <strong>Foto</strong>
                        <div>
                            <label for="nu-photo-input" id="nu-photo-label">
                                📷 Escolher foto
                            </label>
                            <input type="file" id="nu-photo-input" name="nu_photo" accept="image/*">
                        </div>
                    </div>
                </div>

                <button type="submit">
                    Adicionar Nutricionista
                </button>
            </form>
        </article>
    </div>

<?php } ?>