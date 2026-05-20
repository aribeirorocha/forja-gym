<?php
declare(strict_types=1);

/**
 * Vista para Visitantes: Apenas a Foto e o Nome (Cartões maiores)
 */
function output_public_equipment(array $equipments): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">INSTALAÇÕES</p>
        <h1>O nosso <span class="highlight">equipamento</span></h1>
        <p class="trainers-hero-subtitle">Maquinaria de topo para garantir que atinges os teus limites.</p>
    </section>

    <div style="padding: 60px 5%; max-width: 1200px; margin: 0 auto;">
        <div class="class-grid">
            <?php foreach ($equipments as $eq): 
                $photo_src = !empty($eq['photo']) ? "../imgs/" . htmlspecialchars($eq['photo']) : "../imgs/default-equipment.png";
            ?>
                <article class="class-card">
                    <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($eq['name']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.4rem; color: var(--text-main); margin-bottom: 10px;"><?= htmlspecialchars($eq['name']) ?></h3>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}

/**
 * Vista para Membros: Informação completa, Músculos e Vídeo
 */
function output_member_equipment(array $equipments): void {
?>
    <section class="trainers-hero">
        <p class="section-tag">TREINO</p>
        <h1>Como usar o <span class="highlight">equipamento</span></h1>
        <p class="trainers-hero-subtitle">Aprende a executar os movimentos corretamente.</p>
    </section>

    <div style="padding: 60px 5%; max-width: 1200px; margin: 0 auto;">
        <div class="class-grid">
            <?php foreach ($equipments as $eq): 
                $photo_src = !empty($eq['photo']) ? "../imgs/" . htmlspecialchars($eq['photo']) : "../imgs/default-equipment.png";
                $muscle = $eq['muscle_name'] ?? 'Corpo Inteiro';
            ?>
                <article class="class-card" style="padding: 30px;">
                    <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($eq['name']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.4rem; color: var(--text-main); margin-bottom: 15px;"><?= htmlspecialchars($eq['name']) ?></h3>
                    
                    <button class="btn-text btn-toggle-eq" data-id="<?= $eq['id'] ?>" style="width: 100%; margin-top: 0;">Mais informações</button>
                    
                    <div class="eq-details hidden" id="eq-info-<?= $eq['id'] ?>" style="width: 100%; margin-top: 20px; text-align: left; border-top: 1px solid var(--gray-border); padding-top: 20px;">
                        
                        <p style="font-size: 0.9rem; margin-bottom: 15px;">
                            <strong>Foco Muscular:</strong> <span style="color: var(--orange); font-weight: 600;"><?= htmlspecialchars($muscle) ?></span>
                        </p>
                        
                        <?php if(!empty($eq['video_url'])): ?>
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                                <iframe src="<?= htmlspecialchars($eq['video_url']) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php } ?>