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
                    <a href="class.php?id=<?= $class->id ?>" class="btn-text">Inscrever</a>
                </article>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<?php function output_classes(array $classes, bool $isLoggedIn) { 
    $user = UserAuth::currentUser();
    $isTrainer = ($user && isset($user['role']) && $user['role'] === 'trainer');
    $isAdmin   = ($user && isset($user['role']) && $user['role'] === 'admin');

    $trainers_list = [];
    $types_list = [];
    
    foreach($classes as $c) {
        $trainers_list[] = $c->trainer->name ?? 'Sem Trainer';
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
                <option value="all">Todos os Tipos</option>
                <?php foreach($unique_types as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-trainer" class="filter-select">
                <option value="all">Todos os Trainers</option>
                <?php foreach($unique_trainers as $trainer): ?>
                    <option value="<?= htmlspecialchars($trainer) ?>"><?= htmlspecialchars($trainer) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-period" class="filter-select">
                <option value="all">Qualquer Horário</option>
                <option value="Manhã">Manhã (antes das 13h)</option>
                <option value="Tarde">Tarde (13h às 18h)</option>
                <option value="Noite">Noite (após as 19h)</option>
            </select>
        </div>

        <div class="class-grid" id="classes-grid">
            <?php foreach ($classes as $class): 
                if ($class->session === null) {
                    $periodo = 'all';
                    $tempo_texto = 'Horário não definido';
                } else {
                    $hora = (int)$class->session->datetime->format('H');
                    if ($hora < 13) {
                        $periodo = 'Manhã';
                    } elseif ($hora < 19) {
                        $periodo = 'Tarde';
                    } else {
                        $periodo = 'Noite';
                    }
                    $tempo_texto = $class->session->getTime() . " ($periodo)";
                }
            ?>
                <article class="class-card" style="position: relative;"
                         data-trainer="<?= htmlspecialchars($class->trainer->name ?? 'Sem Trainer') ?>"
                         data-type="<?= htmlspecialchars($class->name) ?>"
                         data-period="<?= $periodo ?>">
                         
                    <img src="<?= $class->getPhotoPath() ?>" alt="<?= htmlspecialchars($class->name) ?>" class="class-photo">
                    <div class="class-summary">
                        <h3><?= htmlspecialchars($class->name) ?></h3>
                        <p>Trainer: <?= htmlspecialchars($class->trainer->name ?? 'Não atribuído') ?></p>
                        <p style="font-weight: 600; color: <?= $class->session === null ? 'var(--orange)' : 'inherit' ?>;"><?= $tempo_texto ?></p>
                        
                        <div style="margin-top: 20px;">
                            <a href="class.php?id=<?= $class->id ?>" class="btn-text" style="display: block; text-align: center;">Ver detalhes</a>
                        </div>
                        <?php if ($isAdmin): ?>
                        <button type="button" class="admin-delete-class" data-id="<?= $class->id ?>"
                                style="margin-top:10px;width:100%;background:none;border:1px solid #e53e3e;color:#e53e3e;padding:7px 0;border-radius:20px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s;">
                            Remover
                        </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($isTrainer): ?>
                <article class="class-card text-center" id="trigger-add-class" style="cursor: pointer; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--orange); background-color: #fffbf9; min-height: 310px; transition: var(--transition);">
                    <div style="text-align: center; color: var(--orange);">
                        <span style="font-size: 5rem; font-weight: 200; display: block; line-height: 1; margin-bottom: 5px;">+</span>
                        <span style="font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Criar Aula</span>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>

    <!-- BLINDAGEM DO POP-UP: Forçado com !important largura, max-largura, altura e min-altura para não esticar de forma alguma -->
    <?php if ($isTrainer): ?>
        <div id="add-class-modal" class="add-class-modal-overlay hidden">
            <article class="profile-login-card" style="width: 360px !important; max-width: 360px !important; height: auto !important; min-height: unset !important; max-height: unset !important; display: block !important; margin: 0 auto !important; position: relative !important; animation: modalSlideUp 0.3s ease !important; text-align: center !important; padding: 25px !important; border-radius: 12px !important; box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important; background-color: #ffffff !important; box-sizing: border-box !important; float: none !important;">
                <button type="button" id="close-add-class" class="close-modal-btn">&times;</button>
                <p class="profile-specialty" style="margin-bottom: 5px !important; font-weight: 700 !important; color: var(--orange) !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 0.8rem !important; text-align: center !important;">CRIAÇÃO DE AULA</p>
                
                <form action="create_class.php" method="post" style="text-align: left !important; width: 100% !important; display: block !important;">
                    <input type="hidden" name="action" value="create_class_with_session">
                    
                    <div style="margin-bottom: 20px !important; text-align: center !important; width: 100% !important;">
                        <input type="text" name="name" placeholder="Nome da Aula" required 
                               style="font-size: 1.4rem !important; font-weight: 800 !important; text-align: center !important; border: none !important; border-bottom: 2px solid var(--gray-border) !important; width: 100% !important; padding: 4px !important; color: var(--text-main) !important; outline: none !important; font-family: inherit !important;">
                    </div>

                    <div class="profile-details-list" style="margin-bottom: 20px !important; width: 100% !important; display: block !important; height: auto !important; min-height: unset !important;">
                        
                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid var(--gray-bg) !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Data de Início</strong>
                            <input type="date" name="session_date" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 150px !important; box-sizing: border-box !important; font-family: inherit !important; font-size: 0.85rem !important; color: var(--text-main) !important; background: white !important;">
                        </div>

                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid var(--gray-bg) !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Hora da Aula</strong>
                            <input type="time" name="session_time" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 150px !important; box-sizing: border-box !important; font-family: inherit !important; font-size: 0.85rem !important; color: var(--text-main) !important; background: white !important;">
                        </div>

                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid var(--gray-bg) !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Capacidade</strong>
                            <div style="width: 150px !important; display: flex !important; align-items: center !important; gap: 6px !important; box-sizing: border-box !important;">
                                <input type="number" name="capacity" min="1" placeholder="20" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 100% !important; box-sizing: border-box !important; font-size: 0.85rem !important; color: var(--text-main) !important; background: white !important;">
                                <span style="color: var(--text-muted) !important; font-size: 0.8rem !important; font-weight: 600 !important; white-space: nowrap !important;">vagas</span>
                            </div>
                        </div>

                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid var(--gray-bg) !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Duração</strong>
                            <div style="width: 150px !important; display: flex !important; align-items: center !important; gap: 6px !important; box-sizing: border-box !important;">
                                <input type="number" name="duration_minutes" min="1" placeholder="60" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 100% !important; box-sizing: border-box !important; font-size: 0.85rem !important; color: var(--text-main) !important; background: white !important;">
                                <span style="color: var(--text-muted) !important; font-size: 0.8rem !important; font-weight: 600 !important; white-space: nowrap !important;">min</span>
                            </div>
                        </div>

                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid var(--gray-bg) !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Localização</strong>
                            <input type="text" name="local" placeholder="Ex: Estúdio 1" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 150px !important; box-sizing: border-box !important; font-family: inherit !important; font-size: 0.85rem !important; color: var(--text-main) !important; background: white !important;">
                        </div>

                        <div class="profile-detail-item" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; width: 100% !important; float: none !important;">
                            <strong style="font-size: 0.85rem !important; color: var(--text-muted) !important;">Tipo de Aula</strong>
                            <select name="type" required style="padding: 6px 10px !important; border: 1px solid var(--gray-border) !important; border-radius: 6px !important; width: 150px !important; box-sizing: border-box !important; font-family: inherit !important; font-size: 0.85rem !important; background: white !important; color: var(--text-main) !important;">
                                <option value="yoga">Yoga</option>
                                <option value="pilates">Pilates</option>
                                <option value="spinning">Spinning</option>
                                <option value="crossfit">Crossfit</option>
                                <option value="zumba">Zumba</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-review-submit" style="width: 100% !important; margin: 0 !important; padding: 12px !important; font-size: 0.9rem !important;">Confirmar e Publicar Aula</button>
                </form>
            </article>
        </div>

        <style>
        .add-class-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0, 0, 0, 0.45); backdrop-filter: blur(4px);
            display: flex; justify-content: center; align-items: center; z-index: 99999;
        }
        .add-class-modal-overlay.hidden { display: none; }
        .close-modal-btn {
            position: absolute; top: 15px; right: 15px; background: none; border: none;
            font-size: 2rem; color: var(--text-muted); cursor: pointer; line-height: 1;
        }
        .close-modal-btn:hover { color: var(--orange); }
        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        </style>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const trigger = document.getElementById('trigger-add-class');
            const modal = document.getElementById('add-class-modal');
            const closeBtn = document.getElementById('close-add-class');

            if (trigger && modal && closeBtn) {
                trigger.addEventListener('click', () => modal.classList.remove('hidden'));
                closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) modal.classList.add('hidden');
                });
            }
        });
        </script>
    <?php endif; ?>

    <!-- Remover aula (admin) -->
    <?php if ($isAdmin): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.admin-delete-class').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('Remover esta aula permanentemente?')) return;
                const id   = this.dataset.id;
                const card = this.closest('article');
                fetch('admin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=delete_class&id=' + encodeURIComponent(id)
                }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover aula.'); });
            });
        });
    });
    </script>
    <?php endif; ?>
<?php } ?>


<?php function output_class_details($class, $reviewStats, array $reviewsList, bool $isLoggedIn, bool $hasEnrollment)
{ 
    $user = UserAuth::currentUser();
    $isTrainer = ($user && $user['role'] === 'trainer');
    $canEdit = ($user && $user['role'] === 'trainer' && (int)$class->trainerId === (int)$user['id']);
?>
    <link rel="stylesheet" href="../css/trainer_profile.css">

    <main class="profile-page-wrapper">
        <article class="profile-login-card" style="position: relative;">

           
            <?php if ($canEdit): ?>
                <button type="button" id="delete-class-detail-btn" data-class-id="<?= $class->id ?>"
                        style="position: absolute; top: 20px; left: 25px; background: none; border: none; color: #e63946; font-weight: 700; font-size: 0.9rem; text-decoration: underline; cursor: pointer; z-index: 10; padding: 0;">
                    Eliminar
                </button>
            <?php endif; ?>

            <?php if (!$isTrainer): ?>
                <div class="profile-rating-badge">
                    <span class="star-icon">&#9733;</span>
                    <?= number_format($reviewStats['avg'], 1) ?>
                    <span class="review-count">(<?= $reviewStats['count'] ?>)</span>
                </div>
            <?php endif; ?>

            <img src="<?= $class->getPhotoPath() ?>" alt="<?= htmlspecialchars($class->name) ?>" class="profile-big-photo">

            <?php if ($class->session && $class->session->enrollmentCount >= $class->capacity) { ?>
                <p class="profile-specialty" style="color: #c62828;">Aula Cheia</p>
                <h1 class="profile-name"><?= htmlspecialchars($class->name) ?></h1>
            <?php } else { ?>
                <h1 class="profile-name"><?= htmlspecialchars($class->name) ?></h1>
                <?php if ($class->session && !$isTrainer): ?>
                    <form method="POST" action=<?= "class.php?id={$class->id}" ?>>
                        <input type="hidden" name="enroll" value=<?= $class->session->id ?>>
                        <button class="btn-review-submit" type="submit" style="margin-bottom: 20px;">
                            <?= $hasEnrollment ? "Desinscrever-se" : "Inscrever-se na Aula" ?>
                        </button>
                    </form>
                <?php endif; ?>
            <?php } ?>

            <div class="profile-details-list">
                
                <div class="profile-detail-item">
                    <strong>Data</strong>
                    <div class="inline-view-mode">
                        <span><?= $class->session ? ($class->session->getDayOfWeek() . ', ' . $class->session->getDateText() . ' às ' . $class->session->getTime()) : '🗓️ Horário não definido' ?></span>
                        <?php if ($canEdit && $class->session !== null): ?>
                            <span class="inline-edit-trigger" style="text-decoration: underline; cursor: pointer; margin-left: 8px; color: var(--orange); font-size: 0.85rem; font-weight:600;">Alterar</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($canEdit && $class->session !== null): ?>
                        <form action="create_class.php" method="post" class="inline-edit-form" style="display: none; margin-top: 6px; gap: 8px; flex-wrap: wrap;">
                            <input type="hidden" name="action" value="update_class_session">
                            <input type="hidden" name="id_session" value="<?= $class->session->id ?>">
                            <input type="hidden" name="id_class" value="<?= $class->id ?>">
                            <input type="date" name="session_date" value="<?= $class->session->datetime->format('Y-m-d') ?>" required style="padding: 4px 8px; border: 1px solid var(--gray-border); border-radius: 6px; font-family: inherit; font-size:0.85rem;">
                            <input type="time" name="session_time" value="<?= $class->session->datetime->format('H:i') ?>" required style="padding: 4px 8px; border: 1px solid var(--gray-border); border-radius: 6px; font-family: inherit; font-size:0.85rem;">
                            <button type="submit" class="btn-review-submit" style="margin:0; padding: 4px 12px; width:auto; font-size:0.8rem; border-radius:20px;">✓</button>
                            <span class="inline-cancel-trigger" style="text-decoration: underline; cursor: pointer; color: var(--text-muted); font-size: 0.85rem; margin-left: 4px; align-self: center;">Cancelar</span>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="profile-detail-item">
                    <strong>Capacidade</strong>
                    <div class="inline-view-mode">
                        <span><?= htmlspecialchars($class->session ? ($class->session->enrollmentCount . '/' . $class->capacity) : ('0/' . $class->capacity)) ?></span>
                        <?php if ($canEdit): ?>
                            <span class="inline-edit-trigger" style="text-decoration: underline; cursor: pointer; margin-left: 8px; color: var(--orange); font-size: 0.85rem; font-weight:600;">Alterar</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($canEdit): ?>
                        <form action="create_class.php" method="post" class="inline-edit-form" style="display: none; margin-top: 6px; gap: 8px; align-items: center;">
                            <input type="hidden" name="action" value="update_class_capacity">
                            <input type="hidden" name="id_class" value="<?= $class->id ?>">
                            <input type="number" name="capacity" value="<?= $class->capacity ?>" min="1" required style="padding: 4px 8px; border: 1px solid var(--gray-border); border-radius: 6px; font-family: inherit; font-size:0.85rem; width: 80px;">
                            <button type="submit" class="btn-review-submit" style="margin:0; padding: 4px 12px; width:auto; font-size:0.8rem; border-radius:20px;">✓</button>
                            <span class="inline-cancel-trigger" style="text-decoration: underline; cursor: pointer; color: var(--text-muted); font-size: 0.85rem; margin-left: 4px;">Cancelar</span>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="profile-detail-item">
                    <strong>Duração</strong>
                    <div class="inline-view-mode">
                        <span><?= htmlspecialchars($class->durationMinutes . " min") ?></span>
                        <?php if ($canEdit): ?>
                            <span class="inline-edit-trigger" style="text-decoration: underline; cursor: pointer; margin-left: 8px; color: var(--orange); font-size: 0.85rem; font-weight:600;">Alterar</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($canEdit): ?>
                        <form action="create_class.php" method="post" class="inline-edit-form" style="display: none; margin-top: 6px; gap: 8px; align-items: center;">
                            <input type="hidden" name="action" value="update_class_duration">
                            <input type="hidden" name="id_class" value="<?= $class->id ?>">
                            <input type="number" name="duration_minutes" value="<?= $class->durationMinutes ?>" min="1" required style="padding: 4px 8px; border: 1px solid var(--gray-border); border-radius: 6px; font-family: inherit; font-size:0.85rem; width: 80px;">
                            <button type="submit" class="btn-review-submit" style="margin:0; padding: 4px 12px; width:auto; font-size:0.8rem; border-radius:20px;">✓</button>
                            <span class="inline-cancel-trigger" style="text-decoration: underline; cursor: pointer; color: var(--text-muted); font-size: 0.85rem; margin-left: 4px;">Cancelar</span>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="profile-detail-item">
                    <strong>Local</strong>
                    <div class="inline-view-mode">
                        <span><?= htmlspecialchars($class->local) ?></span>
                        <?php if ($canEdit): ?>
                            <span class="inline-edit-trigger" style="text-decoration: underline; cursor: pointer; margin-left: 8px; color: var(--orange); font-size: 0.85rem; font-weight:600;">Alterar</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($canEdit): ?>
                        <form action="create_class.php" method="post" class="inline-edit-form" style="display: none; margin-top: 6px; gap: 8px; align-items: center;">
                            <input type="hidden" name="action" value="update_class_local">
                            <input type="hidden" name="id_class" value="<?= $class->id ?>">
                            <input type="text" name="local" value="<?= htmlspecialchars($class->local) ?>" required style="padding: 4px 8px; border: 1px solid var(--gray-border); border-radius: 6px; font-family: inherit; font-size:0.85rem; width: 60%;">
                            <button type="submit" class="btn-review-submit" style="margin:0; padding: 4px 12px; width:auto; font-size:0.8rem; border-radius:20px;">✓</button>
                            <span class="inline-cancel-trigger" style="text-decoration: underline; cursor: pointer; color: var(--text-muted); font-size: 0.85rem; margin-left: 4px;">Cancelar</span>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="profile-detail-item">
                    <strong>Trainer</strong>
                    <span><a href=<?= "trainer_profile.php?id={$class->trainer->id}" ?> style="color: var(--orange); text-decoration: underline;"><?= htmlspecialchars($class->trainer->name) ?></a></span>
                </div>
            </div>

            <?php if (!$isTrainer): ?>
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
            <?php endif; ?>

            <?php if (!$isTrainer && count($reviewsList) > 0): ?>
                <div class="reviews-container">
                    <button type="button" class="btn-toggle-reviews" id="toggle-reviews-btn">Ver comentários (<?= count($reviewsList) ?>)</button>
                    <div class="reviews-list hidden" id="reviews-list-container">
                        <?php foreach ($reviewsList as $rev): 
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
                toggleBtn.textContent = reviewsList.classList.contains('hidden') ? 'Ver comentários (' + <?= count($reviewsList) ?> + ')' : 'Ocultar comentários';
            });
        }

        const deleteDetailBtn = document.getElementById('delete-class-detail-btn');
        if (deleteDetailBtn) {
            deleteDetailBtn.addEventListener('click', function() {
                const classId = this.dataset.classId;
                if (!confirm('Tem a certeza que quer remover esta aula permanentemente?')) { return; }
                
                fetch('create_class.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=remove_class&id=' + encodeURIComponent(classId)
                })
                .then(response => {
                    if (!response.ok) { throw new Error('Erro ao remover a aula'); }
                    window.location.href = 'classes.php';
                })
                .catch(error => {
                    alert('Não foi possível remover a aula.');
                    console.error(error);
                });
            });
        }

        document.querySelectorAll('.inline-edit-trigger').forEach(btn => {
            btn.addEventListener('click', function() {
                const pItem = this.closest('.profile-detail-item');
                pItem.querySelector('.inline-view-mode').style.display = 'none';
                pItem.querySelector('.inline-edit-form').style.display = 'flex';
            });
        });

        document.querySelectorAll('.inline-cancel-trigger').forEach(btn => {
            btn.addEventListener('click', function() {
                const pItem = this.closest('.profile-detail-item');
                pItem.querySelector('.inline-view-mode').style.display = 'block';
                pItem.querySelector('.inline-edit-form').style.display = 'none';
            });
        });
    });
    </script>
<?php } ?>

<?php function output_class_form() {} ?>

<?php function output_class_session_forms(array $classes) { ?>
    <section class="class-session-page">
        <section class="trainer-class-list">
            <header>
                <p class="profile-specialty">SESSÕES</p>
                <h2 id="session-form-title" class="profile-name">Criar Sessões</h2>
            </header>
            <?php foreach ($classes as $class) {
                output_class_session_form($class);
            } ?>
        </section>
    </section>
<?php } ?>

<?php function output_class_session_form(GymClass $class) { ?>
    <article class="trainer-class-card">
        <header>
            <section>
                <h3><?= htmlspecialchars($class->name) ?></h3>
                <p class="review-text"><?= htmlspecialchars($class->local) ?></p>
            </section>
            <span class="class-type-pill"><?= htmlspecialchars($class->type) ?></span>
        </header>
        <section class="profile-details-list">
            <p class="profile-detail-item">
                <strong>Duração</strong>
                <span><?= $class->durationMinutes ?> mins</span>
            </p>
            <p class="profile-detail-item">
                <strong>Capacidade</strong>
                <span><?= $class->capacity ?> pessoas</span>
            </p>
            <p class="profile-detail-item">
                <strong>Estado</strong>
                <span><?= $class->active ? 'Ativa' : 'Descontinuada' ?></span>
            </p>
        </section>

        <form action="create_class.php" method="post" class="session-form">
            <input type="hidden" name="action" value="create_class_session">
            <input type="hidden" name="id_class" value="<?= $class->id ?>">

            <label for="session-date-<?= $class->id ?>">
                Data
                <input type="date" id="session-date-<?= $class->id ?>" name="session_date" required>
            </label>

            <label for="session-time-<?= $class->id ?>">
                Hora
                <input type="time" id="session-time-<?= $class->id ?>" name="session_time" required>
            </label>

            <button type="submit" class="btn-review-submit">Criar Sessão</button>
        </form>

        <button data-class-id= <?= $class->id ?> class='delete-class-btn btn-text'> Remover aula </button>
    </article>
<?php } ?>