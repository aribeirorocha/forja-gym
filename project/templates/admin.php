<?php require_once('../database/user.php') ?>

<?php function output_admin_panel(array $users, int $page, string $username, array $all_classes = [], array $all_trainers = [], array $all_nutritionists = [], array $all_pt_bookings = [], array $stats = []) { ?>

<section class="admin-dashboard">
    <header class="admin-header">
        <p class="section-tag">ADMIN</p>
        <h1>Painel de Administrador</h1>
        <p>Gestão completa do ginásio: utilizadores, aulas, trainers, equipamentos e PT.</p>
    </header>

    <!-- ══ TABS ══ -->
    <div class="admin-tabs">
        <button class="admin-tab active" data-tab="users">Utilizadores</button>
        <button class="admin-tab" data-tab="classes">Aulas</button>
        <button class="admin-tab" data-tab="trainers">Trainers</button>
        <button class="admin-tab" data-tab="nutritionists">Nutricionistas</button>
        <button class="admin-tab" data-tab="pt">Sessões PT</button>
        <button class="admin-tab" data-tab="equipment">Equipamentos</button>
        <button class="admin-tab" data-tab="stats">Estatísticas</button>
    </div>

    <!-- ══ TAB: UTILIZADORES ══ -->
    <section class="admin-tab-content active profile-card" id="tab-users">
        <header class="admin-toolbar">
            <form action="admin.php" method="get" class="admin-search">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <fieldset>
                    <label for="admin-username-search">
                        Username
                        <input type="search" id="admin-username-search" name="username"
                               placeholder="Procurar por username" value="<?= htmlspecialchars($username) ?>">
                    </label>
                    <input type="hidden" name="tab" value="users">
                    <button type="submit" class="btn-save">Pesquisar</button>
                </fieldset>
            </form>
            <nav class="admin-pagination">
                <button type="submit" form="admin-prev-page" class="btn-text" <?= $page == 1 ? 'disabled' : '' ?>>Anterior</button>
                <span class="admin-page-indicator">Página <?= $page ?></span>
                <button type="submit" form="admin-next-page" class="btn-text" <?= count($users) < 10 ? 'disabled' : '' ?>>Seguinte</button>
            </nav>
        </header>

        <form action="admin.php" method="get" id="admin-prev-page">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="page" value="<?= $page - 1 ?>">
            <input type="hidden" name="tab" value="users">
            <?php if ($username !== ''): ?>
                <input type="hidden" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>">
            <?php endif; ?>
        </form>
        <form action="admin.php" method="get" id="admin-next-page">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="page" value="<?= $page + 1 ?>">
            <input type="hidden" name="tab" value="users">
            <?php if ($username !== ''): ?>
                <input type="hidden" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>">
            <?php endif; ?>
        </form>

        <div class="admin-table-wrap">
            <h2 class="card-title">Utilizadores</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Data de Registo</th>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Email</th>
                        <th>Membership</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) {
                        output_user_row($user->id, $user->createdAt->format('Y/m/d, H:i'), $user->username, $user->name, $user->role, $user->email, $user->membership ?? 'basico');
                    } ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ══ TAB: AULAS ══ -->
    <section class="admin-tab-content profile-card" id="tab-classes">
        <h2 class="card-title">Todas as Aulas</h2>
        <?php if (empty($all_classes)): ?>
            <p class="weight-hint">Não há aulas registadas.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Trainer</th>
                        <th>Próxima Sessão</th>
                        <th>Capacidade</th>
                        <th>Local</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_classes as $c): ?>
                    <tr>
                        <td><a href="class.php?id=<?= $c['id'] ?>" class="link-orange"><?= htmlspecialchars($c['name']) ?></a></td>
                        <td><span class="class-type-pill-sm"><?= htmlspecialchars($c['type']) ?></span></td>
                        <td><?= htmlspecialchars($c['trainer_name'] ?? '—') ?></td>
                        <td><?= $c['next_session'] ? date('d/m/Y H:i', strtotime($c['next_session'])) : '<span class="text-muted">Sem sessão</span>' ?></td>
                        <td><?= $c['enrolled'] ?? 0 ?>/<?= $c['capacity'] ?></td>
                        <td><?= htmlspecialchars($c['local']) ?></td>
                        <td><span class="<?= $c['active'] ? 'status-active' : 'status-inactive' ?>"><?= $c['active'] ? 'Ativa' : 'Inativa' ?></span></td>
                        <td>
                            <button type="button" class="admin-delete-class btn-delete" data-id="<?= $c['id'] ?>">Remover</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <!-- ══ TAB: TRAINERS ══ -->
    <section class="admin-tab-content profile-card" id="tab-trainers">
        <div class="admin-header-flex">
            <h2 class="card-title" class="m-0">Trainers</h2>
            <button type="button" class="btn-save" id="trigger-add-trainer-admin">+ Adicionar Trainer</button>
        </div>
        <?php if (empty($all_trainers)): ?>
            <p class="weight-hint">Não há trainers registados.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Especialização</th>
                        <th>Localização</th>
                        <th>Aulas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_trainers as $t): 
                        $photo = !empty($t['photo']) ? "../imgs/".htmlspecialchars($t['photo']) : "../imgs/default-avatar.png";
                    ?>
                    <tr>
                        <td><img src="<?= $photo ?>" alt="" class="admin-avatar" onerror="this.src='../imgs/default-avatar.png'"></td>
                        <td><a href="trainer_profile.php?id=<?= $t['id'] ?>" class="link-orange"><?= htmlspecialchars($t['name']) ?></a></td>
                        <td class="admin-email"><?= htmlspecialchars($t['email']) ?></td>
                        <td><?= htmlspecialchars($t['specialization'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($t['location'] ?? '—') ?></td>
                        <td><?= $t['class_count'] ?? 0 ?></td>
                        <td>
                            <button type="button" class="delete-user-btn btn-delete" data-user-id="<?= $t['id'] ?>">Remover</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <!-- ══ TAB: NUTRICIONISTAS ══ -->
    <section class="admin-tab-content profile-card" id="tab-nutritionists">
        <div class="admin-header-flex">
            <h2 class="card-title" class="m-0">Nutricionistas</h2>
            <button type="button" class="btn-save" id="trigger-add-nutritionist-admin">+ Adicionar Nutricionista</button>
        </div>
        <?php if (empty($all_nutritionists)): ?>
            <p class="weight-hint">Não há nutricionistas registados.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Especialização</th>
                        <th>Cédula</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_nutritionists as $n): 
                        $photo = !empty($n['photo']) ? "../imgs/".htmlspecialchars($n['photo']) : "../imgs/default-avatar.png";
                    ?>
                    <tr>
                        <td><img src="<?= $photo ?>" alt="" class="admin-avatar" onerror="this.src='../imgs/default-avatar.png'"></td>
                        <td><?= htmlspecialchars($n['name']) ?></td>
                        <td class="admin-email"><?= htmlspecialchars($n['email']) ?></td>
                        <td><?= htmlspecialchars($n['specialization'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($n['license_number'] ?? '—') ?></td>
                        <td>
                            <button type="button" class="delete-user-btn btn-delete" data-user-id="<?= $n['id'] ?>">Remover</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <!-- ══ TAB: SESSÕES PT ══ -->
    <section class="admin-tab-content profile-card" id="tab-pt">
        <h2 class="card-title">Sessões de Personal Training</h2>
        <?php if (empty($all_pt_bookings)): ?>
            <p class="weight-hint">Não há sessões PT registadas.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Membro</th>
                        <th>Trainer</th>
                        <th>Notas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_pt_bookings as $b): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($b['booking_date'])) ?></td>
                        <td><?= htmlspecialchars($b['start_time']) ?> – <?= htmlspecialchars($b['end_time']) ?></td>
                        <td><?= htmlspecialchars($b['member_name']) ?></td>
                        <td><?= htmlspecialchars($b['trainer_name']) ?></td>
                        <td class="weight-notes"><?= htmlspecialchars($b['notes'] ?? '—') ?></td>
                        <td><span class="pt-status-tag pt-status-tag-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <!-- ══ TAB: EQUIPAMENTOS ══ -->
    <section class="admin-tab-content profile-card" id="tab-equipment">
        <div class="admin-header-flex">
            <h2 class="card-title" class="m-0">Equipamentos</h2>
        </div>

        <!-- Grelha com card "+" igual ao das aulas -->
        <div class="class-grid" id="admin-equipment-grid">
            <?php 
            // $all_equipments é passado globalmente via $GLOBALS dentro do template
            $equips = $GLOBALS['admin_equipments'] ?? [];
            foreach ($equips as $eq):
                $photo = !empty($eq['photo']) ? "../imgs/".htmlspecialchars($eq['photo']) : "../imgs/default-equipment.png";
                $status_class = ($eq['status'] === 'Disponível') ? 'status-available' : 'status-maintenance';
            ?>
            <article class="class-card equipment-card card-relative" data-id="<?= $eq['id'] ?>">
                <span class="status-badge <?= $status_class ?>" class="admin-badge"><?= htmlspecialchars($eq['status']) ?></span>
                <img src="<?= $photo ?>" alt="<?= htmlspecialchars($eq['name']) ?>" class="equipment-photo"
                     onerror="this.onerror=null;this.src='../imgs/default-equipment.png'">
                <h3 class="equipment-name"><?= htmlspecialchars($eq['name']) ?></h3>
                <p class="admin-eq-muscle"><?= htmlspecialchars($eq['muscle_name'] ?? 'Corpo Inteiro') ?></p>
                <p class="loc-text">📍 <?= htmlspecialchars($eq['location']) ?></p>
                <button type="button" class="btn-delete admin-delete-equipment" data-id="<?= $eq['id'] ?>"
                        class="btn-remove-eq">Remover</button>
            </article>
            <?php endforeach; ?>

            <!-- Card "+" para adicionar equipamento -->
            <article class="class-card" id="trigger-add-equipment">
                <div class="text-center text-orange">
                    <span class="create-class-icon">+</span>
                    <span class="create-class-text">Adicionar Equipamento</span>
                </div>
            </article>
        </div>
    </section>

    <!-- ══ TAB: ESTATÍSTICAS ══ -->
    <section class="admin-tab-content profile-card" id="tab-stats">
        <h2 class="card-title">Estatísticas do Ginásio</h2>

        <?php
        $s_members        = $stats['total_members']        ?? 0;
        $s_trainers       = $stats['total_trainers']       ?? 0;
        $s_nutritionists  = $stats['total_nutritionists']  ?? 0;
        $s_avg_age        = $stats['avg_age']              ?? 0;
        $s_eq_avail       = $stats['equipment_available']  ?? 0;
        $s_eq_maint       = $stats['equipment_maintenance']?? 0;
        $s_mem_basico     = $stats['membership_basico']    ?? 0;
        $s_mem_normal     = $stats['membership_normal']    ?? 0;
        $s_mem_vip        = $stats['membership_vip']       ?? 0;
        $s_top_trainers   = $stats['top_trainers']         ?? [];
        $s_total_eq       = max(1, $s_eq_avail + $s_eq_maint);
        ?>

        <!-- Cards de resumo -->
        <div class="admin-dash-grid-4">
            <div class="admin-stat-card">
                <p class="admin-stat-number"><?= $s_members ?></p>
                <p class="admin-stat-label">Membros</p>
            </div>
            <div class="admin-stat-card">
                <p class="admin-stat-number"><?= $s_trainers ?></p>
                <p class="admin-stat-label">Trainers</p>
            </div>
            <div class="admin-stat-card">
                <p class="admin-stat-number"><?= $s_nutritionists ?></p>
                <p class="admin-stat-label">Nutricionistas</p>
            </div>
            <div class="admin-stat-card">
                <p class="admin-stat-number"><?= number_format((float)$s_avg_age, 1) ?></p>
                <p class="admin-stat-label">Idade Média</p>
            </div>
            <div class="admin-stat-card-green">
                <p class="admin-stat-number-green"><?= $s_eq_avail ?></p>
                <p class="admin-stat-label-green">Equip. Disponíveis</p>
            </div>
            <div class="admin-stat-card-orange">
                <p class="admin-stat-number-orange"><?= $s_eq_maint ?></p>
                <p class="admin-stat-label-orange">Em Manutenção</p>
            </div>
        </div>

        <!-- Memberships + Equipamento side by side -->
        <div class="admin-dash-grid-2">
            
            <!-- Memberships -->
            <div class="admin-panel-card">
                <h3 class="admin-panel-title">Distribuição de Memberships</h3>
                <?php
                $total_mem = max(1, $s_mem_basico + $s_mem_normal + $s_mem_vip);
                $bars = [
                    ['Básico', $s_mem_basico, '#bdbdbd'],
                    ['Normal', $s_mem_normal, 'var(--orange)'],
                    ['VIP',    $s_mem_vip,    '#f4c430'],
                ];
                foreach ($bars as [$label, $val, $color]):
                    $pct = round($val / $total_mem * 100);
                ?>
                <div class="admin-progress-container">
                    <div class="admin-progress-header">
                        <span class="fw-600"><?= $label ?></span>
                        <span class="text-muted"><?= $val ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="admin-progress-track">
                        <div class="admin-progress-bar" style="background:<?= $color ?>;width:<?= $pct ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Equipamento disponibilidade -->
            <div class="admin-panel-card">
                <h3 class="admin-panel-title">Estado dos Equipamentos</h3>
                <?php
                $eq_pct_avail = round($s_eq_avail / $s_total_eq * 100);
                $eq_pct_maint = 100 - $eq_pct_avail;
                ?>
                <div class="admin-progress-container">
                    <div class="admin-progress-header">
                        <span class="fw-600">✅ Disponíveis</span>
                        <span class="text-muted"><?= $s_eq_avail ?> (<?= $eq_pct_avail ?>%)</span>
                    </div>
                    <div class="admin-progress-track">
                        <div class="admin-progress-bar" style="background:#4caf50;width:<?= $eq_pct_avail ?>%;"></div>
                    </div>
                </div>
                <div>
                    <div class="admin-progress-header">
                        <span class="fw-600">🛠️ Em Manutenção</span>
                        <span class="text-muted"><?= $s_eq_maint ?> (<?= $eq_pct_maint ?>%)</span>
                    </div>
                    <div class="admin-progress-track">
                        <div class="admin-progress-bar" style="background:#ff9800;width:<?= $eq_pct_maint ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Trainers por avaliação -->
        <?php if (!empty($s_top_trainers)): ?>
        <div class="admin-panel-card">
            <h3 class="admin-panel-title">🏆 Trainers Melhor Avaliados</h3>
            <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trainer</th>
                        <th>Avaliação Média</th>
                        <th>Nº Avaliações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($s_top_trainers as $i => $tr): ?>
                    <tr>
                        <td class="fw-700 text-orange"><?= $i + 1 ?>º</td>
                        <td><a href="trainer_profile.php?id=<?= $tr['id'] ?>" class="link-orange"><?= htmlspecialchars($tr['name']) ?></a></td>
                        <td>
                            <?php
                            $avg = round((float)$tr['avg_rating'], 1);
                            $stars = '';
                            for ($s = 1; $s <= 5; $s++) $stars .= $s <= $avg ? '★' : '☆';
                            ?>
                            <span class="text-gold text-1rem"><?= $stars ?></span>
                            <span class="fw-700 ml-4"><?= number_format($avg, 1) ?></span>
                        </td>
                        <td class="text-muted"><?= $tr['review_count'] ?> avaliações</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php endif; ?>
    </section>
</section>


<!-- ══════════════════════════════════════════════
     MODAL: ADICIONAR EQUIPAMENTO
══════════════════════════════════════════════ -->
<div id="add-equipment-modal" class="add-class-modal-overlay hidden">
    <article class="profile-login-card admin-modal-card">
        <button type="button" id="close-add-equipment" class="close-modal-btn">&times;</button>
        <p class="profile-specialty admin-modal-subtitle">NOVO EQUIPAMENTO</p>

        <form action="admin.php" method="post" enctype="multipart/form-data" class="admin-modal-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="action" value="add_equipment">

            <!-- Nome -->
            <div class="mb-20 text-center">
                <input type="text" name="eq_name" placeholder="Nome do Equipamento" required
                       class="admin-modal-title-input">
            </div>

            <div class="profile-details-list admin-modal-details-list">

                <!-- Foto -->
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Foto</strong>
                    <div class="w-200">
                        <label for="eq-photo-input" id="eq-photo-label"
                               class="admin-modal-file-label">
                            📷 Escolher foto
                        </label>
                        <input type="file" id="eq-photo-input" name="eq_photo" accept="image/*" style="display:none;">
                    </div>
                </div>

                <!-- Grupo Muscular -->
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Grupo Muscular</strong>
                    <select name="eq_muscle" class="admin-modal-input">
                        <option value="">Seleciona...</option>
                        <option value="1">Quadríceps e Glúteos</option>
                        <option value="2">Peitoral e Tríceps</option>
                        <option value="3">Dorsais e Bíceps</option>
                        <option value="4">Ombros</option>
                        <option value="5">Isquiotibiais e Panturrilhas</option>
                        <option value="6">Core e Abdominais</option>
                    </select>
                </div>

                <!-- Localização -->
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Localização</strong>
                    <input type="text" name="eq_location" placeholder="ex: Piso 1 - Força" required
                           class="admin-modal-input">
                </div>

                <!-- Vídeo de Execução -->
                <div class="profile-detail-item admin-modal-detail-item-no-border">
                    <strong class="admin-modal-label">Vídeo (URL)</strong>
                    <input type="text" name="eq_video" placeholder="https://youtube.com/embed/..."
                           class="admin-modal-input">
                </div>
            </div>

            <button type="submit" class="btn-review-submit admin-modal-submit">
                Adicionar Equipamento
            </button>
        </form>
    </article>
</div>

<!-- ══════════════════════════════════════════════
     MODAL: ADICIONAR TRAINER
══════════════════════════════════════════════ -->
<div id="add-trainer-modal" class="add-class-modal-overlay hidden">
    <article class="profile-login-card admin-modal-card">
        <button type="button" id="close-add-trainer" class="close-modal-btn">&times;</button>
        <p class="profile-specialty admin-modal-subtitle">NOVO TRAINER</p>

        <form action="admin.php" method="post" enctype="multipart/form-data" class="admin-modal-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="action" value="add_trainer">

            <div class="mb-20 text-center">
                <input type="text" name="tr_name" placeholder="Nome Completo" required
                       class="admin-modal-title-input">
            </div>

            <div class="profile-details-list admin-modal-details-list">

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Username</strong>
                    <input type="text" name="tr_username" placeholder="username" required
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Email</strong>
                    <input type="email" name="tr_email" placeholder="email@exemplo.com" required
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Password</strong>
                    <input type="password" name="tr_password" placeholder="mínimo 6 caracteres" required minlength="6"
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Género</strong>
                    <select name="tr_sex" required class="admin-modal-input">
                        <option value="">Seleciona...</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                    </select>
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Especialização</strong>
                    <input type="text" name="tr_specialization" placeholder="ex: HIIT,Força"
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Formação</strong>
                    <input type="text" name="tr_formation" placeholder="ex: Lic. Ciências do Desporto"
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Certificações</strong>
                    <input type="text" name="tr_certification" placeholder="ex: CrossFit Level 1"
                           class="admin-modal-input">
                </div>

                <div class="profile-detail-item admin-modal-detail-item-no-border">
                    <strong class="admin-modal-label">Localização</strong>
                    <input type="text" name="tr_location" placeholder="ex: Porto, Portugal"
                           class="admin-modal-input">
                </div>
            </div>

            <button type="submit" class="btn-review-submit admin-modal-submit">
                Criar Trainer
            </button>
        </form>
    </article>
</div>


<!-- ══════════════════════════════════════════════
     MODAL: ADICIONAR NUTRICIONISTA
══════════════════════════════════════════════ -->
<div id="add-nutritionist-modal" class="add-class-modal-overlay hidden">
    <article class="profile-login-card admin-modal-card">
        <button type="button" id="close-add-nutritionist" class="close-modal-btn">&times;</button>
        <p class="profile-specialty admin-modal-subtitle">NOVO NUTRICIONISTA</p>
        <form action="admin.php" method="post" enctype="multipart/form-data" class="admin-modal-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
            <input type="hidden" name="action" value="add_nutritionist">
            <div class="mb-20 text-center">
                <input type="text" name="nu_name" placeholder="Nome Completo" required
                       class="admin-modal-title-input">
            </div>
            <div class="profile-details-list admin-modal-details-list">
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Username</strong>
                    <input type="text" name="nu_username" placeholder="username" required class="admin-modal-input">
                </div>
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Email</strong>
                    <input type="email" name="nu_email" placeholder="email@exemplo.com" required class="admin-modal-input">
                </div>
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Password</strong>
                    <input type="password" name="nu_password" placeholder="mínimo 6 caracteres" required minlength="6" class="admin-modal-input">
                </div>
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Especialização</strong>
                    <input type="text" name="nu_specialization" placeholder="ex: Desportiva, Clínica" class="admin-modal-input">
                </div>
                <div class="profile-detail-item admin-modal-detail-item">
                    <strong class="admin-modal-label">Nº Cédula</strong>
                    <input type="text" name="nu_license" placeholder="ex: ON-12345" class="admin-modal-input">
                </div>
                <div class="profile-detail-item admin-modal-detail-item-no-border">
                    <strong class="admin-modal-label">Foto</strong>
                    <div class="w-200">
                        <label for="nu-photo-input" id="nu-photo-label" class="admin-modal-file-label">📷 Escolher foto</label>
                        <input type="file" id="nu-photo-input" name="nu_photo" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-review-submit admin-modal-submit">Criar Nutricionista</button>
        </form>
    </article>
</div>

<?php } ?>

<?php function output_user_row(int $id, string $join_date, string $username, string $display_name, string $role, string $email, string $membership = 'basico') { ?>
    <tr>
        <td><?= htmlspecialchars($join_date) ?></td>
        <td class="admin-user">@<?= htmlspecialchars($username) ?></td>
        <td><?= htmlspecialchars($display_name) ?></td>
        <td>
            <select name="role" class="role-select" data-user-id="<?= $id ?>">
                <option value="member"       <?= $role === 'member'       ? 'selected' : '' ?>>Membro</option>
                <option value="trainer"      <?= $role === 'trainer'      ? 'selected' : '' ?>>Trainer</option>
                <option value="nutritionist" <?= $role === 'nutritionist' ? 'selected' : '' ?>>Nutricionista</option>
                <option value="admin"        <?= $role === 'admin'        ? 'selected' : '' ?>>Admin</option>
            </select>
        </td>
        <td class="admin-email"><?= htmlspecialchars($email) ?></td>
        <td>
            <?php if ($role === 'member'): ?>
            <form method="post" action="admin.php" class="m-0">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="update_user_membership">
                <input type="hidden" name="user_id" value="<?= $id ?>">
                <select name="membership" class="role-select" onchange="this.form.submit()" class="min-w-90">
                    <option value="basico" <?= $membership === 'basico' ? 'selected' : '' ?>>Básico</option>
                    <option value="normal" <?= $membership === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="vip"    <?= $membership === 'vip'    ? 'selected' : '' ?>>VIP</option>
                </select>
            </form>
            <?php else: ?>
                <span class="text-muted-small">—</span>
            <?php endif; ?>
        </td>
    </tr>
<?php } ?>