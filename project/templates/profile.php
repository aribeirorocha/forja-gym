<?php function output_profile(array $user, string $success_msg = '', string $error_msg = '') { 

$membership = $user['membership'] ?? 'basico';
$membership_labels = ['basico' => 'Básico', 'normal' => 'Normal', 'vip' => 'VIP'];
$membership_label  = $membership_labels[$membership] ?? ucfirst($membership);

$months_pt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
$dt = new DateTime($user['created_at'] ?? 'now');
$member_since = $dt->format('d') . ' de ' . $months_pt[intval($dt->format('m')) - 1] . ' de ' . $dt->format('Y');


$created_at = $user['created_at'] ?? '';

$membership_colors = ['basico' => 'badge-blue', 'normal' => 'badge-orange', 'vip' => 'badge-gold'];
$membership_color  = $membership_colors[$membership] ?? 'badge-blue';

$membership_prices   = ['basico' => '19€/mês', 'normal' => '35€/mês', 'vip' => '59€/mês'];
$membership_features = [
    'basico' => ['Acesso ao ginásio', '1 aula por semana', 'Balneários incluídos'],
    'normal' => ['Acesso ilimitado', '3 aulas por semana', '1 sessão PT/mês'],
    'vip'    => ['Acesso ilimitado 24h', 'Aulas ilimitadas', 'PT dedicado (4/mês)', 'Área premium'],
];

$member_since = '';
if ($created_at) {
    $dt = new DateTime($created_at);
    $months_pt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $member_since = $dt->format('d') . ' de ' . $months_pt[intval($dt->format('m')) - 1] . ' de ' . $dt->format('Y');
}

$initial = strtoupper(substr($user['name'], 0, 1));
$avatar_fallback = "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 150 150%22><circle cx=%2275%22 cy=%2275%22 r=%2275%22 fill=%22%23f27a38%22/><text x=%2275%22 y=%2295%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22 font-family=%22Arial%22>{$initial}</text></svg>";
?>

<div class="profile-dashboard">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar profile-card">
        <div class="profile-photo-container">
            <img src="../imgs/default-avatar.png" alt="Foto de Perfil" class="profile-photo-large"
                 onerror="this.src='<?= $avatar_fallback ?>'">
        </div>
        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <p class="profile-at">@<?= htmlspecialchars($user['username']) ?></p>
        <span class="badge <?= $membership_color ?>"><?= $membership_label ?></span>
        <?php if ($member_since): ?>
            <p class="member-since">Membro desde<br><?= $member_since ?></p>
        <?php endif; ?>
        <a href="logout.php" class="btn-logout">Terminar Sessão</a>
    </aside>

    <!-- MAIN CONTENT -->
    <section class="profile-content">

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <!-- DADOS PESSOAIS -->
        <article class="profile-card">
            <h3 class="card-title">Dados Pessoais</h3>
            <form action="profile.php" method="post">
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
        </article>

        <!-- SEGURANÇA -->
        <article class="profile-card">
            <h3 class="card-title">Segurança</h3>
            <form action="profile.php" method="post">
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
        </article>

        <!-- PLANO -->
        <article class="profile-card">
            <h3 class="card-title">Plano de Membership</h3>

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
                                <?php if ($membership === $plan): ?>
                                    <span class="plan-active-tag">Atual</span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>

                </div>
                <button type="submit" class="btn-save">Atualizar Plano</button>
            </form>
        </article>

    </section>
</div>
<?php } ?>