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

<?php function output_classes(array $classes, bool $isLoggedIn) { 
    // 1. Extrair nomes únicos de Trainers e Tipos de Aula
    $trainers_list = [];
    $types_list = [];
    
    foreach($classes as $c) {
        $trainers_list[] = $c->trainer->name;
        $types_list[] = $c->name;
    }
    
    $unique_trainers = array_unique($trainers_list);
    $unique_types = array_unique($types_list);
    sort($unique_trainers);
    sort($unique_types);
?>
    <section class="classes-hero">
        <p class="section-tag">AULAS</p>
        <h1>As nossas <span class="highlight">Aulas</span></h1>
        <p class="classes-hero-subtitle">Encontra a aula perfeita para o teu objetivo.</p>
    </section>

    <div class="classes-container" style="padding: 60px 5%; max-width: 1200px; margin: 0 auto;">
        
        <div class="filters-bar">
            <select id="filter-type" class="filter-select">
                <option value="all">🧘‍♀️ Todos os Tipos</option>
                <?php foreach($unique_types as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-trainer" class="filter-select">
                <option value="all">🏋️ Todos os Trainers</option>
                <?php foreach($unique_trainers as $trainer): ?>
                    <option value="<?= htmlspecialchars($trainer) ?>"><?= htmlspecialchars($trainer) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-period" class="filter-select">
                <option value="all">🕒 Qualquer Horário</option>
                <option value="Manhã">🌅 Manhã (antes das 13h)</option>
                <option value="Tarde">☀️ Tarde (13h às 18h)</option>
                <option value="Noite">🌙 Noite (após as 19h)</option>
            </select>
        </div>

        <div class="class-grid" id="classes-grid">
            <?php foreach ($classes as $class): 
                
                // 2. Calcular se a aula é de Manhã, Tarde ou Noite
                $hora = (int)$class->session->datetime->format('H');
                if ($hora < 13) {
                    $periodo = 'Manhã';
                } elseif ($hora < 19) {
                    $periodo = 'Tarde';
                } else {
                    $periodo = 'Noite';
                }
            ?>
                <article class="class-card" 
                         data-trainer="<?= htmlspecialchars($class->trainer->name) ?>"
                         data-type="<?= htmlspecialchars($class->name) ?>"
                         data-period="<?= $periodo ?>">
                         
                    <img src="<?= $class->getPhotoPath() ?>" alt="<?= htmlspecialchars($class->name) ?>" class="class-photo">
                    <div class="class-summary">
                        <h3><?= htmlspecialchars($class->name) ?></h3>
                        <p>Trainer: <?= htmlspecialchars($class->trainer->name) ?></p>
                        <p><?= $class->session->getTime() ?> (<?= $periodo ?>)</p>
                        <a href="class.php?id=<?= $class->id ?>" class="btn-text">Ver detalhes</a>
                    </div>
                    
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php } ?>

<?php function output_class_details($class, $reviewStats, array $reviewsList, bool $isLoggedIn, bool $hasEnrollment)
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
                <p class="profile-specialty" style="color: #c62828;">Aula Cheia</p>
                <h1 class="profile-name"><?= htmlspecialchars($class->name) ?></h1>
            <?php } else { ?>
                <h1 class="profile-name"><?= htmlspecialchars($class->name) ?></h1>
                <form method="POST" action=<?= "class.php?id={$class->id}" ?>>
                    <input type="hidden" name="enroll" value=<?= $class->session->id ?>>
                    <button class="btn-review-submit" type="submit" style="margin-bottom: 20px; background-color: <?= $hasEnrollment ? 'var(--orange)' : 'var(--orange)' ?>;">
                        <?= $hasEnrollment ? "Desinscrever-se" : "Inscrever-se na Aula" ?>
                    </button>
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
                    <span><a href=<?= "trainer_profile.php?id={$class->trainer->id}" ?> style="color: var(--orange); text-decoration: underline;"><?= htmlspecialchars($class->trainer->name) ?></a></span>
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

            <?php if ($isLoggedIn): ?>
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

            <?php if (count($reviewsList) > 0): ?>
                <div class="reviews-container">
                    <button type="button" class="btn-toggle-reviews" id="toggle-reviews-btn">Ver comentários (<?= count($reviewsList) ?>)</button>
                    
                    <div class="reviews-list hidden" id="reviews-list-container">
                        <?php foreach ($reviewsList as $rev): 
                            // Gerador de avatares automáticos
                            if (!empty($rev['photo'])) {
                                $rev_photo = "../imgs/" . htmlspecialchars($rev['photo']);
                            } else {
                                $nome_codificado = urlencode($rev['name']);
                                $rev_photo = "https://ui-avatars.com/api/?name=" . $nome_codificado . "&background=ff6600&color=fff";
                            }
                            
                            $rev_date = date('d/m/Y \à\s H:i', strtotime($rev['created_at']));
                        ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="review-author">
                                        <img src="<?= $rev_photo ?>" alt="Membro" class="review-author-photo">
                                        <div class="review-author-info">
                                            <span class="review-author-name"><?= htmlspecialchars($rev['name']) ?></span>
                                            <span class="review-date"><?= $rev_date ?></span>
                                        </div>
                                    </div>
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= $rev['rate'] ? 'star-filled' : '' ?>">&#9733;</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="review-text"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="classes.php" class="btn-text btn-back-team">Voltar às Aulas</a>

        </article>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById('toggle-reviews-btn');
        const reviewsList = document.getElementById('reviews-list-container');
        
        if (toggleBtn && reviewsList) {
            toggleBtn.addEventListener('click', function() {
                reviewsList.classList.toggle('hidden');
                
                if (reviewsList.classList.contains('hidden')) {
                    toggleBtn.textContent = 'Ver comentários (' + <?= count($reviewsList) ?> + ')';
                } else {
                    toggleBtn.textContent = 'Ocultar comentários';
                }
            });
        }
    });
    </script>
<?php } ?>