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

<?php function output_trainers(array $trainers)
{ ?>
  <section class="trainers-list" id="trainers">
    <div class="trainer-grid">

      <?php foreach ($trainers as $trainer) { ?>
        <article class="trainer-card">
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
<?php } ?>

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