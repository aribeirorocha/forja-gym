<?php 
function output_profile(array $user, string $success_msg = '', string $error_msg = '') { 
    // Garante que o CSS do perfil é carregado (se não estiver já no common.php)
?>
    <link rel="stylesheet" href="../css/profile.css">

    <div class="profile-dashboard">
        
        <aside class="profile-sidebar profile-card">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_photo">
                
                <div class="profile-photo-container">
                    <img src="../imgs/default-avatar.png" alt="Foto de Perfil" class="profile-photo-large"
                         onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 150 150%22><circle cx=%2275%22 cy=%2275%22 r=%2275%22 fill=%22%23f27a38%22/><text x=%2275%22 y=%2295%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22 font-family=%22Arial%22><?= strtoupper(substr($user['name'], 0, 1)) ?></text></svg>'">
                    
                    <label for="photo-upload" class="photo-upload-overlay" title="Alterar Foto">
                        &#9998; </label>
                    <input type="file" id="photo-upload" name="profile_photo" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </div>
            </form>

            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <p class="badge">@<?= htmlspecialchars($user['username']) ?></p>
            <p class="badge badge-gold">Membro Ativo</p>
            <p style="margin-top: 20px; font-size: 0.9rem; color: #86868b;">Membro desde 2026</p>
        </aside>

        <section class="profile-content">
            
            <?php if ($success_msg): ?>
                <div style="background: #e6f4ea; color: #137333; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <?= htmlspecialchars($success_msg) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div style="background: #fce8e6; color: #c5221f; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <article class="profile-card" style="margin-bottom: 40px;">
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

            <article class="profile-card">
                <h3 class="card-title">Segurança</h3>
                <form action="profile.php" method="post">
                    <input type="hidden" name="action" value="update_password">
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Password Atual</label>
                            <input type="password" name="current_password" placeholder="Necessário para alterar a password">
                        </div>
                        <div class="form-group">
                            <label>Nova Password</label>
                            <input type="password" name="new_password">
                        </div>
                        <div class="form-group">
                            <label>Confirmar Nova Password</label>
                            <input type="password" name="confirm_password">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Atualizar Password</button>
                </form>
            </article>

        </section>
    </div>
<?php } ?>