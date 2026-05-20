<?php
declare(strict_types=1);

/**
 * Vista para visitantes: lista todos os nutricionistas da base de dados
 */
function output_public_nutritionists(array $nutritionists): void {
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
                <article class="trainer-card nutritionist-public-card">
                    <div class="trainer-summary nutritionist-public-summary">
                        
                        <img src="<?= $photo_src ?>" alt="Foto de <?= htmlspecialchars($nutri['name']) ?>" class="trainer-photo nutritionist-public-photo" onerror="this.src='../imgs/default-avatar.png'">
                        
                        <h3 class="nutritionist-public-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="trainer-specialty nutritionist-public-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <a href="login.php" class="btn-outline">
                            Fazer login para contactar
                        </a>
                        
                    </div>
                </article>
            <?php endforeach; ?>
            
        </div>
    </section>
<?php
}
 function output_member_nutrition_view(?array $plan, array $nutritionists): void {
    
    // Preparar as horas disponíveis
    $slots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
    
    $dias_semana = ['Sun'=>'Dom', 'Mon'=>'Seg', 'Tue'=>'Ter', 'Wed'=>'Qua', 'Thu'=>'Qui', 'Fri'=>'Sex', 'Sat'=>'Sáb'];
    $next_days = [];
    for ($i = 0; $i < 7; $i++) {
        $timestamp = strtotime("+$i days");
        $next_days[] = [
            'date_val' => date('Y-m-d', $timestamp),
            'label' => date('d/m', $timestamp),
            'day_pt' => $dias_semana[date('D', $timestamp)]
        ];
    }
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
                <article class="nutrition-card-member">
                    
                    <div class="nutritionist-summary">
                        <img src="<?= $photo_src ?>" alt="Nutricionista" class="nutritionist-photo" onerror="this.src='../imgs/default-avatar.png'">
                        <h3 class="nutritionist-name"><?= htmlspecialchars($nutri['name']) ?></h3>
                        <p class="nutritionist-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($nutri['specialization'])) ?></p>
                        
                        <button class="btn-outline-booking btn-toggle-booking" data-id="<?= $nutri['id'] ?>">Marcar Consulta</button>
                    </div>

                    <div class="booking-section hidden" id="booking-<?= $nutri['id'] ?>">
                        <hr class="booking-divider">
                        
                        <form method="post" action="nutrition.php">
                            <input type="hidden" name="action" value="schedule_appointment">
                            <input type="hidden" name="nutritionist_id" value="<?= $nutri['id'] ?>">
                            
                            <div class="calendar-wrapper">
                                <div class="calendar-grid">
                                    <?php foreach($next_days as $day): ?>
                                        <div class="calendar-day-col">
                                            <div class="calendar-header">
                                                <span class="day-name"><?= $day['day_pt'] ?></span>
                                                <span class="day-date"><?= $day['label'] ?></span>
                                            </div>
                                            <div class="calendar-slots">
                                                <?php foreach($slots as $time): 
                                                    $slotValue = $day['date_val'] . ' ' . $time;
                                                    $slotId = 'slot_' . $nutri['id'] . '_' . $day['date_val'] . '_' . str_replace(':','',$time);
                                                ?>
                                                    <div class="time-slot">
                                                        <input type="radio" name="date" value="<?= $slotValue ?>" id="<?= $slotId ?>" required>
                                                        <label for="<?= $slotId ?>"><?= $time ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn-schedule">Confirmar Marcação</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
<?php } ?>