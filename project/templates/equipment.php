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
                <option value="all">Todos os Pisos</option>
                <?php foreach($unique_locations as $loc): ?>
                    <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-muscle" class="filter-select">
                <option value="all">Todos os Músculos</option>
                <?php foreach($unique_muscles as $musc): ?>
                    <option value="<?= htmlspecialchars($musc) ?>"><?= htmlspecialchars($musc) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filter-status" class="filter-select">
                <option value="all">Qualquer Estado</option>
                <option value="Disponível">Disponível</option>
                <option value="Manutenção">Em Manutenção</option>
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
                    
                    <?php if ($is_admin): ?>
                    <button type="button" class="admin-delete-equipment" data-id="<?= $id ?>">
                        Remover
                    </button>
                    <?php endif; ?>
                    
                </article>
            <?php endforeach; ?>

            <?php if ($is_admin): ?>
            <!-- Card "+" para adicionar equipamento -->
            <article class="class-card" id="trigger-add-equipment">
                <div>
                    <span>+</span>
                    <span>Adicionar Equipamento</span>
                </div>
            </article>
            <?php endif; ?>

        </div>
    </div>

    <?php if ($is_admin): ?>
    <!-- ══ MODAL ADICIONAR EQUIPAMENTO ══ -->
    <div id="add-equipment-modal" class="add-class-modal-overlay hidden">
        <article>
            <button type="button" id="close-add-equipment">&times;</button>
            <p>NOVO EQUIPAMENTO</p>

            <form action="admin.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="action" value="add_equipment">

                <div>
                    <input type="text" name="eq_name" placeholder="Nome do Equipamento" required>
                </div>

                <div>

                    <div>
                        <strong>Foto</strong>
                        <div>
                            <label for="eq-photo-input" id="eq-photo-label">
                                📷 Escolher foto
                            </label>
                            <input type="file" id="eq-photo-input" name="eq_photo" accept="image/*">
                        </div>
                    </div>

                    <div >
                        <strong>Grupo Muscular</strong>
                        <select name="eq_muscle">
                            <option value="">Seleciona...</option>
                            <option value="1">Quadríceps e Glúteos</option>
                            <option value="2">Peitoral e Tríceps</option>
                            <option value="3">Dorsais e Bíceps</option>
                            <option value="4">Ombros</option>
                            <option value="5">Isquiotibiais e Panturrilhas</option>
                            <option value="6">Core e Abdominais</option>
                        </select>
                    </div>

                    <div>
                        <strong>Localização</strong>
                        <input type="text" name="eq_location" placeholder="ex: Piso 1 - Força" required>
                    </div>

                    <div>
                        <strong>Vídeo (URL embed)</strong>
                        <input type="text" name="eq_video" placeholder="https://youtube.com/embed/...">
                    </div>
                </div>

                <button type="submit">
                    Adicionar Equipamento
                </button>
            </form>
        </article>
    </div>
    <?php endif; ?>
<?php } ?>