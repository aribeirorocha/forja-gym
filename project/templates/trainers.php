<?php
require_once('../database/trainer.php');
?>

<?php function output_trainers_hero() { ?>
  <section class="trainers-hero">
    <p class="section-tag">EQUIPA</p>
    <h1>Os nossos <span class="highlight">trainers</span></h1>
    <p class="trainers-hero-subtitle">Especialistas dedicados ao teu progresso, cada um com a sua área de excelência.</p>
  </section>
<?php } ?>

<?php function output_trainers(array $trainers, bool $is_admin = false) { ?>
  <section class="trainers-list" id="trainers">

    <div class="filters-bar">
        <select id="filter-gender" class="filter-select">
            <option value="all">🚻 Todos os Géneros</option>
            <option value="masculino">Homens</option>
            <option value="feminino">Mulheres</option>
        </select>

        <select id="sort-trainers" class="filter-select">
            <option value="default">↕️ Ordenar por...</option>
            <option value="age-asc">Mais novos</option>
            <option value="age-desc">Mais velhos</option>
            <option value="time-desc">Mais tempo no ginásio</option>
            <option value="time-asc">Menos tempo no ginásio</option>
        </select>
    </div>

    <div class="trainer-grid" id="trainers-grid-container">

      <?php foreach ($trainers as $trainer) {
          $ano_entrada = $trainer->trainerCreatedAt->format('Y');
          $idade       = $trainer->age ?? 0;
          $genero      = isset($trainer->sex) ? strtolower($trainer->sex) : 'all';
      ?>
        <article class="trainer-card"
                 data-id="<?= $trainer->id ?>"
                 data-gender="<?= htmlspecialchars($genero) ?>"
                 data-age="<?= $idade ?>"
                 data-year="<?= $ano_entrada ?>">

          <div class="trainer-summary">
            <img src="<?= htmlspecialchars($trainer->photoPath) ?>" alt="<?= htmlspecialchars($trainer->name) ?>" class="trainer-photo">
            <h3><?= htmlspecialchars($trainer->name) ?></h3>
            <p class="trainer-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($trainer->specialization)) ?></p>

            <a href="trainer_profile.php?id=<?= $trainer->id ?>" class="btn-text" style="margin-top:15px;display:inline-block;">Ver perfil</a>

            <?php if ($is_admin): ?>
            <button type="button"
                    class="admin-delete-trainer"
                    data-id="<?= $trainer->id ?>"
                    style="margin-top:10px;width:100%;background:none;border:1px solid #e53e3e;color:#e53e3e;padding:7px 0;border-radius:20px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s;">
                Remover
            </button>
            <?php endif; ?>
          </div>
        </article>
      <?php } ?>

      <?php if ($is_admin): ?>
      <!-- Card "+" para adicionar trainer -->
      <article id="trigger-add-trainer"
               style="cursor:pointer;display:flex;align-items:center;justify-content:center;
                      border:2px dashed var(--orange);background-color:#fffbf9;border-radius:var(--radius-lg);
                      min-height:320px;transition:var(--transition);">
          <div style="text-align:center;color:var(--orange);">
              <span style="font-size:5rem;font-weight:200;display:block;line-height:1;margin-bottom:5px;">+</span>
              <span style="font-weight:700;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;">Adicionar Trainer</span>
          </div>
      </article>
      <?php endif; ?>

    </div>
  </section>

  <?php if ($is_admin): ?>
  <!-- ══ MODAL ADICIONAR TRAINER ══ -->
  <div id="add-trainer-modal" class="add-class-modal-overlay hidden">
      <article style="width:420px;max-width:95vw;max-height:90vh;overflow-y:auto;background:#fff;border-radius:12px;padding:30px;position:relative;box-shadow:0 8px 30px rgba(0,0,0,0.15);animation:modalSlideUp 0.3s ease;box-sizing:border-box;">
          <button type="button" id="close-add-trainer" style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:2rem;color:#888;cursor:pointer;line-height:1;">&times;</button>
          <p style="font-weight:700;color:var(--orange);text-transform:uppercase;letter-spacing:0.05em;font-size:0.8rem;text-align:center;margin-bottom:5px;">NOVO TRAINER</p>

          <form action="admin.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="add_trainer">

              <div style="margin-bottom:20px;text-align:center;">
                  <input type="text" name="trainer_name" placeholder="Nome completo" required
                         style="font-size:1.3rem;font-weight:800;text-align:center;border:none;border-bottom:2px solid var(--gray-border);width:100%;padding:4px;color:var(--text-main);outline:none;font-family:inherit;background:transparent;">
              </div>

              <div style="display:flex;flex-direction:column;gap:0;border-top:1px solid var(--gray-border);border-bottom:1px solid var(--gray-border);padding:10px 0;margin-bottom:20px;">

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Email</strong>
                      <input type="email" name="trainer_email" placeholder="nometrainer@gmail.com" required
                             style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                  </div>

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Password</strong>
                      <input type="password" name="trainer_password" placeholder="••••••••" required
                             style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                  </div>

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Género</strong>
                      <select name="trainer_sex" style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;font-family:inherit;font-size:0.85rem;background:#fff;color:var(--text-main);">
                          <option value="masculino">Masculino</option>
                          <option value="feminino">Feminino</option>
                      </select>
                  </div>

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Especialização</strong>
                      <input type="text" name="trainer_specialization" placeholder="ex: Musculação, Cardio"
                             style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                  </div>

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Foto</strong>
                      <div style="width:220px;">
                          <label for="trainer-photo-input" id="trainer-photo-label"
                                 style="display:block;padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;cursor:pointer;font-size:0.8rem;color:var(--text-muted);text-align:center;background:#fff;box-sizing:border-box;width:100%;">
                              📷 Escolher foto
                          </label>
                          <input type="file" id="trainer-photo-input" name="trainer_photo" accept="image/*" style="display:none;">
                      </div>
                  </div>

                  <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                      <strong style="font-size:0.85rem;color:var(--text-muted);">Localização</strong>
                      <input type="text" name="trainer_location" placeholder="ex: Lisboa"
                             style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:220px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                  </div>
              </div>

              <button type="submit" style="width:100%;background:var(--black);color:#fff;border:none;padding:12px;border-radius:30px;font-size:0.9rem;font-weight:600;cursor:pointer;">
                  Adicionar Trainer
              </button>
          </form>
      </article>
  </div>

  <style>
  .add-class-modal-overlay { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:flex;justify-content:center;align-items:center;z-index:99999; }
  .add-class-modal-overlay.hidden { display:none; }
  @keyframes modalSlideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
  .admin-delete-trainer:hover { background:#e53e3e !important; color:#fff !important; }
  </style>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      // Abrir/fechar modal
      const trigger  = document.getElementById('trigger-add-trainer');
      const modal    = document.getElementById('add-trainer-modal');
      const closeBtn = document.getElementById('close-add-trainer');
      if (trigger && modal) {
          trigger.addEventListener('click', () => modal.classList.remove('hidden'));
          closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
          modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
      }

      // Preview nome da foto
      const inp = document.getElementById('trainer-photo-input');
      const lbl = document.getElementById('trainer-photo-label');
      if (inp) inp.addEventListener('change', function() {
          lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto';
      });

      // Remover trainer via fetch
      document.querySelectorAll('.admin-delete-trainer').forEach(function(btn) {
          btn.addEventListener('click', function() {
              if (!confirm('Remover este trainer permanentemente?')) return;
              const id   = this.dataset.id;
              const card = this.closest('article');
              fetch('admin.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: 'action=delete_user&id=' + encodeURIComponent(id)
              }).then(r => { if (r.ok) card.remove(); else alert('Erro ao remover trainer.'); });
          });
      });
  });
  </script>
  <?php endif; ?>

<?php } ?>

<?php function output_featured_trainers(array $trainers) { ?>
  <section class="trainers">
    <header class="section-header">
      <p class="section-tag">EQUIPA</p>
      <h2>Os nossos trainers</h2>
      <p>Especialistas dedicados ao teu progresso.</p>
    </header>

    <div class="trainer-grid">
      <?php foreach ($trainers as $trainer) { ?>
        <article class="trainer-card">
          <img src=<?= $trainer->photoPath ?> alt=<?= $trainer->name ?> class="trainer-photo">
          <h3><?= $trainer->name ?></h3>
          <p><?= "Especialista em " . str_replace(',', ' e ', $trainer->specialization) ?></p>
          <a href="trainer_profile.php?id=<?= $trainer->id ?>" class="btn-text"> Ver perfil </a>
        </article>
      <?php } ?>
    </div>
  </section>
<?php } ?>