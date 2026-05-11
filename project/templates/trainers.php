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

      <?php foreach ($trainers as $index => $trainer) { ?>
        <article class="trainer-card" id=<?= $trainer->username ?>>
          <input type="checkbox" id=<?= "toggle-t$index" ?> class="trainer-toggle">
          <div class="trainer-summary">
            <img src=<?= $trainer->photoPath ?> alt=<?= $trainer->name ?> class="trainer-photo">
            <h3><?= $trainer->name ?></h3>
            <p class="trainer-specialty">Yoga &bull; Pilates</p>
            <label for=<?= "toggle-t$index" ?> class="btn-text btn-ver-perfil">
              <span class="label-ver">Ver perfil</span>
              <span class="label-fechar">Fechar perfil</span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>32 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2018</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd><?= $trainer->formation ?></dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd><?= $trainer->certification ?></dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Porto, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>Yoga de manhã &bull; Pilates ao início da tarde</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t1" name="rating-t1" value="5">
                  <label for="r5-t1" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t1" name="rating-t1" value="4">
                  <label for="r4-t1" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t1" name="rating-t1" value="3">
                  <label for="r3-t1" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t1" name="rating-t1" value="2">
                  <label for="r2-t1" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t1" name="rating-t1" value="1">
                  <label for="r1-t1" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t1" class="review-label">Comentário</label>
                <textarea id="review-text-t1" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
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
          <p><?= $trainer->formation ?></p>
          <a href=<?= "trainers.php#$trainer->username" ?> class="btn-text"> Ver perfil </a>
        </article>
      <?php } ?>
    </div>
  </section>
<?php } ?>