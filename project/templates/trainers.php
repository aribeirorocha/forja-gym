<?php function output_trainers_hero() { ?>
    <section class="trainers-hero">
      <p class="section-tag">EQUIPA</p>
      <h1>Os nossos <span class="highlight">trainers</span></h1>
      <p class="trainers-hero-subtitle">Especialistas dedicados ao teu progresso, cada um com a sua área de excelência.</p>
    </section>
<?php } ?>


<?php function output_trainers() { ?>
<section class="trainers-list" id="trainers">
      <div class="trainer-grid">

        
        <article class="trainer-card" id="ana-silva">
          <input type="checkbox" id="toggle-t1" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer1.jpg" alt = "Trainer 1" class = "trainer-photo">
            <h3>Ana Silva</h3>
            <p class="trainer-specialty">Yoga &bull; Pilates</p>
            <label for="toggle-t1" class="btn-text btn-ver-perfil">
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
                <dd>Licenciatura em Ciências do Desporto – FADEUP</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>Yoga Alliance RYT-200, STOTT Pilates</dd>
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

        
        <article class="trainer-card" id="joao-costa">
          <input type="checkbox" id="toggle-t2" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer2.jpg"  alt = "Trainer 2" class = "trainer-photo">
            <h3>João Costa</h3>
            <p class="trainer-specialty">HIIT &bull; Força</p>
            <label for="toggle-t2" class="btn-text">
              <span class="label-ver">Ver perfil </span>
              <span class="label-fechar">Fechar perfil </span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>28 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2020</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd>Licenciatura em Educação Física – ISMAI</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>CrossFit Level 1, Kettlebell Instructor</dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Matosinhos, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>HIIT ao final do dia &bull; Treino de Força ao fim de semana</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t2" name="rating-t2" value="5">
                  <label for="r5-t2" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t2" name="rating-t2" value="4">
                  <label for="r4-t2" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t2" name="rating-t2" value="3">
                  <label for="r3-t2" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t2" name="rating-t2" value="2">
                  <label for="r2-t2" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t2" name="rating-t2" value="1">
                  <label for="r1-t2" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t2" class="review-label">Comentário</label>
                <textarea id="review-text-t2" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
          </div>
        </article>

        
        <article class="trainer-card" id="rui-barbosa">
          <input type="checkbox" id="toggle-t3" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer6.jpg" alt = "Trainer 6" class = "trainer-photo">
            <h3>Rui Barbosa</h3>
            <p class="trainer-specialty">Stretch &bull; Calistenia</p>
            <label for="toggle-t3" class="btn-text btn-ver-perfil">
              <span class="label-ver">Ver perfil</span>
              <span class="label-fechar">Fechar perfil</span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>35 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2016</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd>Mestrado em Treino Desportivo – FMH Lisboa</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>Functional Movement Screen, FRC Mobility Specialist</dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Gaia, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>Mobility de manhã &bull; Calistenia à tarde</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t3" name="rating-t3" value="5">
                  <label for="r5-t3" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t3" name="rating-t3" value="4">
                  <label for="r4-t3" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t3" name="rating-t3" value="3">
                  <label for="r3-t3" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t3" name="rating-t3" value="2">
                  <label for="r2-t3" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t3" name="rating-t3" value="1">
                  <label for="r1-t3" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t3" class="review-label">Comentário</label>
                <textarea id="review-text-t3" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
          </div>
        </article>

        
        <article class="trainer-card" id="nuno-mendes">
          <input type="checkbox" id="toggle-t4" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer4.jpg" alt = "Trainer 4" class = "trainer-photo">
            <h3>Nuno Mendes</h3>
            <p class="trainer-specialty">Spinning &bull; Sprint</p>
            <label for="toggle-t4" class="btn-text btn-ver-perfil">
              <span class="label-ver">Ver perfil</span>
              <span class="label-fechar">Fechar perfil </span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>30 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2019</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd>Licenciatura em Desporto e Lazer – ESDRM</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>Spinning® Certified Instructor, Athletics Coach</dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Braga, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>Spinning ao fim do dia &bull; Sprint ao fim de semana</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t4" name="rating-t4" value="5">
                  <label for="r5-t4" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t4" name="rating-t4" value="4">
                  <label for="r4-t4" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t4" name="rating-t4" value="3">
                  <label for="r3-t4" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t4" name="rating-t4" value="2">
                  <label for="r2-t4" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t4" name="rating-t4" value="1">
                  <label for="r1-t4" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t4" class="review-label">Comentário</label>
                <textarea id="review-text-t4" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
          </div>
        </article>

        
        <article class="trainer-card" id="mário-fernandes">
          <input type="checkbox" id="toggle-t5" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer3.jpg" alt = "Trainer 3" class = "trainer-photo">
            <h3>Mário Fernandes</h3>
            <p class="trainer-specialty">CrossFit &bull; Cardio</p>
            <label for="toggle-t5" class="btn-text btn-ver-perfil">
              <span class="label-ver">Ver perfil </span>
              <span class="label-fechar">Fechar perfil </span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>27 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2021</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd>Licenciatura em Ciências do Desporto – UTAD</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>CrossFit Level 2, Precision Nutrition L1</dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Porto, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>CrossFit de manhã &bull; Cardio ao início da tarde</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t5" name="rating-t5" value="5">
                  <label for="r5-t5" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t5" name="rating-t5" value="4">
                  <label for="r4-t5" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t5" name="rating-t5" value="3">
                  <label for="r3-t5" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t5" name="rating-t5" value="2">
                  <label for="r2-t5" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t5" name="rating-t5" value="1">
                  <label for="r1-t5" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t5" class="review-label">Comentário</label>
                <textarea id="review-text-t5" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
          </div>
        </article>

        
        <article class="trainer-card" id="rafael-leao">
          <input type="checkbox" id="toggle-t6" class="trainer-toggle">
          <div class="trainer-summary">
            <img src = "../imgs/trainer5.jpg" alt = "Trainer 5" class = "trainer-photo">
            <h3>Rafael Leão</h3>
            <p class="trainer-specialty">CrossFit &bull; Mobilidade</p>
            <label for="toggle-t6" class="btn-text btn-ver-perfil">
              <span class="label-ver">Ver perfil</span>
              <span class="label-fechar">Fechar perfil </span>
            </label>
          </div>
          <div class="trainer-profile">
            <hr class="profile-divider">
            <dl class="profile-info">
              <div class="profile-row">
                <dt>Idade</dt>
                <dd>26 anos</dd>
              </div>
              <div class="profile-row">
                <dt>Desde</dt>
                <dd>2022</dd>
              </div>
              <div class="profile-row">
                <dt>Formação</dt>
                <dd>Licenciatura em Educação Física – FADEUP</dd>
              </div>
              <div class="profile-row">
                <dt>Certificações</dt>
                <dd>CrossFit Level 1, Mobility WOD Coach</dd>
              </div>
              <div class="profile-row">
                <dt>Localização</dt>
                <dd>Porto, Portugal</dd>
              </div>
              <div class="profile-row">
                <dt>Aulas</dt>
                <dd>CrossFit ao fim do dia &bull; Mobilidade ao fim de semana</dd>
              </div>
            </dl>
            <hr class="profile-divider">
            <section class="review-section">
              <h4 class="review-title">Deixa a tua review</h4>
              <form class="review-form" method="post">
                <fieldset class="star-rating">
                  <legend class="sr-only">Classificação</legend>
                  <input type="radio" id="r5-t6" name="rating-t6" value="5">
                  <label for="r5-t6" title="5 estrelas">&#9733;</label>
                  <input type="radio" id="r4-t6" name="rating-t6" value="4">
                  <label for="r4-t6" title="4 estrelas">&#9733;</label>
                  <input type="radio" id="r3-t6" name="rating-t6" value="3">
                  <label for="r3-t6" title="3 estrelas">&#9733;</label>
                  <input type="radio" id="r2-t6" name="rating-t6" value="2">
                  <label for="r2-t6" title="2 estrelas">&#9733;</label>
                  <input type="radio" id="r1-t6" name="rating-t6" value="1">
                  <label for="r1-t6" title="1 estrela">&#9733;</label>
                </fieldset>
                <label for="review-text-t6" class="review-label">Comentário</label>
                <textarea id="review-text-t6" name="comentario" rows="3" placeholder="Partilha a tua experiência..."></textarea>
                <button type="submit" class="btn-review-submit">Enviar review</button>
              </form>
            </section>
          </div>
        </article>

      </div>
    </section>
<?php } ?>
<?php function output_featured_trainers() { ?>
            <section class = "trainers" >
                <header class = "section-header">
                    <p class = "section-tag">EQUIPA</p>
                    <h2>Os nossos trainers</h2>
                    <p>Especialistas dedicados ao teu progresso .</p>
                </header>

                <div class = "trainer-grid">
                    <article class = "trainer-card">
                        <img src = "../imgs/trainer1.jpg" alt = "Trainer 1" class = "trainer-photo">
                        <h3>Ana Silva</h3>
                        <p>Especialista em Yoga e Pilates</p>
                        <a href="trainers.html#ana-silva"class = "btn-text"> Ver perfil </a>
                    </article>

                    <article class = "trainer-card">
                        <img src = "../imgs/trainer2.jpg"  alt = "Trainer 2" class = "trainer-photo">
                        <h3>João Costa</h3>
                        <p>Especialista em CrossFit e Treino Funcional</p>
                        <a href="trainers.html#joao-costa" class = "btn-text"> Ver perfil </a>
                    </article>

                    <article class = "trainer-card">
                        <img src = "../imgs/trainer3.jpg" alt = "Trainer 3" class = "trainer-photo">
                        <h3>Mário Fernandes</h3>
                        <p>Especialista em Spinning e Cardio</p>
                        <a href="trainers.html#mário-fernandes"class = "btn-text"> Ver perfil</a>
                    </article>
                </div> 
            </section>
<?php } ?>

