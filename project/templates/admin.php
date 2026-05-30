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
            <input type="hidden" name="page" value="<?= $page - 1 ?>">
            <input type="hidden" name="tab" value="users">
            <?php if ($username !== ''): ?>
                <input type="hidden" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>">
            <?php endif; ?>
        </form>
        <form action="admin.php" method="get" id="admin-next-page">
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
                        <td><a href="class.php?id=<?= $c['id'] ?>" style="color:var(--orange);text-decoration:underline;"><?= htmlspecialchars($c['name']) ?></a></td>
                        <td><span class="class-type-pill-sm"><?= htmlspecialchars($c['type']) ?></span></td>
                        <td><?= htmlspecialchars($c['trainer_name'] ?? '—') ?></td>
                        <td><?= $c['next_session'] ? date('d/m/Y H:i', strtotime($c['next_session'])) : '<span style="color:var(--text-muted)">Sem sessão</span>' ?></td>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <h2 class="card-title" style="margin:0;">Trainers</h2>
            <button type="button" class="btn-save" id="trigger-add-trainer">+ Adicionar Trainer</button>
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
                        <td><a href="trainer_profile.php?id=<?= $t['id'] ?>" style="color:var(--orange);text-decoration:underline;"><?= htmlspecialchars($t['name']) ?></a></td>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <h2 class="card-title" style="margin:0;">Nutricionistas</h2>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <h2 class="card-title" style="margin:0;">Equipamentos</h2>
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
            <article class="class-card equipment-card" data-id="<?= $eq['id'] ?>" style="position:relative;">
                <span class="status-badge <?= $status_class ?>" style="position:absolute;top:12px;right:12px;font-size:0.7rem;padding:4px 10px;"><?= htmlspecialchars($eq['status']) ?></span>
                <img src="<?= $photo ?>" alt="<?= htmlspecialchars($eq['name']) ?>" class="equipment-photo"
                     onerror="this.onerror=null;this.src='../imgs/default-equipment.png'">
                <h3 class="equipment-name"><?= htmlspecialchars($eq['name']) ?></h3>
                <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:8px;"><?= htmlspecialchars($eq['muscle_name'] ?? 'Corpo Inteiro') ?></p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:16px;">📍 <?= htmlspecialchars($eq['location']) ?></p>
                <button type="button" class="btn-delete admin-delete-equipment" data-id="<?= $eq['id'] ?>"
                        style="width:100%;margin-top:auto;">Remover</button>
            </article>
            <?php endforeach; ?>

            <!-- Card "+" para adicionar equipamento -->
            <article class="class-card" id="trigger-add-equipment"
                     style="cursor:pointer;display:flex;align-items:center;justify-content:center;
                            border:2px dashed var(--orange);background-color:#fffbf9;
                            min-height:280px;transition:var(--transition);">
                <div style="text-align:center;color:var(--orange);">
                    <span style="font-size:5rem;font-weight:200;display:block;line-height:1;margin-bottom:5px;">+</span>
                    <span style="font-weight:700;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;">Adicionar Equipamento</span>
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
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin-bottom:32px;">
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:var(--orange);margin:0;"><?= $s_members ?></p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;font-weight:600;text-transform:uppercase;">Membros</p>
            </div>
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:var(--orange);margin:0;"><?= $s_trainers ?></p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;font-weight:600;text-transform:uppercase;">Trainers</p>
            </div>
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:var(--orange);margin:0;"><?= $s_nutritionists ?></p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;font-weight:600;text-transform:uppercase;">Nutricionistas</p>
            </div>
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:var(--orange);margin:0;"><?= number_format((float)$s_avg_age, 1) ?></p>
                <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;font-weight:600;text-transform:uppercase;">Idade Média</p>
            </div>
            <div style="background:#e8f5e9;border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:#2e7d32;margin:0;"><?= $s_eq_avail ?></p>
                <p style="font-size:0.8rem;color:#2e7d32;margin:4px 0 0;font-weight:600;text-transform:uppercase;">Equip. Disponíveis</p>
            </div>
            <div style="background:#fff3e0;border-radius:12px;padding:20px;text-align:center;">
                <p style="font-size:2rem;font-weight:800;color:#e65100;margin:0;"><?= $s_eq_maint ?></p>
                <p style="font-size:0.8rem;color:#e65100;margin:4px 0 0;font-weight:600;text-transform:uppercase;">Em Manutenção</p>
            </div>
        </div>

        <!-- Memberships + Equipamento side by side -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;">
            
            <!-- Memberships -->
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;">
                <h3 style="font-size:0.9rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:0 0 16px;">Distribuição de Memberships</h3>
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
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                        <span style="font-weight:600;"><?= $label ?></span>
                        <span style="color:var(--text-muted);"><?= $val ?> (<?= $pct ?>%)</span>
                    </div>
                    <div style="background:#e0e0e0;border-radius:20px;height:8px;">
                        <div style="background:<?= $color ?>;width:<?= $pct ?>%;height:8px;border-radius:20px;transition:width .5s;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Equipamento disponibilidade -->
            <div style="background:var(--gray-bg);border-radius:12px;padding:20px;">
                <h3 style="font-size:0.9rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:0 0 16px;">Estado dos Equipamentos</h3>
                <?php
                $eq_pct_avail = round($s_eq_avail / $s_total_eq * 100);
                $eq_pct_maint = 100 - $eq_pct_avail;
                ?>
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                        <span style="font-weight:600;">✅ Disponíveis</span>
                        <span style="color:var(--text-muted);"><?= $s_eq_avail ?> (<?= $eq_pct_avail ?>%)</span>
                    </div>
                    <div style="background:#e0e0e0;border-radius:20px;height:8px;">
                        <div style="background:#4caf50;width:<?= $eq_pct_avail ?>%;height:8px;border-radius:20px;"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                        <span style="font-weight:600;">🛠️ Em Manutenção</span>
                        <span style="color:var(--text-muted);"><?= $s_eq_maint ?> (<?= $eq_pct_maint ?>%)</span>
                    </div>
                    <div style="background:#e0e0e0;border-radius:20px;height:8px;">
                        <div style="background:#ff9800;width:<?= $eq_pct_maint ?>%;height:8px;border-radius:20px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Trainers por avaliação -->
        <?php if (!empty($s_top_trainers)): ?>
        <div style="background:var(--gray-bg);border-radius:12px;padding:20px;">
            <h3 style="font-size:0.9rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:0 0 16px;">🏆 Trainers Melhor Avaliados</h3>
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
                        <td style="font-weight:700;color:var(--orange);"><?= $i + 1 ?>º</td>
                        <td><a href="trainer_profile.php?id=<?= $tr['id'] ?>" style="color:var(--orange);text-decoration:underline;"><?= htmlspecialchars($tr['name']) ?></a></td>
                        <td>
                            <?php
                            $avg = round((float)$tr['avg_rating'], 1);
                            $stars = '';
                            for ($s = 1; $s <= 5; $s++) $stars .= $s <= $avg ? '★' : '☆';
                            ?>
                            <span style="color:#f4c430;font-size:1rem;"><?= $stars ?></span>
                            <span style="font-weight:700;margin-left:4px;"><?= number_format($avg, 1) ?></span>
                        </td>
                        <td style="color:var(--text-muted);"><?= $tr['review_count'] ?> avaliações</td>
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
    <article class="profile-login-card" style="width:400px !important;max-width:95vw !important;height:auto !important;min-height:unset !important;display:block !important;margin:0 auto !important;position:relative !important;animation:modalSlideUp 0.3s ease !important;text-align:center !important;padding:25px !important;border-radius:12px !important;box-shadow:0 8px 30px rgba(0,0,0,0.15) !important;background-color:#ffffff !important;box-sizing:border-box !important;">
        <button type="button" id="close-add-equipment" class="close-modal-btn">&times;</button>
        <p class="profile-specialty" style="margin-bottom:5px !important;font-weight:700 !important;color:var(--orange) !important;text-transform:uppercase !important;letter-spacing:0.05em !important;font-size:0.8rem !important;">NOVO EQUIPAMENTO</p>

        <form action="admin.php" method="post" enctype="multipart/form-data" style="text-align:left !important;width:100% !important;">
            <input type="hidden" name="action" value="add_equipment">

            <!-- Nome -->
            <div style="margin-bottom:20px !important;text-align:center !important;">
                <input type="text" name="eq_name" placeholder="Nome do Equipamento" required
                       style="font-size:1.3rem !important;font-weight:800 !important;text-align:center !important;border:none !important;border-bottom:2px solid var(--gray-border) !important;width:100% !important;padding:4px !important;color:var(--text-main) !important;outline:none !important;font-family:inherit !important;background:transparent !important;">
            </div>

            <div class="profile-details-list" style="margin-bottom:20px !important;width:100% !important;">

                <!-- Foto -->
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Foto</strong>
                    <div style="width:200px !important;">
                        <label for="eq-photo-input" id="eq-photo-label"
                               style="display:block;padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;cursor:pointer;font-size:0.8rem;color:var(--text-muted);text-align:center;background:#fff;box-sizing:border-box;width:100%;">
                            📷 Escolher foto
                        </label>
                        <input type="file" id="eq-photo-input" name="eq_photo" accept="image/*" style="display:none;">
                    </div>
                </div>

                <!-- Grupo Muscular -->
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Grupo Muscular</strong>
                    <select name="eq_muscle" style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;font-family:inherit !important;font-size:0.85rem !important;background:#fff !important;color:var(--text-main) !important;">
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
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Localização</strong>
                    <input type="text" name="eq_location" placeholder="ex: Piso 1 - Força" required
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <!-- Vídeo de Execução -->
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Vídeo (URL)</strong>
                    <input type="text" name="eq_video" placeholder="https://youtube.com/embed/..."
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
            </div>

            <button type="submit" class="btn-review-submit" style="width:100% !important;margin:0 !important;padding:12px !important;font-size:0.9rem !important;">
                Adicionar Equipamento
            </button>
        </form>
    </article>
</div>

<!-- ══════════════════════════════════════════════
     MODAL: ADICIONAR TRAINER
══════════════════════════════════════════════ -->
<div id="add-trainer-modal" class="add-class-modal-overlay hidden">
    <article class="profile-login-card" style="width:400px !important;max-width:95vw !important;height:auto !important;min-height:unset !important;display:block !important;margin:0 auto !important;position:relative !important;animation:modalSlideUp 0.3s ease !important;text-align:center !important;padding:25px !important;border-radius:12px !important;box-shadow:0 8px 30px rgba(0,0,0,0.15) !important;background-color:#ffffff !important;box-sizing:border-box !important;">
        <button type="button" id="close-add-trainer" class="close-modal-btn">&times;</button>
        <p class="profile-specialty" style="margin-bottom:5px !important;font-weight:700 !important;color:var(--orange) !important;text-transform:uppercase !important;letter-spacing:0.05em !important;font-size:0.8rem !important;">NOVO TRAINER</p>

        <form action="admin.php" method="post" enctype="multipart/form-data" style="text-align:left !important;width:100% !important;">
            <input type="hidden" name="action" value="add_trainer">

            <div style="margin-bottom:20px !important;text-align:center !important;">
                <input type="text" name="tr_name" placeholder="Nome Completo" required
                       style="font-size:1.3rem !important;font-weight:800 !important;text-align:center !important;border:none !important;border-bottom:2px solid var(--gray-border) !important;width:100% !important;padding:4px !important;color:var(--text-main) !important;outline:none !important;font-family:inherit !important;background:transparent !important;">
            </div>

            <div class="profile-details-list" style="margin-bottom:20px !important;width:100% !important;">

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Username</strong>
                    <input type="text" name="tr_username" placeholder="username" required
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Email</strong>
                    <input type="email" name="tr_email" placeholder="email@exemplo.com" required
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Password</strong>
                    <input type="password" name="tr_password" placeholder="mínimo 6 caracteres" required minlength="6"
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Género</strong>
                    <select name="tr_sex" required style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;font-family:inherit !important;font-size:0.85rem !important;background:#fff !important;color:var(--text-main) !important;">
                        <option value="">Seleciona...</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                    </select>
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Especialização</strong>
                    <input type="text" name="tr_specialization" placeholder="ex: HIIT,Força"
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Formação</strong>
                    <input type="text" name="tr_formation" placeholder="ex: Lic. Ciências do Desporto"
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Certificações</strong>
                    <input type="text" name="tr_certification" placeholder="ex: CrossFit Level 1"
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>

                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Localização</strong>
                    <input type="text" name="tr_location" placeholder="ex: Porto, Portugal"
                           style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
            </div>

            <button type="submit" class="btn-review-submit" style="width:100% !important;margin:0 !important;padding:12px !important;font-size:0.9rem !important;">
                Criar Trainer
            </button>
        </form>
    </article>
</div>

<style>
/* ── TABS ───────────────────────────────────────── */
.admin-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.admin-tab {
    padding: 10px 18px;
    border: 1px solid var(--gray-border);
    border-radius: 30px;
    background: var(--white);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    color: var(--text-muted);
    transition: var(--transition);
}
.admin-tab:hover { border-color: var(--orange); color: var(--orange); }
.admin-tab.active { background: var(--orange); border-color: var(--orange); color: #fff; }

.admin-tab-content { display: none; }
.admin-tab-content.active { display: block; }

/* ── TABLE ──────────────────────────────────────── */
.admin-avatar {
    width: 36px; height: 36px;
    border-radius: 50%; object-fit: cover;
    border: 1px solid var(--gray-border);
}
.class-type-pill-sm {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 20px;
    background: var(--gray-bg);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: capitalize;
}
.status-active  { color: #2e7d32; font-weight: 600; font-size: 0.85rem; }
.status-inactive { color: #c62828; font-weight: 600; font-size: 0.85rem; }
.status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-available   { background: #e8f5e9; color: #2e7d32; }
.status-maintenance { background: #fff3e0; color: #e65100; }

/* Modal overlay */
.add-class-modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.45); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 99999; overflow-y: auto; padding: 20px; box-sizing: border-box; }
.add-class-modal-overlay.hidden { display: none; }
.close-modal-btn { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 2rem; color: var(--text-muted); cursor: pointer; line-height: 1; }
.close-modal-btn:hover { color: var(--orange); }
@keyframes modalSlideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>


<!-- ══════════════════════════════════════════════
     MODAL: ADICIONAR NUTRICIONISTA
══════════════════════════════════════════════ -->
<div id="add-nutritionist-modal" class="add-class-modal-overlay hidden">
    <article class="profile-login-card" style="width:400px !important;max-width:95vw !important;height:auto !important;min-height:unset !important;display:block !important;margin:0 auto !important;position:relative !important;animation:modalSlideUp 0.3s ease !important;text-align:center !important;padding:25px !important;border-radius:12px !important;box-shadow:0 8px 30px rgba(0,0,0,0.15) !important;background-color:#ffffff !important;box-sizing:border-box !important;">
        <button type="button" id="close-add-nutritionist" class="close-modal-btn">&times;</button>
        <p class="profile-specialty" style="margin-bottom:5px !important;font-weight:700 !important;color:var(--orange) !important;text-transform:uppercase !important;letter-spacing:0.05em !important;font-size:0.8rem !important;">NOVO NUTRICIONISTA</p>
        <form action="admin.php" method="post" enctype="multipart/form-data" style="text-align:left !important;width:100% !important;">
            <input type="hidden" name="action" value="add_nutritionist">
            <div style="margin-bottom:20px !important;text-align:center !important;">
                <input type="text" name="nu_name" placeholder="Nome Completo" required
                       style="font-size:1.3rem !important;font-weight:800 !important;text-align:center !important;border:none !important;border-bottom:2px solid var(--gray-border) !important;width:100% !important;padding:4px !important;color:var(--text-main) !important;outline:none !important;font-family:inherit !important;background:transparent !important;">
            </div>
            <div class="profile-details-list" style="margin-bottom:20px !important;width:100% !important;">
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Username</strong>
                    <input type="text" name="nu_username" placeholder="username" required style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Email</strong>
                    <input type="email" name="nu_email" placeholder="email@exemplo.com" required style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Password</strong>
                    <input type="password" name="nu_password" placeholder="mínimo 6 caracteres" required minlength="6" style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Especialização</strong>
                    <input type="text" name="nu_specialization" placeholder="ex: Desportiva, Clínica" style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;border-bottom:1px solid var(--gray-bg) !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Nº Cédula</strong>
                    <input type="text" name="nu_license" placeholder="ex: ON-12345" style="padding:6px 10px !important;border:1px solid var(--gray-border) !important;border-radius:6px !important;width:200px !important;box-sizing:border-box !important;font-family:inherit !important;font-size:0.85rem !important;color:var(--text-main) !important;background:#fff !important;">
                </div>
                <div class="profile-detail-item" style="display:flex !important;justify-content:space-between !important;align-items:center !important;padding:8px 0 !important;">
                    <strong style="font-size:0.85rem !important;color:var(--text-muted) !important;">Foto</strong>
                    <div style="width:200px !important;">
                        <label for="nu-photo-input" id="nu-photo-label" style="display:block;padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;cursor:pointer;font-size:0.8rem;color:var(--text-muted);text-align:center;background:#fff;box-sizing:border-box;width:100%;">📷 Escolher foto</label>
                        <input type="file" id="nu-photo-input" name="nu_photo" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-review-submit" style="width:100% !important;margin:0 !important;padding:12px !important;font-size:0.9rem !important;">Criar Nutricionista</button>
        </form>
    </article>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── TABS ──────────────────────────────────────
    const tabs    = document.querySelectorAll('.admin-tab');
    const panels  = document.querySelectorAll('.admin-tab-content');

    // Restore active tab from URL hash or query param
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || location.hash.replace('#','') || 'users';

    tabs.forEach(tab => {
        const target = tab.dataset.tab;
        if (target === activeTab) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    panels.forEach(panel => {
        panel.classList.toggle('active', panel.id === 'tab-' + activeTab);
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    // ── MODAL EQUIPAMENTO ─────────────────────────
    const eqTrigger = document.getElementById('trigger-add-equipment');
    const eqModal   = document.getElementById('add-equipment-modal');
    const eqClose   = document.getElementById('close-add-equipment');
    if (eqTrigger && eqModal) {
        eqTrigger.addEventListener('click', () => eqModal.classList.remove('hidden'));
        eqClose.addEventListener('click',   () => eqModal.classList.add('hidden'));
        eqModal.addEventListener('click',   e => { if (e.target === eqModal) eqModal.classList.add('hidden'); });
    }

    // Mostrar nome do ficheiro seleccionado
    const eqPhotoInput = document.getElementById('eq-photo-input');
    const eqPhotoLabel = document.getElementById('eq-photo-label');
    if (eqPhotoInput) {
        eqPhotoInput.addEventListener('change', function () {
            eqPhotoLabel.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto';
        });
    }

    // ── MODAL TRAINER ─────────────────────────────
    const trTrigger = document.getElementById('trigger-add-trainer');
    const trModal   = document.getElementById('add-trainer-modal');
    const trClose   = document.getElementById('close-add-trainer');
    if (trTrigger && trModal) {
        trTrigger.addEventListener('click', () => trModal.classList.remove('hidden'));
        trClose.addEventListener('click',   () => trModal.classList.add('hidden'));
        trModal.addEventListener('click',   e => { if (e.target === trModal) trModal.classList.add('hidden'); });
    }

    // ── MODAL NUTRICIONISTA ──────────────────────
    const nuTrigger = document.getElementById('trigger-add-nutritionist-admin');
    const nuModal   = document.getElementById('add-nutritionist-modal');
    const nuClose   = document.getElementById('close-add-nutritionist');
    if (nuTrigger && nuModal) {
        nuTrigger.addEventListener('click', () => nuModal.classList.remove('hidden'));
        nuClose.addEventListener('click',   () => nuModal.classList.add('hidden'));
        nuModal.addEventListener('click',   e => { if (e.target === nuModal) nuModal.classList.add('hidden'); });
    }
    const nuPhotoInput = document.getElementById('nu-photo-input');
    const nuPhotoLabel = document.getElementById('nu-photo-label');
    if (nuPhotoInput) {
        nuPhotoInput.addEventListener('change', function() {
            nuPhotoLabel.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto';
        });
    }

    // ── DELETE CLASS ──────────────────────────────
    document.querySelectorAll('.admin-delete-class').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Remover esta aula e todas as suas sessões permanentemente?')) return;
            const id = this.dataset.id;
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_class&id=' + encodeURIComponent(id)
            }).then(r => {
                if (r.ok) this.closest('tr').remove();
                else alert('Erro ao remover aula.');
            });
        });
    });

    // ── DELETE EQUIPMENT ──────────────────────────────
    document.querySelectorAll('.admin-delete-equipment').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Remover este equipamento permanentemente?')) return;
            const id = this.dataset.id;
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_equipment&id=' + encodeURIComponent(id)
            }).then(r => {
                if (r.ok) this.closest('article').remove();
                else alert('Erro ao remover equipamento.');
            });
        });
    });
});
</script>

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
            <form method="post" action="admin.php" style="margin:0;">
                <input type="hidden" name="action" value="update_user_membership">
                <input type="hidden" name="user_id" value="<?= $id ?>">
                <select name="membership" class="role-select" onchange="this.form.submit()" style="min-width:90px;">
                    <option value="basico" <?= $membership === 'basico' ? 'selected' : '' ?>>Básico</option>
                    <option value="normal" <?= $membership === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="vip"    <?= $membership === 'vip'    ? 'selected' : '' ?>>VIP</option>
                </select>
            </form>
            <?php else: ?>
                <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
            <?php endif; ?>
        </td>
    </tr>
<?php } ?>