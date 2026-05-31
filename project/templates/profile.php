<?php function output_profile(array $user, array $weight_log = [], string $success_msg = '', string $error_msg = '', array $trainer_classes = [], array $trainer_reviews = [], array $trainer_review_stats = [], array $pt_bookings = [], array $trainers_list = [], array $nutri_appointments = [], $my_nutrition_plan = null, array $my_meals = [], array $nutritionists_list = [], array $my_appointments = [], array $my_classes = []) {

$is_trainer = ($user['role'] ?? '') === 'trainer';
$is_admin   = ($user['role'] ?? '') === 'admin';
$is_nutritionist = ($user['role'] ?? '') === 'nutritionist';

// ── Dados comuns ──────────────────────────────────────────────────────────────
$created_at   = $user['created_at'] ?? '';
$member_since = '';
if ($created_at) {
    $dt = new DateTime($created_at);
    $months_pt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $member_since = $dt->format('d') . ' de ' . $months_pt[intval($dt->format('m')) - 1] . ' de ' . $dt->format('Y');
}

$initial         = strtoupper(substr($user['name'], 0, 1));
$avatar_fallback = "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 150 150%22><circle cx=%2275%22 cy=%2275%22 r=%2275%22 fill=%22%23f27a38%22/><text x=%2275%22 y=%2295%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22 font-family=%22Arial%22>{$initial}</text></svg>";
$photo_path      = !empty($user['photo']) ? "../imgs/" . htmlspecialchars($user['photo']) : "../imgs/default-avatar.png";

// ── Dados só para members ─────────────────────────────────────────────────────
if (!$is_trainer && !$is_admin && !$is_nutritionist) {
    $membership        = $user['membership'] ?? 'basico';
    $membership_labels = ['basico' => 'Básico', 'normal' => 'Normal', 'vip' => 'VIP'];
    $membership_label  = $membership_labels[$membership] ?? ucfirst($membership);
    $membership_colors = ['basico' => 'badge-blue', 'normal' => 'badge-orange', 'vip' => 'badge-gold'];
    $membership_color  = $membership_colors[$membership] ?? 'badge-blue';
    $membership_prices = ['basico' => '19€/mês', 'normal' => '35€/mês', 'vip' => '59€/mês'];
    $membership_features = [
        'basico' => ['Acesso ao ginásio', '1 aula por semana', 'Balneários incluídos'],
        'normal' => ['Acesso ilimitado', '3 aulas por semana', '1 consulta nutrição/mês', '1 sessão PT/mês'],
        'vip'    => ['Acesso ilimitado 24h', 'Aulas ilimitadas', 'Nutrição ilimitada', 'PT dedicado (4/mês)', 'Área premium'],
    ];
    $latest_weight = !empty($weight_log) ? $weight_log[0]['weight_kg'] : null;
    $chart_labels  = [];
    $chart_data    = [];
    foreach (array_reverse($weight_log) as $entry) {
        $chart_labels[] = (new DateTime($entry['logged_at']))->format('d/m');
        $chart_data[]   = (float) $entry['weight_kg'];
    }
    $chart_labels_json = json_encode($chart_labels);
    $chart_data_json   = json_encode($chart_data);
}

// ── Dados do trainer_profile ──────────────────────────────────────────────────
if ($is_trainer) {
    $formation       = $user['formation']      ?? '';
    $certification   = $user['certification']  ?? '';
    $specialization  = $user['specialization'] ?? '';
    $location        = $user['location']       ?? '';
}

if ($is_nutritionist) {
    $specialization  = $user['specialization'] ?? '';
    $license_number  = $user['license_number'] ?? '';
}
?>

<div class="profile-dashboard">

    <!-- ══ SIDEBAR ══════════════════════════════════════════════════════════ -->
    <aside class="profile-sidebar profile-card">
        <div class="profile-photo-wrapper">
            <img src="<?= $photo_path ?>" alt="Foto de Perfil" class="profile-photo-large"
                 onerror="this.src='<?= $avatar_fallback ?>'">
            <form action="profile.php" method="post" enctype="multipart/form-data" id="photo-upload-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_photo">
                <label for="photo-upload" class="camera-icon" title="Mudar foto de perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M149.1 64.8L138.7 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H373.3L362.9 64.8C356.4 45.2 338.1 32 317.4 32H194.6c-20.7 0-39 13.2-45.5 32.8zM256 192a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                </label>
                <input type="file" id="photo-upload" name="profile_photo" accept="image/*" style="display:none;" onchange="document.getElementById('photo-upload-form').submit();">
            </form>
        </div>

        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <p class="profile-at">@<?= htmlspecialchars($user['username']) ?></p>

        <?php if ($is_admin): ?>
            <span class="badge" style="background:#1d1d1f;color:#fff;">Admin</span>
        <?php elseif ($is_trainer): ?>
            <span class="badge badge-trainer">Trainer</span>
            <?php if ($specialization): ?>
                <p class="trainer-specialization"><?= htmlspecialchars($specialization) ?></p>
            <?php endif; ?>
            <?php if (!empty($trainer_review_stats) && $trainer_review_stats['count'] > 0): ?>
                <div class="sidebar-rating">
                    <div class="sidebar-rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?= $i <= round($trainer_review_stats['avg']) ? 'star-filled' : 'star-empty' ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <span class="sidebar-rating-text">
                        <?= number_format($trainer_review_stats['avg'], 1) ?>
                        <span>(<?= $trainer_review_stats['count'] ?> <?= $trainer_review_stats['count'] === 1 ? 'avaliação' : 'avaliações' ?>)</span>
                    </span>
                </div>
            <?php endif; ?>
        <?php elseif ($is_nutritionist): ?>
            <span class="badge" style="background:#2ea043;color:#fff;">Nutricionista</span>
            <?php if ($specialization): ?>
                <p class="trainer-specialization"><?= htmlspecialchars($specialization) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <span class="badge <?= $membership_color ?>"><?= $membership_label ?></span>
            <?php if ($latest_weight ?? false): ?>
                <p class="member-since">Peso atual<br><strong><?= number_format($latest_weight, 1) ?> kg</strong></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($member_since): ?>
            <p class="member-since">
                <?= $is_admin ? 'Administrador desde' : ($is_trainer ? 'Treinador desde' : ($is_nutritionist ? 'Nutricionista desde' : 'Membro desde')) ?><br>
                <?= $member_since ?>
            </p>
        <?php endif; ?>

        <a href="logout.php" class="btn-logout">Terminar Sessão</a>
    </aside>

    <!-- ══ CONTEÚDO PRINCIPAL ════════════════════════════════════════════════ -->
    <section class="profile-content">

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
        <!-- ════════════════════════════════════════════════════════════════
             SECÇÕES EXCLUSIVAS DO ADMIN
             ════════════════════════════════════════════════════════════════ -->

        <!-- Dados Pessoais -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados Pessoais</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_details">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nome Completo</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <!-- Segurança -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Segurança</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_password">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Password Atual</label>
                        <input type="password" name="current_password" placeholder="Necessário para alterar">
                    </div>
                    <div class="form-group">
                        <label>Nova Password</label>
                        <input type="password" name="new_password" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Password</label>
                        <input type="password" name="confirm_password">
                    </div>
                </div>
                <button type="submit" class="btn-save">Atualizar Password</button>
            </form>
        </details>

        <?php elseif ($is_trainer): ?>
        <!-- ════════════════════════════════════════════════════════════════
             SECÇÕES EXCLUSIVAS DO TRAINER
             ════════════════════════════════════════════════════════════════ -->

        <!-- Formação Académica & Certificações -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Formação & Certificações</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_trainer_profile">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Formação Académica</label>
                        <input type="text" name="formation"
                               value="<?= htmlspecialchars($formation) ?>"
                               placeholder="ex: Licenciatura em Ciências do Desporto – FADEUP">
                    </div>
                    <div class="form-group full-width">
                        <label>Certificações</label>
                        <input type="text" name="certification"
                               value="<?= htmlspecialchars($certification) ?>"
                               placeholder="ex: CrossFit Level 1, Kettlebell Instructor">
                    </div>
                    <div class="form-group">
                        <label>Especialização</label>
                        <input type="text" name="specialization"
                               value="<?= htmlspecialchars($specialization) ?>"
                               placeholder="ex: HIIT, Força">
                    </div>
                    <div class="form-group">
                        <label>Localização</label>
                        <input type="text" name="location"
                               value="<?= htmlspecialchars($location) ?>"
                               placeholder="ex: Porto, Portugal">
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <!-- Dados Pessoais (conta) -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados da Conta</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_details">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nome Completo</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <!-- Segurança -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Segurança</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_password">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Password Atual</label>
                        <input type="password" name="current_password" placeholder="Necessário para alterar">
                    </div>
                    <div class="form-group">
                        <label>Nova Password</label>
                        <input type="password" name="new_password" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Password</label>
                        <input type="password" name="confirm_password">
                    </div>
                </div>
                <button type="submit" class="btn-save">Atualizar Password</button>
            </form>
        </details>

        <!-- As minhas Aulas -->
        <?php if (!empty($trainer_classes)): ?>
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">As minhas Aulas</summary>
            <div class="details-body">
                <div class="trainer-classes-list">
                    <?php foreach ($trainer_classes as $class):
                        $next = null;
                        if (!empty($class['next_session'])) {
                            $next = new DateTime($class['next_session']);
                            $days_pt = ['Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'];
                            $day_label = $days_pt[intval($next->format('N')) - 1];
                        }
                    ?>
                    <div class="trainer-class-item">
                        <div class="trainer-class-info">
                            <span class="trainer-class-name"><?= htmlspecialchars($class['name']) ?></span>
                            <span class="trainer-class-meta">
                                <?= htmlspecialchars($class['duration_minutes']) ?> min
                                &bull; <?= htmlspecialchars($class['local']) ?>
                                &bull; <?= $class['capacity'] ?> lugares
                            </span>
                            <?php if ($next): ?>
                                <span class="trainer-class-schedule">
                                    <?= $day_label ?>, <?= $next->format('d/m') ?> às <?= $next->format('H:i') ?>
                                </span>
                                <?php if (!empty($class['enrolled_members'])): ?>
                                    <div style="margin-top: 12px;">
                                        <strong style="font-size: 0.85rem; color: var(--text-light); display: block; margin-bottom: 6px;">
                                            Membros Inscritos (<?= count($class['enrolled_members']) ?>/<?= $class['capacity'] ?>):
                                        </strong>
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php foreach ($class['enrolled_members'] as $m): 
                                            $m_photo = !empty($m['photo']) ? "../imgs/" . htmlspecialchars($m['photo']) : "https://ui-avatars.com/api/?name=" . urlencode($m['name']) . "&background=f27a38&color=fff";
                                        ?>
                                            <div style="display: flex; align-items: center; gap: 6px; background: var(--bg-alt); padding: 4px 10px 4px 4px; border-radius: 20px; border: 1px solid var(--border-color);">
                                                <img src="<?= $m_photo ?>" alt="<?= htmlspecialchars($m['name']) ?>" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                                                <span style="font-size: 0.85rem; color: var(--text-color);"><?= htmlspecialchars($m['name']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="trainer-class-schedule" style="margin-top: 5px; color: var(--text-light); font-size: 0.85rem;">Sem inscrições para a próxima sessão</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="trainer-class-schedule trainer-class-no-session">Sem sessão agendada</span>
                            <?php endif; ?>
                        </div>
                        <span class="trainer-class-type"><?= htmlspecialchars($class['type']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </details>
        <?php endif; ?>

        <!-- Reviews recebidas -->
        <?php
        $avg   = $trainer_review_stats['avg']   ?? 0;
        $count = $trainer_review_stats['count'] ?? 0;
        ?>
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">
                Avaliações
                <?php if ($count > 0): ?>
                    <span class="trainer-rating-inline">
                        <span class="star-icon">&#9733;</span>
                        <?= number_format($avg, 1) ?>
                        <span class="review-count-inline">(<?= $count ?>)</span>
                    </span>
                <?php endif; ?>
            </summary>
            <div class="details-body">
                <?php if (empty($trainer_reviews)): ?>
                    <p class="weight-hint">Ainda não tens avaliações.</p>
                <?php else: ?>


                    <div class="reviews-list" style="margin-top: 20px;">
                        <?php foreach ($trainer_reviews as $rev):
                            $rev_photo = !empty($rev['photo'])
                                ? "../imgs/" . htmlspecialchars($rev['photo'])
                                : "https://ui-avatars.com/api/?name=" . urlencode($rev['name']) . "&background=f27a38&color=fff";
                            $rev_date = date('d/m/Y \à\s H:i', strtotime($rev['created_at']));
                        ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-author">
                                    <img src="<?= $rev_photo ?>" alt="Membro" class="review-author-photo"
                                         onerror="this.src='https://ui-avatars.com/api/?name=U&background=f27a38&color=fff'">
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
                            <?php if (!empty($rev['comment'])): ?>
                                <p class="review-text"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>

        <!-- Pedidos de PT recebidos -->
        <?php
        $pending   = array_filter($pt_bookings, fn($b) => $b['status'] === 'Pendente');
        $confirmed = array_filter($pt_bookings, fn($b) => $b['status'] === 'Confirmado');
        $cancelled = array_filter($pt_bookings, fn($b) => $b['status'] === 'Cancelado');
        $pending_count = count($pending);
        ?>
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">
                Pedidos de PT
                <?php if ($pending_count > 0): ?>
                    <span class="pt-pending-badge"><?= $pending_count ?> pendente<?= $pending_count > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </summary>
            <div class="details-body">
                <?php if (empty($pt_bookings)): ?>
                    <p class="weight-hint">Ainda não tens pedidos de sessão PT.</p>
                <?php else: ?>

                    <?php foreach ([['Pendente', $pending], ['Confirmado', $confirmed], ['Cancelado', $cancelled]] as [$label, $group]):
                        if (empty($group)) continue; ?>
                    <h4 class="card-subtitle pt-group-title pt-group-<?= strtolower($label) ?>"><?= $label ?>s</h4>
                    <div class="pt-bookings-list">
                        <?php foreach ($group as $b):
                            $bdate = (new DateTime($b['booking_date']))->format('d/m/Y');
                            $member_photo = !empty($b['member_photo'])
                                ? "../imgs/" . htmlspecialchars($b['member_photo'])
                                : "https://ui-avatars.com/api/?name=" . urlencode($b['member_name']) . "&background=f27a38&color=fff";
                        ?>
                        <div class="pt-booking-card pt-status-<?= strtolower($b['status']) ?>">
                            <div class="pt-booking-header">
                                <div class="pt-booking-who">
                                    <img src="<?= $member_photo ?>" alt="Membro" class="pt-booking-photo"
                                         onerror="this.src='https://ui-avatars.com/api/?name=M&background=f27a38&color=fff'">
                                    <div>
                                        <span class="pt-booking-name"><?= htmlspecialchars($b['member_name']) ?></span>
                                        <span class="pt-booking-datetime"><?= $bdate ?> às <?= $b['start_time'] ?> – <?= $b['end_time'] ?></span>
                                    </div>
                                </div>
                                <span class="pt-status-tag pt-status-tag-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span>
                            </div>
                            <?php if (!empty($b['notes'])): ?>
                                <p class="pt-booking-notes"><?= nl2br(htmlspecialchars($b['notes'])) ?></p>
                            <?php endif; ?>
                            <?php if ($b['status'] === 'Pendente'): ?>
                            <div class="pt-booking-actions">
                                <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                    <input type="hidden" name="action" value="update_pt_status">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="new_status" value="Confirmado">
                                    <button type="submit" class="pt-btn pt-btn-confirm">Confirmar</button>
                                </form>
                                <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                    <input type="hidden" name="action" value="update_pt_status">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="new_status" value="Cancelado">
                                    <button type="submit" class="pt-btn pt-btn-cancel">Recusar</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>
        </details>

        <?php elseif ($is_nutritionist): ?>
     
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados Profissionais</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_nutritionist_profile">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Especialização</label>
                        <input type="text" name="specialization"
                               value="<?= htmlspecialchars($specialization) ?>"
                               placeholder="ex: Nutrição Desportiva, Emagrecimento">
                    </div>
                    <div class="form-group full-width">
                        <label>Cédula Profissional</label>
                        <input type="text" name="license_number"
                               value="<?= htmlspecialchars($license_number) ?>"
                               placeholder="ex: 1234N">
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <!-- Dados Pessoais (conta) -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados da Conta</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_details">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nome Completo</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <!-- Segurança -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Segurança</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_password">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Password Atual</label>
                        <input type="password" name="current_password" placeholder="Necessário para alterar">
                    </div>
                    <div class="form-group">
                        <label>Nova Password</label>
                        <input type="password" name="new_password" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Password</label>
                        <input type="password" name="confirm_password">
                    </div>
                </div>
                <button type="submit" class="btn-save">Atualizar Password</button>
            </form>
        </details>

        
        <?php
        $pending_nutri   = array_filter($nutri_appointments, fn($a) => $a['status'] === 'pendente');
        $confirmed_nutri = array_filter($nutri_appointments, fn($a) => $a['status'] === 'confirmada');
        $cancelled_nutri = array_filter($nutri_appointments, fn($a) => $a['status'] === 'cancelada');
        $pending_nutri_count = count($pending_nutri);
        ?>
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">
                Pedidos de Consulta
                <?php if ($pending_nutri_count > 0): ?>
                    <span class="pt-pending-badge"><?= $pending_nutri_count ?> pendente<?= $pending_nutri_count > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </summary>
            <div class="details-body">
                <?php if (empty($nutri_appointments)): ?>
                    <p class="weight-hint">Ainda não tens pedidos de consulta.</p>
                <?php else: ?>

                    <?php foreach ([['Pendente', $pending_nutri], ['Confirmada', $confirmed_nutri], ['Cancelada', $cancelled_nutri]] as [$label, $group]):
                        if (empty($group)) continue; ?>
                    <h4 class="card-subtitle pt-group-title pt-group-<?= strtolower(substr($label, 0, 7)) ?>"><?= $label ?>s</h4>
                    <div class="pt-bookings-list">
                        <?php foreach ($group as $a):
                            $adate = (new DateTime($a['appointment_date']))->format('d/m/Y \à\s H:i');
                            $member_photo = !empty($a['member_photo'])
                                ? "../imgs/" . htmlspecialchars($a['member_photo'])
                                : "https://ui-avatars.com/api/?name=" . urlencode($a['member_name']) . "&background=2ea043&color=fff";
                        ?>
                        <div class="pt-booking-card pt-status-<?= strtolower($a['status']) ?>">
                            <div class="pt-booking-header">
                                <div class="pt-booking-who">
                                    <img src="<?= $member_photo ?>" alt="Membro" class="pt-booking-photo"
                                         onerror="this.src='https://ui-avatars.com/api/?name=M&background=2ea043&color=fff'">
                                    <div>
                                        <span class="pt-booking-name"><?= htmlspecialchars($a['member_name']) ?></span>
                                        <span class="pt-booking-datetime"><?= $adate ?></span>
                                    </div>
                                </div>
                                <span class="pt-status-tag pt-status-tag-<?= strtolower($a['status']) ?>"><?= ucfirst($a['status']) ?></span>
                            </div>
                            <?php if ($a['status'] === 'pendente'): ?>
                            <div class="pt-booking-actions">
                                <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                    <input type="hidden" name="action" value="update_appointment_status">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <input type="hidden" name="new_status" value="confirmada">
                                    <button type="submit" class="pt-btn pt-btn-confirm">Confirmar</button>
                                </form>
                                <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                    <input type="hidden" name="action" value="update_appointment_status">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <input type="hidden" name="new_status" value="cancelada">
                                    <button type="submit" class="pt-btn pt-btn-cancel">Recusar</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>
        </details>

        <?php else: ?>
        <!-- ════════════════════════════════════════════════════════════════
             SECÇÕES EXCLUSIVAS DO MEMBER (original, sem alterações)
             ════════════════════════════════════════════════════════════════ -->

        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Registo de Peso</summary>
            <div class="details-body">
                <form action="profile.php" method="post" class="weight-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                    <input type="hidden" name="action" value="log_weight">
                    <div class="weight-form-row">
                        <div class="form-group" style="margin-bottom:0; flex:1;">
                            <label>Peso (kg)</label>
                            <input type="number" name="weight_kg" step="0.1" min="20" max="400"
                                   placeholder="ex: 75.5" required
                                   value="<?= ($latest_weight ?? false) ? number_format($latest_weight, 1, '.', '') : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom:0; flex:1;">
                            <label>Data</label>
                            <input type="date" name="logged_at" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom:0; flex:2;">
                            <label>Notas (opcional)</label>
                            <input type="text" name="notes" placeholder="ex: após treino, em jejum...">
                        </div>
                        <button type="submit" class="btn-save" style="align-self:flex-end; margin-top:0;">Registar</button>
                    </div>
                </form>

                <?php if (count($weight_log) >= 2): ?>
                <div class="weight-chart-wrapper">
                    <canvas id="weightChart"></canvas>
                </div>
                <?php elseif (!empty($weight_log)): ?>
                    <p class="weight-hint">Adiciona pelo menos 2 registos para ver o gráfico.</p>
                <?php else: ?>
                    <p class="weight-hint">Ainda não tens registos de peso. Começa hoje!</p>
                <?php endif; ?>

                <?php if (!empty($weight_log)): ?>
                <div class="weight-history">
                    <h4 class="card-subtitle" style="margin-bottom:12px;">Histórico</h4>
                    <table class="weight-table">
                        <thead>
                            <tr>
                                <th>Data</th><th>Peso</th><th>Variação</th><th>Notas</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($weight_log as $i => $entry):
                                $prev       = $weight_log[$i + 1]['weight_kg'] ?? null;
                                $diff       = $prev !== null ? round($entry['weight_kg'] - $prev, 1) : null;
                                $diff_class = $diff === null ? '' : ($diff < 0 ? 'diff-down' : ($diff > 0 ? 'diff-up' : 'diff-same'));
                                $diff_str   = $diff === null ? '—' : ($diff > 0 ? "+{$diff}" : "{$diff}") . ' kg';
                            ?>
                            <tr>
                                <td><?= (new DateTime($entry['logged_at']))->format('d/m/Y') ?></td>
                                <td><strong><?= number_format($entry['weight_kg'], 1) ?> kg</strong></td>
                                <td><span class="weight-diff <?= $diff_class ?>"><?= $diff_str ?></span></td>
                                <td class="weight-notes"><?= htmlspecialchars($entry['notes'] ?? '') ?></td>
                                <td>
                                    <form action="profile.php" method="post" style="margin:0">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                        <input type="hidden" name="action" value="delete_weight">
                                        <input type="hidden" name="weight_id" value="<?= $entry['id'] ?>">
                                        <button type="submit" class="btn-delete-weight" title="Eliminar">×</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </details>

        <!-- O Meu Plano de Nutrição -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">O Meu Plano de Nutrição</summary>
            <div class="details-body">
                <?php if ($my_nutrition_plan): ?>
                    <div style="background: var(--bg-elevated, #f9f9f9); padding: 20px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid var(--primary-color, #2ea043);">
                        <h4 style="margin: 0 0 8px 0; color: var(--text-main); font-size: 1.1rem;">Objetivo: <?= htmlspecialchars($my_nutrition_plan['goal']) ?></h4>
                        <p style="margin: 0 0 12px 0; color: var(--text-muted); font-size: 0.9rem;">
                            <strong>Nutricionista:</strong> <?= htmlspecialchars($my_nutrition_plan['nutritionist_name']) ?> <br>
                            <strong>Início:</strong> <?= (new DateTime($my_nutrition_plan['start_date']))->format('d/m/Y') ?>
                            <?= $my_nutrition_plan['end_date'] ? '— <strong>Fim:</strong> ' . (new DateTime($my_nutrition_plan['end_date']))->format('d/m/Y') : '' ?>
                        </p>
                        <?php if (!empty($my_nutrition_plan['notes'])): ?>
                            <p style="margin: 0; padding-top: 12px; border-top: 1px solid var(--border-color, #e0e0e0); color: var(--text-main); font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($my_nutrition_plan['notes']) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php 
                    $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                    $meals_by_day = [];
                    foreach ($days as $d) $meals_by_day[$d] = [];
                    foreach ($my_meals as $m) $meals_by_day[$m['day_of_week']][] = $m;
                    ?>

                    <?php if (empty($my_meals)): ?>
                        <p class="weight-hint">Ainda não há refeições adicionadas ao teu plano.</p>
                    <?php else: ?>
                        <div class="meals-schedule" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                            <?php foreach ($days as $day): ?>
                                <?php if (!empty($meals_by_day[$day])): ?>
                                    <div class="day-card" style="background: var(--bg-card, #fff); border: 1px solid var(--border-color, #e0e0e0); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                        <div class="day-header" style="background: var(--bg-elevated, #f0f0f5); padding: 12px 16px; font-weight: 700; color: var(--text-main); border-bottom: 1px solid var(--border-color, #e0e0e0);">
                                            <?= $day ?>
                                        </div>
                                        <div class="day-meals" style="padding: 16px;">
                                            <?php foreach ($meals_by_day[$day] as $meal): ?>
                                                <div class="meal-block" style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px dashed var(--border-color, #e0e0e0);">
                                                    <strong style="color: var(--primary-color, #2ea043); display: block; margin-bottom: 4px;"><?= htmlspecialchars($meal['meal_type']) ?></strong>
                                                    <p style="margin: 0; color: var(--text-main); font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($meal['description']) ?></p>
                                                    <div style="margin-top: 8px; font-size: 0.8rem; color: var(--text-muted); display: flex; gap: 8px; flex-wrap: wrap;">
                                                        <?php if ($meal['calories']): ?><span style="background: var(--bg-elevated, #f0f0f5); padding: 2px 6px; border-radius: 4px;"><?= $meal['calories'] ?> kcal</span><?php endif; ?>
                                                        <?php if ($meal['protein_g']): ?><span style="background: var(--bg-elevated, #f0f0f5); padding: 2px 6px; border-radius: 4px;">P: <?= $meal['protein_g'] ?>g</span><?php endif; ?>
                                                        <?php if ($meal['carbs_g']): ?><span style="background: var(--bg-elevated, #f0f0f5); padding: 2px 6px; border-radius: 4px;">C: <?= $meal['carbs_g'] ?>g</span><?php endif; ?>
                                                        <?php if ($meal['fat_g']): ?><span style="background: var(--bg-elevated, #f0f0f5); padding: 2px 6px; border-radius: 4px;">G: <?= $meal['fat_g'] ?>g</span><?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="weight-hint">Não tens nenhum plano de nutrição ativo neste momento. Marca uma consulta com um dos nossos nutricionistas!</p>
                <?php endif; ?>
            </div>
        </details>

        <!-- Consultas de Nutrição -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Consultas de Nutrição</summary>
            <div class="details-body">

                <!-- Formulário de pedido -->
                <h4 class="card-subtitle" style="margin-bottom: 16px;">Pedir nova consulta</h4>
                <form method="post" action="profile.php">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                    <input type="hidden" name="action" value="book_nutrition">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Nutricionista</label>
                            <select name="nutritionist_id" class="filter-select" style="border-radius: var(--radius-sm); min-width:unset; width:100%; padding: 12px 16px;" required>
                                <option value="">Escolhe um nutricionista...</option>
                                <?php foreach ($nutritionists_list as $n): ?>
                                    <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['name']) ?> — <?= htmlspecialchars($n['specialization']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data</label>
                            <input type="date" name="appointment_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Hora</label>
                            <input type="time" name="appointment_time" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Enviar Pedido</button>
                </form>

                <!-- Histórico de consultas -->
                <?php if (!empty($my_appointments)): ?>
                <h4 class="card-subtitle" style="margin-top: 30px; margin-bottom: 16px;">Os meus pedidos</h4>
                <div class="pt-bookings-list">
                    <?php foreach ($my_appointments as $a):
                        $datetime_obj = new DateTime($a['appointment_date']);
                        $adate = $datetime_obj->format('d/m/Y');
                        $atime = $datetime_obj->format('H:i');
                        $nutri_photo = !empty($a['nutritionist_photo'])
                            ? "../imgs/" . htmlspecialchars($a['nutritionist_photo'])
                            : "https://ui-avatars.com/api/?name=" . urlencode($a['nutritionist_name']) . "&background=f27a38&color=fff";
                    ?>
                    <div class="pt-booking-card pt-status-<?= strtolower($a['status']) ?>">
                        <div class="pt-booking-header">
                            <div class="pt-booking-who">
                                <img src="<?= $nutri_photo ?>" alt="Nutricionista" class="pt-booking-photo"
                                     onerror="this.src='https://ui-avatars.com/api/?name=N&background=f27a38&color=fff'">
                                <div>
                                    <span class="pt-booking-name"><?= htmlspecialchars($a['nutritionist_name']) ?></span>
                                    <span class="pt-booking-datetime"><?= $adate ?> às <?= $atime ?></span>
                                </div>
                            </div>
                            <span class="pt-status-tag pt-status-tag-<?= strtolower($a['status']) ?>"><?= ucfirst($a['status']) ?></span>
                        </div>
                        <?php if ($a['status'] === 'pendente'): ?>
                        <div class="pt-booking-actions">
                            <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                <input type="hidden" name="action" value="cancel_nutrition">
                                <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                <button type="submit" class="pt-btn pt-btn-cancel">Cancelar pedido</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </details>

        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados Pessoais</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_details">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nome Completo</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-save">Guardar Alterações</button>
            </form>
        </details>

        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Segurança</summary>
            <form action="profile.php" method="post" class="details-body">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_password">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Password Atual</label>
                        <input type="password" name="current_password" placeholder="Necessário para alterar">
                    </div>
                    <div class="form-group">
                        <label>Nova Password</label>
                        <input type="password" name="new_password" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Password</label>
                        <input type="password" name="confirm_password">
                    </div>
                </div>
                <button type="submit" class="btn-save">Atualizar Password</button>
            </form>
        </details>

        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Plano de Membership</summary>
            <div class="details-body">
                <div class="current-plan">
                    <div class="current-plan-info">
                        <span class="badge <?= $membership_color ?>"><?= $membership_label ?></span>
                        <span class="current-plan-price"><?= $membership_prices[$membership] ?></span>
                    </div>
                    <ul class="current-plan-features">
                        <?php foreach ($membership_features[$membership] as $f): ?>
                            <li>✓ <?= $f ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <h4 class="card-subtitle">Mudar de Plano</h4>
                <form action="profile.php" method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                    <input type="hidden" name="action" value="update_membership">
                    <div class="plans-change-grid">
                        <?php foreach (['basico' => '19€', 'normal' => '35€', 'vip' => '59€'] as $plan => $price): ?>
                            <label class="plan-change-card <?= $membership === $plan ? 'plan-current' : '' ?>" for="change-<?= $plan ?>">
                                <input type="radio" name="membership" id="change-<?= $plan ?>" value="<?= $plan ?>" <?= $membership === $plan ? 'checked' : '' ?>>
                                <div class="plan-change-inner">
                                    <span class="plan-change-name"><?= $membership_labels[$plan] ?></span>
                                    <span class="plan-change-price"><?= $price ?><small>/mês</small></span>
                                    <?php if ($membership === $plan): ?><span class="plan-active-tag">Atual</span><?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn-save">Atualizar Plano</button>
                </form>
            </div>
        </details>

        <!-- Aulas Inscritas -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Aulas de Grupo Inscritas</summary>
            <div class="details-body">
                <?php if (empty($my_classes)): ?>
                    <p class="empty-state">Ainda não te inscreveste em nenhuma aula.</p>
                <?php else: ?>
                    <div class="reviews-list">
                        <?php foreach ($my_classes as $c): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <strong><a href="class.php?id=<?= $c['id'] ?>" style="color: var(--primary); text-decoration: none;"><?= htmlspecialchars($c['name']) ?></a></strong>
                                        <span class="review-date">Tipo: <?= htmlspecialchars(ucfirst($c['type'])) ?></span>
                                    </div>
                                    <div class="review-rating" style="font-size: 0.9rem; font-weight: 500;">
                                        <?= date('d/m/Y H:i', strtotime($c['session_datetime'])) ?>
                                    </div>
                                </div>
                                <div class="review-body" style="display: flex; justify-content: space-between; align-items: center;">
                                    <p style="margin-bottom: 0; font-size: 0.9rem; color: var(--text-light);"><strong>Treinador:</strong> <?= htmlspecialchars($c['trainer_name']) ?></p>
                                    <?php if (strtotime($c['session_datetime']) > time()): ?>
                                    <form method="post" action="profile.php" style="margin: 0;">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                        <input type="hidden" name="action" value="unenroll_class">
                                        <input type="hidden" name="session_id" value="<?= $c['session_id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: #d32f2f; text-decoration: underline; cursor: pointer; padding: 0; font-size: 0.85rem;">Desinscrever</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>

        <!-- Sessões PT -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Sessões de Personal Training</summary>
            <div class="details-body">

                <!-- Formulário de pedido -->
                <h4 class="card-subtitle" style="margin-bottom: 16px;">Pedir nova sessão</h4>
                <form method="post" action="profile.php">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                    <input type="hidden" name="action" value="book_pt">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Trainer</label>
                            <select name="trainer_id" class="filter-select" style="border-radius: var(--radius-sm); min-width:unset; width:100%; padding: 12px 16px;" required>
                                <option value="">Escolhe um trainer...</option>
                                <?php foreach ($trainers_list as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> — <?= htmlspecialchars($t['specialization']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data</label>
                            <input type="date" name="booking_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Hora</label>
                            <input type="time" name="booking_time" required>
                        </div>
                        <div class="form-group full-width">
                            <label>Notas (opcional)</label>
                            <input type="text" name="notes" placeholder="Objetivos, lesões, preferências...">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Enviar Pedido</button>
                </form>

                <!-- Histórico de pedidos -->
                <?php if (!empty($pt_bookings)): ?>
                <h4 class="card-subtitle" style="margin-top: 30px; margin-bottom: 16px;">Os meus pedidos</h4>
                <div class="pt-bookings-list">
                    <?php foreach ($pt_bookings as $b):
                        $bdate = (new DateTime($b['booking_date']))->format('d/m/Y');
                        $trainer_photo = !empty($b['trainer_photo'])
                            ? "../imgs/" . htmlspecialchars($b['trainer_photo'])
                            : "https://ui-avatars.com/api/?name=" . urlencode($b['trainer_name']) . "&background=f27a38&color=fff";
                    ?>
                    <div class="pt-booking-card pt-status-<?= strtolower($b['status']) ?>">
                        <div class="pt-booking-header">
                            <div class="pt-booking-who">
                                <img src="<?= $trainer_photo ?>" alt="Trainer" class="pt-booking-photo"
                                     onerror="this.src='https://ui-avatars.com/api/?name=T&background=f27a38&color=fff'">
                                <div>
                                    <span class="pt-booking-name"><?= htmlspecialchars($b['trainer_name']) ?></span>
                                    <span class="pt-booking-datetime"><?= $bdate ?> às <?= $b['start_time'] ?> – <?= $b['end_time'] ?></span>
                                </div>
                            </div>
                            <span class="pt-status-tag pt-status-tag-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span>
                        </div>
                        <?php if (!empty($b['notes'])): ?>
                            <p class="pt-booking-notes"><?= nl2br(htmlspecialchars($b['notes'])) ?></p>
                        <?php endif; ?>
                        <?php if ($b['status'] === 'Pendente'): ?>
                        <div class="pt-booking-actions">
                            <form method="post" action="profile.php" style="display:inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                                <input type="hidden" name="action" value="cancel_pt">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="pt-btn pt-btn-cancel">Cancelar pedido</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </details>

        <?php endif; // fim is_admin / is_trainer ?>

    </section>
</div>

<?php if (!$is_trainer && !$is_nutritionist && count($weight_log) >= 2): ?>
<script>
(function() {
    const labels = <?= $chart_labels_json ?>;
    const data   = <?= $chart_data_json ?>;
    const canvas = document.getElementById('weightChart');
    const ctx    = canvas.getContext('2d');
    const W = canvas.offsetWidth || 700;
    const H = 220;
    canvas.width  = W;
    canvas.height = H;
    const PAD = { top: 20, right: 20, bottom: 40, left: 50 };
    const plotW = W - PAD.left - PAD.right;
    const plotH = H - PAD.top  - PAD.bottom;
    const minV = Math.min(...data) - 2;
    const maxV = Math.max(...data) + 2;
    function xOf(i) { return PAD.left + (i / (data.length - 1)) * plotW; }
    function yOf(v) { return PAD.top  + plotH - ((v - minV) / (maxV - minV)) * plotH; }
    const orange = '#f27a38', muted = '#86868b', border = '#d2d2d7';
    const steps = 4;
    const grad = ctx.createLinearGradient(0, PAD.top, 0, H - PAD.bottom);
    grad.addColorStop(0, 'rgba(242,122,56,0.18)');
    grad.addColorStop(1, 'rgba(242,122,56,0)');
    function drawChart() {
        ctx.clearRect(0, 0, W, H);
        ctx.strokeStyle = border; ctx.lineWidth = 1;
        for (let s = 0; s <= steps; s++) {
            const v = minV + ((maxV - minV) / steps) * s;
            const y = yOf(v);
            ctx.beginPath(); ctx.moveTo(PAD.left, y); ctx.lineTo(W - PAD.right, y); ctx.stroke();
            ctx.fillStyle = muted; ctx.font = '11px Segoe UI, sans-serif'; ctx.textAlign = 'right';
            ctx.fillText(v.toFixed(1), PAD.left - 8, y + 4);
        }
        ctx.fillStyle = muted; ctx.font = '11px Segoe UI, sans-serif'; ctx.textAlign = 'center';
        data.forEach((_, i) => { if (data.length <= 12 || i % Math.ceil(data.length / 10) === 0) ctx.fillText(labels[i], xOf(i), H - 10); });
        ctx.beginPath(); ctx.moveTo(xOf(0), yOf(data[0]));
        for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
        ctx.lineTo(xOf(data.length - 1), H - PAD.bottom); ctx.lineTo(xOf(0), H - PAD.bottom);
        ctx.closePath(); ctx.fillStyle = grad; ctx.fill();
        ctx.beginPath(); ctx.moveTo(xOf(0), yOf(data[0]));
        for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
        ctx.strokeStyle = orange; ctx.lineWidth = 2.5; ctx.lineJoin = 'round'; ctx.stroke();
        data.forEach((v, i) => {
            ctx.beginPath(); ctx.arc(xOf(i), yOf(v), 4, 0, Math.PI * 2);
            ctx.fillStyle = '#fff'; ctx.fill(); ctx.strokeStyle = orange; ctx.lineWidth = 2; ctx.stroke();
        });
    }
    drawChart();
    canvas.addEventListener('mousemove', function(e) {
        const rect = canvas.getBoundingClientRect();
        const mx = (e.clientX - rect.left) * (W / rect.width);
        let closest = 0, minDist = Infinity;
        data.forEach((_, i) => { const d = Math.abs(xOf(i) - mx); if (d < minDist) { minDist = d; closest = i; } });
        canvas.width = W; drawChart();
        const tx = xOf(closest), ty = yOf(data[closest]);
        const tip = `${labels[closest]}: ${data[closest].toFixed(1)} kg`;
        ctx.font = 'bold 12px Segoe UI, sans-serif';
        const tw = ctx.measureText(tip).width + 16;
        let bx = tx - tw / 2;
        if (bx < 4) bx = 4;
        if (bx + tw > W - 4) bx = W - tw - 4;
        ctx.fillStyle = '#1d1d1f'; ctx.beginPath(); ctx.roundRect(bx, ty - 36, tw, 24, 6); ctx.fill();
        ctx.fillStyle = '#fff'; ctx.textAlign = 'left'; ctx.fillText(tip, bx + 8, ty - 20);
        ctx.beginPath(); ctx.arc(tx, ty, 6, 0, Math.PI * 2); ctx.fillStyle = orange; ctx.fill();
    });
    canvas.addEventListener('mouseleave', drawChart);
})();
</script>
<?php endif; ?>

<?php } ?>