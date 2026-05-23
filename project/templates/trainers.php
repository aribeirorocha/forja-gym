<?php
require_once('../database/trainer.php');
?>

<?php function output_trainers_hero()
{ ?>
  <section class="trainers-hero">
    <p class="section-tag">EQUIPA</p>
    <h1>Os nossos <span class="highlight">trainers</span></h1>
    <p class="trainers-hero-subtitle">Especialistas dedicados ao teu progresso, cada um com a sua área de excelência.</p>
  </section>
<?php } ?>

<?php function output_trainers(array $trainers) { ?>
  <section class="trainers-list" id="trainers">
    
    <div class="filters-bar">
        <select id="filter-gender" class="filter-select">
            <option value="all">🚻 Todos os Géneros</option>
            <option value="masculino">👨 Homens</option>
            <option value="feminino">👩 Mulheres</option>
        </select>

        <select id="sort-trainers" class="filter-select">
            <option value="default">↕️ Ordenar por...</option>
            <option value="age-asc">👶 Idade (Mais novos primeiro)</option>
            <option value="age-desc">👴 Idade (Mais velhos primeiro)</option>
            <option value="time-desc">⭐ Mais tempo no ginásio</option>
            <option value="time-asc">🌱 Menos tempo no ginásio</option>
        </select>
    </div>

    <div class="trainer-grid" id="trainers-grid-container">

      <?php foreach ($trainers as $trainer) { 
          // Prepara os dados para a ordenação/filtragem
          $ano_entrada = $trainer->trainerCreatedAt->format('Y');
          $idade = $trainer->age ?? 0;
          
          // A CORREÇÃO ESTÁ AQUI: Agora usamos $trainer->sex para ler a BD corretamente
          $genero = isset($trainer->sex) ? strtolower($trainer->sex) : 'all'; 
      ?>
      
        <article class="trainer-card" 
                 data-gender="<?= htmlspecialchars($genero) ?>" 
                 data-age="<?= $idade ?>" 
                 data-year="<?= $ano_entrada ?>">
                 
          <div class="trainer-summary">
            <img src="<?= htmlspecialchars($trainer->photoPath) ?>" alt="<?= htmlspecialchars($trainer->name) ?>" class="trainer-photo">
            <h3><?= htmlspecialchars($trainer->name) ?></h3>
            <p class="trainer-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($trainer->specialization)) ?></p>
            
            <a href="trainer_profile.php?id=<?= $trainer->id ?>" class="btn-text" style="margin-top: 15px; display: inline-block;">Ver perfil</a>
          </div>
        </article>
      <?php } ?>
      
    </div>
  </section>
<?php } ?>44

<?php function output_featured_trainers(array $trainers)
{ ?>
  <section class="trainers">
    <header class="section-header">
      <p class="section-tag">EQUIPA</p>
      <h2>Os nossos trainers</h2>
      <p>Especialistas dedicados ao teu progresso .</p>
    </header>

    <div class="trainer-grid">
      <?php foreach ($trainers as $trainer) { ?>
        <article class="trainer-card">
          <img src=<?= $trainer->photoPath ?> alt=<?= $trainer->name ?> class="trainer-photo">
          <h3><?= $trainer->name ?></h3>
          <p><?= "Especialista em " . str_replace(',', ' e ', $trainer->specialization) ?></p>
          <a href=<?= "trainers.php#$trainer->username" ?> class="btn-text"> Ver perfil </a>
        </article>
      <?php } ?>
    </div>
  </section>
<?php } ?>