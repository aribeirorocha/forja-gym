<?php
require_once('../database/trainer.php');
?>

<?php function output_trainers_hero()
{ ?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">trainers</span></h1>
        <p class="trainers-hero-subtitle">Especialistas dedicados ao teu progresso, cada um com a sua área de excelência.</p>
    </section>
<?php } ?>

<?php function output_trainers(array $trainers, bool $is_admin = false)
{ ?>
    <section class="trainers-list" id="trainers">

        <div class="filters-bar">
            <select id="filter-gender" class="filter-select">
                <option value="all">Todos os Géneros</option>
                <option value="masculino">Homens</option>
                <option value="feminino">Mulheres</option>
            </select>

            <select id="sort-trainers" class="filter-select">
                <option value="default">Ordenar por...</option>
                <option value="age-asc">Mais novos</option>
                <option value="age-desc">Mais velhos</option>
                <option value="time-desc">Mais tempo no ginásio</option>
                <option value="time-asc">Menos tempo no ginásio</option>
            </select>
        </div>

        <div class="trainer-grid" id="trainers-grid-container">

            <?php foreach ($trainers as $trainer) {
                $ano_entrada = $trainer->trainerCreatedAt->format('Y');
                $idade       = $trainer->age ?? 0;
                $genero      = isset($trainer->sex) ? strtolower($trainer->sex) : 'all';
            ?>
                <article class="trainer-card"
                    data-id="<?= $trainer->id ?>"
                    data-gender="<?= htmlspecialchars($genero) ?>"
                    data-age="<?= $idade ?>"
                    data-year="<?= $ano_entrada ?>">

                    <div class="trainer-summary">
                        <img src="<?= htmlspecialchars($trainer->photoPath) ?>" alt="<?= htmlspecialchars($trainer->name) ?>" class="trainer-photo">
                        <h3><?= htmlspecialchars($trainer->name) ?></h3>
                        <p class="trainer-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($trainer->specialization)) ?></p>

                        <a href="trainer_profile.php?id=<?= $trainer->id ?>" class="btn-text" style="margin-top:15px;display:inline-block;">Ver perfil</a>

                        <?php if ($is_admin): ?>
                            <button type="button"
                                class="admin-delete-trainer"
                                data-id="<?= $trainer->id ?>">
                                Remover
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php } ?>

            <?php if ($is_admin): ?>
                <!-- Card "+" para adicionar trainer -->
                <article id="trigger-add-trainer" class="trainer-card">
                    <div>
                        <span>+</span>
                        <span>Adicionar Trainer</span>
                    </div>
                </article>
            <?php endif; ?>

        </div>
    </section>

    <?php if ($is_admin): ?>
        <!-- ══ MODAL ADICIONAR TRAINER ══ -->
        <div id="add-trainer-modal" class="add-class-modal-overlay hidden">
            <article>
                <button type="button" id="close-add-trainer">&times;</button>
                <p>NOVO TRAINER</p>

                <form action="admin.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                    <input type="hidden" name="action" value="add_trainer">

                    <div>
                        <input type="text" name="trainer_name" placeholder="Nome completo" required>
                    </div>

                    <div>

                        <div>
                            <strong>Email</strong>
                            <input type="email" name="trainer_email" placeholder="nometrainer@gmail.com" required>
                        </div>

                        <div>
                            <strong>Password</strong>
                            <input type="password" name="trainer_password" placeholder="••••••••" required>
                        </div>

                        <div>
                            <strong>Género</strong>
                            <select name="trainer_sex">
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                            </select>
                        </div>

                        <div>
                            <strong>Especialização</strong>
                            <input type="text" name="trainer_specialization" placeholder="ex: Musculação, Cardio">
                        </div>

                        <div>
                            <strong>Foto</strong>
                            <div style="width:220px;">
                                <label for="trainer-photo-input" id="trainer-photo-label">
                                    📷 Escolher foto
                                </label>
                                <input type="file" id="trainer-photo-input" name="trainer_photo" accept="image/*" style="display:none;">
                            </div>
                        </div>

                        <div>
                            <strong>Localização</strong>
                            <input type="text" name="trainer_location" placeholder="ex: Lisboa">
                        </div>
                    </div>

                    <button type="submit">
                        Adicionar Trainer
                    </button>
                </form>
            </article>
        </div>

    <?php endif; ?>

<?php } ?>

<?php function output_featured_trainers(array $trainers)
{ ?>
    <section class="trainers">
        <header class="section-header">
            <p class="section-tag">EQUIPA</p>
            <h2>Os nossos trainers</h2>
            <p>Especialistas dedicados ao teu progresso.</p>
        </header>

        <div class="trainer-grid">
            <?php foreach ($trainers as $trainer) { ?>
                <article class="trainer-card">
                    <img src=<?= $trainer->photoPath ?> alt=<?= $trainer->name ?> class="trainer-photo">
                    <h3><?= $trainer->name ?></h3>
                    <p><?= "Especialista em " . str_replace(',', ' e ', $trainer->specialization) ?></p>
                    <a href="trainer_profile.php?id=<?= $trainer->id ?>" class="btn-text"> Ver perfil </a>
                </article>
            <?php } ?>
        </div>
    </section>
<?php } ?>