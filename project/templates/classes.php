<?php
require_once('../database/gym_class.php');
?>

<?php function output_featured_classes(array $classes)
{ ?>
    <section class="featured-classes" id="aulas">
        <header class="section-header">
            <p class="section-tag">AGENDA</p>
            <h2>Aulas em destaque</h2>
            <p>Encontra a aula certa para ti.</p>
        </header>

        <div class="class-grid">
            <?php foreach ($classes as $class) {
                if ($class->session == null) continue; ?>
                <article class="class-card">
                    <img src=<?= $class->getPhotoPath() ?> alt=<?= $class->name ?> class="class-photo">
                    <h3><?= $class->name ?></h3>
                    <p><?= $class->session->getDayOfWeek() ?> &bull; <?= $class->session->getTime() ?></p>
                    <p><?= $class->trainer->name ?> &bull; <?= $class->capacity ?> <?= $class->capacity == 1 ? 'vaga' : 'vagas' ?></p>
                    <a href="#" class="btn-text">Inscrever</a>
                </article>
            <?php } ?>
        </div>
    </section>
<?php } ?>


<?php function output_classes(array $classes, bool $isSignedIn)
{ ?>
    <section class="trainers-hero">
        <p class="section-tag">AULAS</p>
        <h1> <span class="highlight">Aulas</span></h1>
        <p class="trainers-hero-subtitle">Aulas.</p>
    </section>
    <section class="featured-classes" id="aulas">
        <div class="class-grid">
            <?php foreach ($classes as $index => $class) {
                if ($class->session == null) continue; ?>
                <article class="class-card">
                    <img src=<?= $class->getPhotoPath() ?> alt=<?= $class->name ?> class="class-photo">
                    <h3><?= $class->name ?></h3>
                    <p><?= $class->session->getDayOfWeek() ?> &bull; <?= $class->session->getTime() ?></p>
                    <p><?= $class->trainer->name ?> &bull; <?= $class->capacity ?> <?= $class->capacity == 1 ? 'vaga' : 'vagas' ?></p>
                    <?php if ($isSignedIn) { ?>
                        <a href=<?= "class.php?id={$class->id}" ?> class="btn-text">Ver mais</a>
                    <?php } else { ?>
                        <a href="login.php" class="btn-text">Fazer login para ver mais</a>
                    <?php } ?>
                </article>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<?php /*function output_class_details(GymClass $class)
{ ?>
    <article class="class-card">
        <img src=<?= $class->getPhotoPath() ?> alt=<?= $class->name ?> class="class-photo">
        <h3><?= $class->name ?></h3>
        <p><?= $class->session->getDayOfWeek() ?> &bull; <?= $class->session->getTime() ?></p>
        <p><?= $class->trainer->name ?> &bull; <?= $class->capacity ?> <?= $class->capacity == 1 ? 'vaga' : 'vagas' ?></p>
        <div >
            <hr class="profile-divider">
            <dl class="profile-info">
                <div class="profile-row">
                    <dt>Data</dt>
                    <dd><?= 1 ?> anos</dd>
                </div>
                <div class="profile-row">
                    <dt>Capacidade</dt>
                    <dd><?= $class->session->enrolmentCount . '/' . $class->capacity ?></dd>
                </div>
                <div class="profile-row">
                    <dt>Local</dt>
                    <dd><?= $class->local ?></dd>
                </div>
                <div class="profile-row">
                    <dt>Aulas</dt>
                    <dd>Yoga de manhã &bull; Pilates ao início da tarde</dd>
                </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
                <h4 class="review-title">Deixa a tua review</h4>
                <form class="review-form" method="post">
                    <fieldset class="star-rating">
                        <legend class="sr-only">Classificação</legend>
                        <input type="radio" id="r5-t1" name="rating-t1" value="5">
                        <label for="r5-t1" title="5 estrelas">&#9733;</label>
                        <input type="radio" id="r4-t1" name="rating-t1" value="4">
                        <label for="r4-t1" title="4 estrelas">&#9733;</label>
                        <input type="radio" id="r3-t1" name="rating-t1" value="3">
                        <label for="r3-t1" title="3 estrelas">&#9733;</label>
                        <input type="radio" id="r2-t1" name="rating-t1" value="2">
                        <label for="r2-t1" title="2 estrelas">&#9733;</label>
                        <input type="radio" id="r1-t1" name="rating-t1" value="1">
                        <label for="r1-t1" title="1 estrela">&#9733;</label>
                    </fieldset>
                    <label for="review-text-t1" class="review-label">Comentário</label>
                    <textarea id="review-text-t1" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                    <button type="submit" class="btn-review-submit">Enviar review</button>
                </form>
            </section>
        </div>
</article>
    <?php } */ ?>


<?php function output_class_details(GymClass $class, array $reviewStats, bool $isUser, bool $hasEnrollment)
{ ?>
    <link rel="stylesheet" href="../css/trainer_profile.css">

    <main class="profile-page-wrapper">
        <article class="profile-login-card">

            <div class="profile-rating-badge">
                <span class="star-icon">&#9733;</span>
                <?= number_format($reviewStats['avg'], 1) ?>
                <span class="review-count">(<?= $reviewStats['count'] ?>)</span>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'review'): ?>
                <div class="success-banner">
                    Obrigado! A tua review foi registada com sucesso.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'enroll'): ?>
                <div class="success-banner">
                    Obrigado! A tua inscrição foi registada com sucesso.
                </div>
            <?php endif; ?>

            <img src="<?= $class->getPhotoPath() ?>" alt="<?= htmlspecialchars($class->name) ?>" class="profile-big-photo">

            <?php if ($class->session->enrollmentCount >= $class->capacity) { ?>
                <p class="profile-specialty">Cheio</p>
            <?php } else { ?>
                <h1 class="profile-name"><?= htmlspecialchars($class->name) ?></h1>
                <form method="POST" action=<?= "class.php?id={$class->id}" ?>>
                    <input type="hidden" name="enroll" value=<?= $class->session->id ?>>
                    <button class="btn-schedule" type="submit"><?= $hasEnrollment ? "Desinscrever-se" : "Inscrever-se" ?></button>
                </form>
            <?php } ?>

            <div class="profile-details-list">
                <div class="profile-detail-item">
                    <strong>Data</strong>
                    <span> <?= $class->session->getDayOfWeek() ?>, <?= $class->session->getDateText() ?> às <?= $class->session->getTime() ?> </span>
                </div>
                <div class="profile-detail-item">
                    <strong>Capacidade</strong>
                    <span> <?= htmlspecialchars($class->session->enrollmentCount . '/' . $class->capacity) ?> </span>
                </div>
                <div class="profile-detail-item">
                    <strong>Trainer</strong>
                    <span><a href=<?= "trainer_profile.php?id={$class->trainer->id}" ?>><?= htmlspecialchars($class->trainer->name) ?></a></span>
                </div>
                <div class="profile-detail-item">
                    <strong>Duração</strong>
                    <span><?= htmlspecialchars($class->durationMinutes . " min") ?></span>
                </div>
                <div class="profile-detail-item">
                    <strong>Local</strong>
                    <span><?= htmlspecialchars($class->local) ?></span>
                </div>
            </div>

            <?php if ($isUser): ?>
                <section class="review-section">
                    <h4 class="review-box-title">Deixa a tua review</h4>
                    <form class="review-form" method="post" action="class.php?id=<?= $class->id ?>">

                        <fieldset class="star-rating">
                            <input type="radio" id="r5" name="rating" value="5">
                            <label for="r5" title="5 estrelas">&#9733;</label>
                            <input type="radio" id="r4" name="rating" value="4">
                            <label for="r4" title="4 estrelas">&#9733;</label>
                            <input type="radio" id="r3" name="rating" value="3">
                            <label for="r3" title="3 estrelas">&#9733;</label>
                            <input type="radio" id="r2" name="rating" value="2">
                            <label for="r2" title="2 estrelas">&#9733;</label>
                            <input type="radio" id="r1" name="rating" value="1" required>
                            <label for="r1" title="1 estrela">&#9733;</label>
                        </fieldset>

                        <textarea name="comentario" rows="3" placeholder="Partilha a tua experiência..." required></textarea>

                        <button type="submit" class="btn-review-submit">Enviar review</button>
                    </form>
                </section>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                    Faz <a href="login.php" style="color: var(--orange); text-decoration: underline;">login</a> para deixar uma review.
                </p>
            <?php endif; ?>

            <a href="trainers.php" class="btn-text btn-back-team">Voltar à Equipa</a>

        </article>
    </main>
<?php } ?>