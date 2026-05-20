<?php function output_profile(array $user, array $weight_log = [], string $success_msg = '', string $error_msg = '') { 

$membership = $user['membership'] ?? 'basico';
$membership_labels = ['basico' => 'Básico', 'normal' => 'Normal', 'vip' => 'VIP'];
$membership_label  = $membership_labels[$membership] ?? ucfirst($membership);

$membership_colors = ['basico' => 'badge-blue', 'normal' => 'badge-orange', 'vip' => 'badge-gold'];
$membership_color  = $membership_colors[$membership] ?? 'badge-blue';

$membership_prices   = ['basico' => '19€/mês', 'normal' => '35€/mês', 'vip' => '59€/mês'];
$membership_features = [
    'basico' => ['Acesso ao ginásio', '1 aula por semana', 'Balneários incluídos'],
    'normal' => ['Acesso ilimitado', '3 aulas por semana', '1 sessão PT/mês'],
    'vip'    => ['Acesso ilimitado 24h', 'Aulas ilimitadas', 'PT dedicado (4/mês)', 'Área premium'],
];

$created_at = $user['created_at'] ?? '';
$member_since = '';
if ($created_at) {
    $dt = new DateTime($created_at);
    $months_pt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $member_since = $dt->format('d') . ' de ' . $months_pt[intval($dt->format('m')) - 1] . ' de ' . $dt->format('Y');
}

$initial = strtoupper(substr($user['name'], 0, 1));
$avatar_fallback = "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 150 150%22><circle cx=%2275%22 cy=%2275%22 r=%2275%22 fill=%22%23f27a38%22/><text x=%2275%22 y=%2295%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22 font-family=%22Arial%22>{$initial}</text></svg>";

// Preparar dados do gráfico de peso (JSON para JS)
$chart_labels = [];
$chart_data   = [];
foreach (array_reverse($weight_log) as $entry) {
    $chart_labels[] = (new DateTime($entry['logged_at']))->format('d/m');
    $chart_data[]   = (float) $entry['weight_kg'];
}
$chart_labels_json = json_encode($chart_labels);
$chart_data_json   = json_encode($chart_data);

$latest_weight = !empty($weight_log) ? $weight_log[0]['weight_kg'] : null;

// Tentar carregar a foto do utilizador, se não existir usa a default/fallback
$photo_path = !empty($user['photo']) ? "../imgs/" . htmlspecialchars($user['photo']) : "../imgs/default-avatar.png";
?>

<div class="profile-dashboard">

    <aside class="profile-sidebar profile-card">
        <div class="profile-photo-wrapper">
            <img src="<?= $photo_path ?>" alt="Foto de Perfil" class="profile-photo-large"
                 onerror="this.src='<?= $avatar_fallback ?>'">
            
            <form action="profile.php" method="post" enctype="multipart/form-data" id="photo-upload-form">
                <input type="hidden" name="action" value="update_photo">
                <label for="photo-upload" class="camera-icon" title="Mudar foto de perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M149.1 64.8L138.7 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H373.3L362.9 64.8C356.4 45.2 338.1 32 317.4 32H194.6c-20.7 0-39 13.2-45.5 32.8zM256 192a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                </label>
                <input type="file" id="photo-upload" name="profile_photo" accept="image/*" style="display: none;" onchange="document.getElementById('photo-upload-form').submit();">
            </form>
        </div>
        
        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <p class="profile-at">@<?= htmlspecialchars($user['username']) ?></p>
        <span class="badge <?= $membership_color ?>"><?= $membership_label ?></span>
        <?php if ($member_since): ?>
            <p class="member-since">Membro desde<br><?= $member_since ?></p>
        <?php endif; ?>
        <?php if ($latest_weight): ?>
            <p class="member-since">Peso atual<br><strong><?= number_format($latest_weight, 1) ?> kg</strong></p>
        <?php endif; ?>
        <a href="logout.php" class="btn-logout">Terminar Sessão</a>
    </aside>

    <section class="profile-content">

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

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
                                   value="<?= $latest_weight ? number_format($latest_weight, 1, '.', '') : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom:0; flex:1;">
                            <label>Data</label>
                            <input type="date" name="logged_at"
                                   value="<?= date('Y-m-d') ?>"
                                   max="<?= date('Y-m-d') ?>">
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
                                <th>Data</th>
                                <th>Peso</th>
                                <th>Variação</th>
                                <th>Notas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($weight_log as $i => $entry):
                                $prev = $weight_log[$i + 1]['weight_kg'] ?? null;
                                $diff = $prev !== null ? round($entry['weight_kg'] - $prev, 1) : null;
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
                                    <?php if ($membership === $plan): ?>
                                        <span class="plan-active-tag">Atual</span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn-save">Atualizar Plano</button>
                </form>
            </div>
        </details>

    </section>
</div>

<?php if (count($weight_log) >= 2): ?>
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

    function xOf(i)  { return PAD.left + (i / (data.length - 1)) * plotW; }
    function yOf(v)  { return PAD.top  + plotH - ((v - minV) / (maxV - minV)) * plotH; }

    const orange = '#f27a38';
    const muted  = '#86868b';
    const border = '#d2d2d7';

    // Grid lines
    ctx.strokeStyle = border;
    ctx.lineWidth = 1;
    const steps = 4;
    for (let s = 0; s <= steps; s++) {
        const v = minV + ((maxV - minV) / steps) * s;
        const y = yOf(v);
        ctx.beginPath();
        ctx.moveTo(PAD.left, y);
        ctx.lineTo(W - PAD.right, y);
        ctx.stroke();
        ctx.fillStyle = muted;
        ctx.font = '11px Segoe UI, sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText(v.toFixed(1), PAD.left - 8, y + 4);
    }

    // X labels
    ctx.fillStyle = muted;
    ctx.font = '11px Segoe UI, sans-serif';
    ctx.textAlign = 'center';
    data.forEach((_, i) => {
        if (data.length <= 12 || i % Math.ceil(data.length / 10) === 0) {
            ctx.fillText(labels[i], xOf(i), H - 10);
        }
    });

    // Gradient fill
    const grad = ctx.createLinearGradient(0, PAD.top, 0, H - PAD.bottom);
    grad.addColorStop(0, 'rgba(242,122,56,0.18)');
    grad.addColorStop(1, 'rgba(242,122,56,0)');
    ctx.beginPath();
    ctx.moveTo(xOf(0), yOf(data[0]));
    for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
    ctx.lineTo(xOf(data.length - 1), H - PAD.bottom);
    ctx.lineTo(xOf(0), H - PAD.bottom);
    ctx.closePath();
    ctx.fillStyle = grad;
    ctx.fill();

    // Line
    ctx.beginPath();
    ctx.moveTo(xOf(0), yOf(data[0]));
    for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
    ctx.strokeStyle = orange;
    ctx.lineWidth = 2.5;
    ctx.lineJoin = 'round';
    ctx.stroke();

    // Dots
    data.forEach((v, i) => {
        ctx.beginPath();
        ctx.arc(xOf(i), yOf(v), 4, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.strokeStyle = orange;
        ctx.lineWidth = 2;
        ctx.stroke();
    });

    // Tooltip on hover
    canvas.addEventListener('mousemove', function(e) {
        const rect  = canvas.getBoundingClientRect();
        const mx    = (e.clientX - rect.left) * (W / rect.width);
        let closest = 0, minDist = Infinity;
        data.forEach((_, i) => {
            const d = Math.abs(xOf(i) - mx);
            if (d < minDist) { minDist = d; closest = i; }
        });

        canvas.width = W; 
        drawChart();
        const tx = xOf(closest);
        const ty = yOf(data[closest]);
        const tip = `${labels[closest]}: ${data[closest].toFixed(1)} kg`;

        ctx.font = 'bold 12px Segoe UI, sans-serif';
        const tw = ctx.measureText(tip).width + 16;
        let bx = tx - tw / 2;
        if (bx < 4) bx = 4;
        if (bx + tw > W - 4) bx = W - tw - 4;

        ctx.fillStyle = '#1d1d1f';
        ctx.beginPath();
        ctx.roundRect(bx, ty - 36, tw, 24, 6);
        ctx.fill();
        ctx.fillStyle = '#fff';
        ctx.textAlign = 'left';
        ctx.fillText(tip, bx + 8, ty - 20);

        // Highlight dot
        ctx.beginPath();
        ctx.arc(tx, ty, 6, 0, Math.PI * 2);
        ctx.fillStyle = orange;
        ctx.fill();
    });

    function drawChart() {
        ctx.clearRect(0, 0, W, H);

        ctx.strokeStyle = border;
        ctx.lineWidth = 1;
        for (let s = 0; s <= steps; s++) {
            const v = minV + ((maxV - minV) / steps) * s;
            const y = yOf(v);
            ctx.beginPath();
            ctx.moveTo(PAD.left, y);
            ctx.lineTo(W - PAD.right, y);
            ctx.stroke();
            ctx.fillStyle = muted;
            ctx.font = '11px Segoe UI, sans-serif';
            ctx.textAlign = 'right';
            ctx.fillText(v.toFixed(1), PAD.left - 8, y + 4);
        }

        ctx.fillStyle = muted;
        ctx.font = '11px Segoe UI, sans-serif';
        ctx.textAlign = 'center';
        data.forEach((_, i) => {
            if (data.length <= 12 || i % Math.ceil(data.length / 10) === 0) {
                ctx.fillText(labels[i], xOf(i), H - 10);
            }
        });

        ctx.beginPath();
        ctx.moveTo(xOf(0), yOf(data[0]));
        for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
        ctx.lineTo(xOf(data.length - 1), H - PAD.bottom);
        ctx.lineTo(xOf(0), H - PAD.bottom);
        ctx.closePath();
        ctx.fillStyle = grad;
        ctx.fill();

        ctx.beginPath();
        ctx.moveTo(xOf(0), yOf(data[0]));
        for (let i = 1; i < data.length; i++) ctx.lineTo(xOf(i), yOf(data[i]));
        ctx.strokeStyle = orange;
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.stroke();

        data.forEach((v, i) => {
            ctx.beginPath();
            ctx.arc(xOf(i), yOf(v), 4, 0, Math.PI * 2);
            ctx.fillStyle = '#fff';
            ctx.fill();
            ctx.strokeStyle = orange;
            ctx.lineWidth = 2;
            ctx.stroke();
        });
    }

    canvas.addEventListener('mouseleave', drawChart);
})();
</script>
<?php endif; ?>

<?php } ?>