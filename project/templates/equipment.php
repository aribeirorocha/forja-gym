<?php
declare(strict_types=1);

/**
 * Vista para Visitantes: Apenas a Foto e o Nome (Cartões maiores)
 */
function output_public_equipment(array $equipments, bool $is_admin = false): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">INSTALAÇÕES</p>
        <h1>O nosso <span class="highlight">equipamento</span></h1>
        <p class="trainers-hero-subtitle">Maquinaria de topo para garantir que atinges os teus limites.</p>
    </section>

    <div class="equipment-container">
        <div class="class-grid">
            <?php foreach ($equipments as $eq): 
                $photo_src = !empty($eq['photo']) ? "../imgs/" . htmlspecialchars($eq['photo']) : "../imgs/default-equipment.png";
            ?>
                <article class="class-card equipment-card">
                    <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($eq['name']) ?>" class="equipment-photo">
                    <h3 class="equipment-name"><?= htmlspecialchars($eq['name']) ?></h3>
                    
                    <a href="login.php" class="btn-text btn-equipment-bottom">Login mais informações</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}
function output_member_equipment(array $equipments, bool $is_admin = false): void {
    $all_locations = [];
    $all_muscles = [];
    foreach ($equipments as $eq) {
        $all_locations[] = $eq['location'] ?? 'Não especificada';
        $all_muscles[] = $eq['muscle_name'] ?? 'Corpo Inteiro';
    }
    $unique_locations = array_unique($all_locations);
    $unique_muscles = array_unique($all_muscles);
    sort($unique_locations);
    sort($unique_muscles);
?>
    <section class="trainers-hero">
        <p class="section-tag">TREINO</p>
        <h1>Como usar o <span class="highlight">equipamento</span></h1>
        <p class="trainers-hero-subtitle">Aprende a executar os movimentos corretamente.</p>
    </section>

    <div class="equipment-container">
        
        <div class="filters-bar">
            <select id="filter-location" class="filter-select">
                <option value="all">📍 Todos os Pisos</option>
                <?php foreach($unique_locations as $loc): ?>
                    <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-muscle" class="filter-select">
                <option value="all">💪 Todos os Músculos</option>
                <?php foreach($unique_muscles as $musc): ?>
                    <option value="<?= htmlspecialchars($musc) ?>"><?= htmlspecialchars($musc) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-status" class="filter-select">
                <option value="all">⚙️ Qualquer Estado</option>
                <option value="Disponível">✅ Disponível</option>
                <option value="Manutenção">🛠️ Em Manutenção</option>
            </select>
        </div>

        <div class="class-grid" id="equipment-grid">
            <?php foreach ($equipments as $eq): 
                $photo_src = !empty($eq['photo']) ? "../imgs/" . htmlspecialchars($eq['photo']) : "../imgs/default-equipment.png";
                $muscle = $eq['muscle_name'] ?? 'Corpo Inteiro';
                $location = $eq['location'] ?? 'Não especificada';
                $status_text = $eq['status'] ?? 'Disponível';
                $id = $eq['id'];
            ?>
                <article class="class-card equipment-card"
                         data-id= "<?= $id?>"
                         data-location="<?= htmlspecialchars($location) ?>" 
                         data-muscle="<?= htmlspecialchars($muscle) ?>" 
                         data-status="<?= htmlspecialchars($status_text) ?>">
                         
                    <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($eq['name']) ?>" class="equipment-photo">
                    <h3 class="equipment-name"><?= htmlspecialchars($eq['name']) ?></h3>
                    
                    
                    <a href="equipment_info.php?id=<?= $eq['id'] ?>" class="btn-text btn-equipment-bottom">Mais informações</a>
                    
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
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
            <?php endif; ?>

        </div>
    </div>

    <?php if ($is_admin): ?>
    <!-- ══ MODAL ADICIONAR EQUIPAMENTO ══ -->
    <div id="add-equipment-modal" class="add-class-modal-overlay hidden">
        <article style="width:400px;max-width:95vw;height:auto;background:#fff;border-radius:12px;padding:30px;position:relative;box-shadow:0 8px 30px rgba(0,0,0,0.15);animation:modalSlideUp 0.3s ease;box-sizing:border-box;">
            <button type="button" id="close-add-equipment" style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:2rem;color:#888;cursor:pointer;line-height:1;">&times;</button>
            <p style="font-weight:700;color:var(--orange);text-transform:uppercase;letter-spacing:0.05em;font-size:0.8rem;text-align:center;margin-bottom:5px;">NOVO EQUIPAMENTO</p>

            <form action="admin.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_equipment">

                <div style="margin-bottom:20px;text-align:center;">
                    <input type="text" name="eq_name" placeholder="Nome do Equipamento" required
                           style="font-size:1.3rem;font-weight:800;text-align:center;border:none;border-bottom:2px solid var(--gray-border);width:100%;padding:4px;color:var(--text-main);outline:none;font-family:inherit;background:transparent;">
                </div>

                <div style="display:flex;flex-direction:column;gap:0;border-top:1px solid var(--gray-border);border-bottom:1px solid var(--gray-border);padding:10px 0;margin-bottom:20px;">

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                        <strong style="font-size:0.85rem;color:var(--text-muted);">Foto</strong>
                        <div style="width:200px;">
                            <label for="eq-photo-input" id="eq-photo-label"
                                   style="display:block;padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;cursor:pointer;font-size:0.8rem;color:var(--text-muted);text-align:center;background:#fff;box-sizing:border-box;width:100%;">
                                📷 Escolher foto
                            </label>
                            <input type="file" id="eq-photo-input" name="eq_photo" accept="image/*" style="display:none;">
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                        <strong style="font-size:0.85rem;color:var(--text-muted);">Grupo Muscular</strong>
                        <select name="eq_muscle" style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:200px;font-family:inherit;font-size:0.85rem;background:#fff;color:var(--text-main);">
                            <option value="">Seleciona...</option>
                            <option value="1">Quadríceps e Glúteos</option>
                            <option value="2">Peitoral e Tríceps</option>
                            <option value="3">Dorsais e Bíceps</option>
                            <option value="4">Ombros</option>
                            <option value="5">Isquiotibiais e Panturrilhas</option>
                            <option value="6">Core e Abdominais</option>
                        </select>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-bg);">
                        <strong style="font-size:0.85rem;color:var(--text-muted);">Localização</strong>
                        <input type="text" name="eq_location" placeholder="ex: Piso 1 - Força" required
                               style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:200px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                        <strong style="font-size:0.85rem;color:var(--text-muted);">Vídeo (URL embed)</strong>
                        <input type="text" name="eq_video" placeholder="https://youtube.com/embed/..."
                               style="padding:6px 10px;border:1px solid var(--gray-border);border-radius:6px;width:200px;box-sizing:border-box;font-family:inherit;font-size:0.85rem;color:var(--text-main);background:#fff;">
                    </div>
                </div>

                <button type="submit" style="width:100%;background:var(--black);color:#fff;border:none;padding:12px;border-radius:30px;font-size:0.9rem;font-weight:600;cursor:pointer;">
                    Adicionar Equipamento
                </button>
            </form>
        </article>
    </div>

    <style>
    .add-class-modal-overlay { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:flex;justify-content:center;align-items:center;z-index:99999; }
    .add-class-modal-overlay.hidden { display:none; }
    @keyframes modalSlideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('trigger-add-equipment');
        const modal   = document.getElementById('add-equipment-modal');
        const closeBtn = document.getElementById('close-add-equipment');
        if (trigger && modal) {
            trigger.addEventListener('click', () => modal.classList.remove('hidden'));
            closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
        }
        const inp = document.getElementById('eq-photo-input');
        const lbl = document.getElementById('eq-photo-label');
        if (inp) inp.addEventListener('change', function() { lbl.textContent = this.files[0] ? '✓ ' + this.files[0].name : '📷 Escolher foto'; });
    });
    </script>
    <?php endif; ?>
<?php } ?>