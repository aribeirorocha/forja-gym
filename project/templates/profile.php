<?php function output_profile(array $user, array $weight_log = [], string $success_msg = '', string $error_msg = '', array $trainer_classes = [], array $trainer_reviews = [], array $trainer_review_stats = [], array $pt_bookings = [], array $trainers_list = []) {

$is_trainer = ($user['role'] ?? '') === 'trainer';
$is_admin   = ($user['role'] ?? '') === 'admin';

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
if (!$is_trainer && !$is_admin) {
    $membership        = $user['membership'] ?? 'basico';
    $membership_labels = ['basico' => 'Básico', 'normal' => 'Normal', 'vip' => 'VIP'];
    $membership_label  = $membership_labels[$membership] ?? ucfirst($membership);
    $membership_colors = ['basico' => 'badge-blue', 'normal' => 'badge-orange', 'vip' => 'badge-gold'];
    $membership_color  = $membership_colors[$membership] ?? 'badge-blue';
    $membership_prices = ['basico' => '19€/mês', 'normal' => '35€/mês', 'vip' => '59€/mês'];
    $membership_features = [
        'basico' => ['Acesso ao ginásio', '1 aula por semana', 'Balneários incluídos'],
        'normal' => ['Acesso ilimitado', '3 aulas por semana', '1 sessão PT/mês'],
        'vip'    => ['Acesso ilimitado 24h', 'Aulas ilimitadas', 'PT dedicado (4/mês)', 'Área premium'],
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
?>

<div class="profile-dashboard">

    <!-- ══ SIDEBAR ══════════════════════════════════════════════════════════ -->
    <aside class="profile-sidebar profile-card">
        <div class="profile-photo-wrapper">
            <img src="<?= $photo_path ?>" alt="Foto de Perfil" class="profile-photo-large"
                 onerror="this.src='<?= $avatar_fallback ?>'">
            <form action="profile.php" method="post" enctype="multipart/form-data" id="photo-upload-form">
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
        <?php else: ?>
            <span class="badge <?= $membership_color ?>"><?= $membership_label ?></span>
            <?php if ($latest_weight ?? false): ?>
                <p class="member-since">Peso atual<br><strong><?= number_format($latest_weight, 1) ?> kg</strong></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($member_since): ?>
            <p class="member-since">
                <?= $is_admin ? 'Administrador desde' : ($is_trainer ? 'Treinador desde' : 'Membro desde') ?><br>
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
        <details class="profile-card profile-details" open>
            <summary class="card-title card-summary">Dados Pessoais</summary>
            <form action="profile.php" method="post" class="details-body">
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
        <details class="profile-card profile-details" open>
            <summary class="card-title card-summary">Formação & Certificações</summary>
            <form action="profile.php" method="post" class="details-body">
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
                    <!-- Barra de média -->
                    <div class="trainer-rating-summary">
                        <span class="trainer-rating-big"><?= number_format($avg, 1) ?></span>
                        <div class="trainer-rating-stars-big">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="<?= $i <= round($avg) ? 'star-filled' : '' ?>">&#9733;</span>
                            <?php endfor; ?>
                            <span class="trainer-rating-total"><?= $count ?> <?= $count === 1 ? 'avaliação' : 'avaliações' ?></span>
                        </div>
                    </div>

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
        <details class="profile-card profile-details" <?= $pending_count > 0 ? 'open' : '' ?>>
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
                                    <input type="hidden" name="action" value="update_pt_status">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="new_status" value="Confirmado">
                                    <button type="submit" class="pt-btn pt-btn-confirm">Confirmar</button>
                                </form>
                                <form method="post" action="profile.php" style="display:inline">
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

        <?php else: ?>
        <!-- ════════════════════════════════════════════════════════════════
             SECÇÕES EXCLUSIVAS DO MEMBER (original, sem alterações)
             ════════════════════════════════════════════════════════════════ -->

        <details class="profile-card profile-details" open>
            <summary class="card-title card-summary">Registo de Peso</summary>
            <div class="details-body">
                <form action="profile.php" method="post" class="weight-form">
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

        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Dados Pessoais</summary>
            <form action="profile.php" method="post" class="details-body">
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

        <!-- Sessões PT -->
        <details class="profile-card profile-details">
            <summary class="card-title card-summary">Sessões de Personal Training</summary>
            <div class="details-body">

                <!-- Formulário de pedido -->
                <h4 class="card-subtitle" style="margin-bottom: 16px;">Pedir nova sessão</h4>
                <form method="post" action="profile.php">
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

<?php if (!$is_trainer && count($weight_log) >= 2): ?>
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