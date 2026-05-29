<?php
declare(strict_types=1);

function output_public_nutritionists(array $nutritionists, bool $is_admin = false): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">nutricionistas</span></h1>
        <p class="trainers-hero-subtitle">Especialistas focados na tua alimentação, para potenciar os teus resultados.</p>
    </section>

    <section class="trainers-list nutrition-list" id="nutritionists">
        <div class="nutrition-grid">
            <?php foreach ($nutritionists as $nutri): 
                $photo_src = !empty($nutri['photo']) ? "../imgs/" . htmlspecialchars($nutri['photo']) : "../imgs/default-avatar.png";
            ?>
                <article class="nutrition-card-member" data-id="<?= $nutri['id'] ?>">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Foto de <?= htmlspecialchars($nutri['name']) ?>" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <a href="login.php" class="btn-text" style="margin-top: 15px; display: inline-block;">Fazer login para contactar</a>
                        
                        <?php if ($is_admin): ?>
                        <button type="button" class="admin-delete-nutritionist" data-id="<?= $nutri['id'] ?>"
                                style="margin-top:10px;width:100%;background:none;border:1px solid #e53e3e;color:#e53e3e;padding:7px 0;border-radius:20px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s;">
                            Remover
                        </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
            <article id="trigger-add-nutritionist"
                     style="cursor:pointer;display:flex;align-items:center;justify-content:center;
                            border:2px dashed var(--orange);background-color:#fffbf9;border-radius:var(--radius-lg);
                            min-height:320px;transition:var(--transition);">
                <div style="text-align:center;color:var(--orange);">
                    <span style="font-size:5rem;font-weight:200;display:block;line-height:1;margin-bottom:5px;">+</span>
                    <span style="font-weight:700;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;">Adicionar Nutricionista</span>
                </div>
            </article>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($is_admin): output_nutritionist_modal(); output_nutritionist_admin_js(); endif; ?>
<?php
}

function output_member_nutrition_view(?array $plan, array $nutritionists, bool $is_admin = false): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">EQUIPA</p>
        <h1>Os nossos <span class="highlight">nutricionistas</span></h1>
        <p class="trainers-hero-subtitle">Especialistas focados na tua alimentação, para potenciar os teus resultados.</p>
    </section>

    <div class="nutrition-dashboard">
        
        <?php if ($plan): ?>
            <div class="class-card" style="margin-bottom: 50px; text-align: center;">
                <h2>O teu Plano Nutricional</h2>
                <p style="color: var(--text-muted);">Prescrito por: <strong><?= htmlspecialchars($plan['nutritionist_name']) ?></strong></p>
                <div style="margin-top: 15px;">
                    <?= nl2br(htmlspecialchars($plan['content'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="nutrition-grid">
            <?php foreach ($nutritionists as $nutri): 
                $photo_src = !empty($nutri['photo']) ? "../imgs/" . htmlspecialchars($nutri['photo']) : "../imgs/default-avatar.png";
            ?>
                <article class="nutrition-card-member" data-id="<?= $nutri['id'] ?>">
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Nutricionista" class="nutritionist-photo" onerror="this.onerror=null; this.src='../imgs/default-avatar.png';">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <?php if (!$is_admin): ?>
                        <a href="nutritionist_profile.php?id=<?= $nutri['id'] ?>" class="btn-text" style="margin-top: 15px; display: inline-block;">Ver perfil e Marcar</a>
                        <?php endif; ?>
                        
                        <?php if ($is_admin): ?>
                        <button type="button" class="admin-delete-nutritionist" data-id="<?= $nutri['id'] ?>"
                                style="margin-top:10px;width:100%;background:none;border:1px solid #e53e3e;color:#e53e3e;padding:7px 0;border-radius:20px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s;">
                            Remover
                        </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
            <article id="trigger-add-nutritionist"
                     style="cursor:pointer;display:flex;align-items:center;justify-content:center;
                            border:2px dashed var(--orange);background-color:#fffbf9;border-radius:var(--radius-lg);
                            min-height:320px;transition:var(--transition);">
                <div style="text-align:center;color:var(--orange);">
                    <span style="font-size:5rem;font-weight:200;display:block;line-height:1;margin-bottom:5px;">+</span>
                    <span style="font-weight:700;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;">Adicionar Nutricionista</span>
                </div>
            </article>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($is_admin): output_nutritionist_modal(); output_nutritionist_admin_js(); endif; ?>
<?php }

function output_nutritionist_modal(): void { ?>
<!-- ══ MODAL ADICIONAR NUTRICIONISTA ══ -->
<div id="add-nutritionist-modal" class="add-class-modal-overlay hidden">
    <article style="width:420px;max-width:95vw;max-height:90vh;overflow-y:auto;background:#fff;border-radius:12px;padding:30px;position:relative;box-shadow:0 8px 30px rgba(0,0,0,0.15);animation:modalSlideUp 0.3s ease;box-sizing:border-box;">
        <button type="button" id="close-add-nutritionist" style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:2rem;color:#888;cursor:pointer;line-height:1;">&times;</button>
        <p style="font-weight:700;color:var(--orange);text-transform:uppercase;letter-spacing:0.05em;font-size:0.8rem;text-align:center;margin-bottom:5px;">NOVO NUTRICIONISTA</p>

        <form action="admin.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_nutritionist">

            <div style="margin-bottom:20px;text-align:center;">
                <input type="text" name="nu_name" placeholder="Nome completo" required
                       style="font-size:1.3rem;font-weight:800;text-align:center;border:none;border-bottom:2px solid var(--gray-border);width:100%;padding:4px;color:var(--text-main);outline:none;font-family:inherit;background:transparent;">
            </div>

            <div style="display:flex;flex-direction:column;border-top:1px solid var(--gray-border);border-bottom:1px solid var(--gray-border);padding:10px 0;margin-bottom:20px;">

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Username</strong>
                    <input type="text" name="nu_username" placeholder="username" required
                           style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Email</strong>
                    <input type="email" name="nu_email" placeholder="email@exemplo.com" required
                           style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Password</strong>
                    <input type="password" name="nu_password" placeholder="mínimo 6 caracteres" required minlength="6"
                           style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Especialização</strong>
                    <input type="text" name="nu_specialization" placeholder="ex: Desportiva, Clínica"
                           style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Nº Cédula</strong>
                    <input type="text" name="nu_license" placeholder="ex: ON-12345"
                           style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                    <strong style="font-size:0.85rem;color:var(--text-muted);">Foto</strong>
                    <div style="width:220px;">
                        <label for="nu-photo-input" id="nu-photo-label"
                               style="display:block;padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;cursor:pointer;font-size:0.8rem;color:var(--text-muted);text-align:center;background:#fff;box-sizing:border-box;width:100%;">
                            📷 Escolher foto
                        </label>
                        <input type="file" id="nu-photo-input" name="nu_photo" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>

            <button type="submit" style="width:100%;background:var(--black);color:#fff;border:none;padding:12px;border-radius:30px;font-size:0.9rem;font-weight:600;cursor:pointer;">
                Adicionar Nutricionista
            </button>
        </form>
    </article>
</div>

<style>
.add-class-modal-overlay { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:flex;justify-content:center;align-items:center;z-index:99999; }
.add-class-modal-overlay.hidden { display:none; }
@keyframes modalSlideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
.admin-delete-nutritionist:hover { background:#e53e3e !important; color:#fff !important; }
</style>
<?php }

function output_nutritionist_admin_js(): void { ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger  = document.getElementById('trigger-add-nutritionist');
    const modal    = document.getElementById('add-nutritionist-modal');
    const closeBtn = document.getElementById('close-add-nutritionist');
    if (trigger && modal) {
        trigger.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
    }
    const inp = document.getElementById('nu-photo-input');
    const lbl = document.getElementById('nu-photo-label');
    if (inp) inp.addEventListener('change', function() { lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto'; });

    document.querySelectorAll('.admin-delete-nutritionist').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Remover este nutricionista permanentemente?')) return;
            const id   = this.dataset.id;
            const card = this.closest('article');
            fetch('admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete_user&id=' + encodeURIComponent(id)
            }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover nutricionista.'); });
        });
    });
});
</script>
<?php } ?>