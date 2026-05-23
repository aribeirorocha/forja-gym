<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/trainer.php');
require_once('../database/user_auth.php'); 

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ==========================================
// PROCESSAR A SUBMISSÃO DA REVIEW AQUI
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    if ($user) {
        $rate = intval($_POST['rating']);
        $comment = trim($_POST['comentario'] ?? '');
        
        if ($rate >= 1 && $rate <= 5) {
            addTrainerReview($db, $user['id'], $id, $rate, $comment);
            header("Location: trainer_profile.php?id=$id&success=review");
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

// ==========================================
// BUSCAR OS DADOS PARA MOSTRAR
// ==========================================
$stmt = $db->prepare('
    SELECT u.name, u.photo, u.age, u.created_at, 
           tp.specialization, tp.formation, tp.certification, tp.location
    FROM user u
    JOIN trainer_profile tp ON u.id = tp.id_user
    WHERE u.id = ?
');
$stmt->execute([$id]);
$trainerInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trainerInfo) {
    header('Location: trainers.php');
    exit;
}

// Vai buscar as Estatísticas (Média/Total) e a Lista de Comentários
$reviewStats = getTrainerReviewsStats($db, $id);
$reviewsList = getTrainerReviews($db, $id); // NOVA FUNÇÃO

$photo_src = !empty($trainerInfo['photo']) ? "../imgs/" . htmlspecialchars($trainerInfo['photo']) : "../imgs/default-avatar.png";
$ano_entrada = date('Y', strtotime($trainerInfo['created_at']));

output_header();
?>

<link rel="stylesheet" href="../css/trainer_profile.css">

<main class="profile-page-wrapper">
    <article class="profile-login-card">
        
        <div class="profile-rating-badge">
            <span class="star-icon">&#9733;</span> 
            <?= number_format($reviewStats['avg'], 1) ?> 
            <span class="review-count">(<?= $reviewStats['count'] ?>)</span>
        </div>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'review'): ?>
            <div class="success-banner">
                Obrigado! A tua review foi registada com sucesso.
            </div>
        <?php endif; ?>

        <img src="<?= $photo_src ?>" alt="<?= htmlspecialchars($trainerInfo['name']) ?>" class="profile-big-photo" onerror="this.src='../imgs/default-avatar.png'">
        
        <h1 class="profile-name"><?= htmlspecialchars($trainerInfo['name']) ?></h1>
        <p class="profile-specialty"><?= str_replace(',', ' &bull; ', htmlspecialchars($trainerInfo['specialization'])) ?></p>

        <div class="profile-details-list">
            <div class="profile-detail-item">
                <strong>Idade</strong>
                <span><?= htmlspecialchars((string)$trainerInfo['age']) ?> anos</span>
            </div>
            <div class="profile-detail-item">
                <strong>Desde</strong>
                <span><?= $ano_entrada ?></span>
            </div>
            <div class="profile-detail-item">
                <strong>Formação Académica</strong>
                <span><?= htmlspecialchars($trainerInfo['formation']) ?></span>
            </div>
            <div class="profile-detail-item">
                <strong>Certificações</strong>
                <span><?= htmlspecialchars($trainerInfo['certification']) ?></span>
            </div>
            <div class="profile-detail-item">
                <strong>Localização</strong>
                <span><?= htmlspecialchars($trainerInfo['location']) ?></span>
            </div>
        </div>

        <?php if ($user): ?>
            <section class="review-section">
                <h4 class="review-box-title">Deixa a tua review</h4>
                <form class="review-form" method="post" action="trainer_profile.php?id=<?= $id ?>">
                    
                    <fieldset class="star-rating">
                        <input type="radio" id="r5" name="rating" value="5">
                        <label for="r5" title="5 estrelas">&#9733;</label>
                        <input type="radio" id="r4" name="rating" value="4">
                        <label for="r4" title="4 estrelas">&#9733;</label>
                        <input type="radio" id="r3" name="rating" value="3">
                        <label for="r3" title="3 estrelas">&#9733;</label>
                        <input type="radio" id="r2" name="rating" value="2">
                        <label for="r2" title="2 estrelas">&#9733;</label>
                        <input type="radio" id="r1" name="rating" value="1" required>
                        <label for="r1" title="1 estrela">&#9733;</label>
                    </fieldset>
                    
                    <textarea name="comentario" rows="3" placeholder="Partilha a tua experiência..." required></textarea>
                    
                    <button type="submit" class="btn-review-submit">Enviar review</button>
                </form>
            </section>
        <?php else: ?>
            <p class="review-text" style="text-align: center; margin-bottom: 20px;">
                Faz <a href="login.php" style="color: var(--orange); text-decoration: underline;">login</a> para deixar uma review.
            </p>
        <?php endif; ?>

        <?php if (count($reviewsList) > 0): ?>
            <div class="reviews-container">
                <button class="btn-toggle-reviews" id="toggle-reviews-btn">Ver comentários (<?= count($reviewsList) ?>)</button>
                
                <div class="reviews-list hidden" id="reviews-list-container">
                        <?php foreach ($reviewsList as $rev): 
                            // SE NÃO HOUVER FOTO: Gera um avatar automático com as iniciais do membro e cor laranja do teu ginásio!
                            if (!empty($rev['photo'])) {
                                $rev_photo = "../imgs/" . htmlspecialchars($rev['photo']);
                            } else {
                                $nome_codificado = urlencode($rev['name']);
                                $rev_photo = "https://ui-avatars.com/api/?name=" . $nome_codificado . "&background=ff6600&color=fff";
                            }
                                
                            // Formata a data: ex: "20/05/2026 às 17:45"
                            $rev_date = date('d/m/Y \à\s H:i', strtotime($rev['created_at']));
                        ?>
                       <div class="review-card">
                        <div class="review-header">
                            <div class="review-author">
                                <img src="<?= $rev_photo ?>" alt="Membro" class="review-author-photo">

                                    <div class="review-author-info">
                                        <span class="review-author-name"><?= htmlspecialchars($rev['name']) ?></span>
                                        <span class="review-date"><?= $rev_date ?></span>
                                    </div>
                                </div>
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?= $i <= $rev['rate'] ? 'star-filled' : '' ?>">&#9733;</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-text"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="trainers.php" class="btn-text btn-back-team">Voltar à Equipa</a>
        
    </article>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById('toggle-reviews-btn');
    const reviewsList = document.getElementById('reviews-list-container');
    
    if (toggleBtn && reviewsList) {
        toggleBtn.addEventListener('click', function() {
            reviewsList.classList.toggle('hidden');
            
            if (reviewsList.classList.contains('hidden')) {
                toggleBtn.textContent = 'Ver comentários (' + <?= count($reviewsList) ?> + ')';
            } else {
                toggleBtn.textContent = 'Ocultar comentários';
            }
        });
    }
});
</script>

<?php output_footer(); ?>