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
                    <input type="checkbox" id=<?= "toggle-t$index" ?> class="trainer-toggle">
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

<?php function output_class_details(GymClass $class)
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
    <?php } ?>