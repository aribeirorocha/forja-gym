<?php function output_featured_classes(array $classes) { ?>
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
                    <p><?= $class->trainer->name ?> &bull; <?= $class->capacity?> <?= $class->capacity == 1 ? 'vaga' : 'vagas' ?></p>
                    <a href="#" class="btn-text">Inscrever</a>
                </article>
            <?php } ?>
        </div>
    </section>
<?php } ?>