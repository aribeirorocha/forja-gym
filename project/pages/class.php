<?php

declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/classes.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$class = GymClass::getClass($db, $id);

if (!$class) {
    header('Location: classes.php');
    exit;
}
if ($class->session === null) {
    output_header();
    echo "<p class='form-error'>Esta aula não tem nenhuma sessão agendada de momento.</p>";
    output_footer();
    exit;
}
$hasEnrollment = $user && ClassSession::hasEnrollment($db, $user['id'], $class->session->id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    if ($user) {
        $rate = intval($_POST['rating']);
        $comment = trim($_POST['comentario'] ?? '');

        if ($rate >= 1 && $rate <= 5) {

            ClassSession::addSessionReview($db, $user['id'], $class->session->id, $rate, $comment);

            header("Location: class.php?id=$id&success=review");
            exit;
        }
    } else {

        header('Location: login.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if ($user) {

                $isAjax = isset($_POST['ajax']);
        
        if ($class->session->enrollmentCount >= $class->capacity) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Aula lotada']); exit; }
            header("Location: class.php?id=$id"); exit;
        }
        if ($hasEnrollment) {
            ClassSession::removeEnrollment($db, $user['id'], $class->session->id);
            if ($isAjax) { echo json_encode(['success' => true, 'action' => 'unenroll', 'new_count' => $class->session->enrollmentCount - 1]); exit; }
            header("Location: class.php?id=$id"); exit;
        }

        $membership = $user['membership'] ?? 'basico';
        if ($membership !== 'vip') {
            $weekly_count = ClassSession::getWeeklyClassCount($db, $user['id']);
            $max_classes = ($membership === 'normal') ? 3 : 1;
            
            if ($weekly_count >= $max_classes) {
                if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Atingiste o limite de aulas semanais do teu plano.']); exit; }
                header("Location: class.php?id=$id&error=membership_limit"); exit;
            }
        }

        ClassSession::enrollInSession($db, $user['id'], $class->session->id);
        if ($isAjax) { echo json_encode(['success' => true, 'action' => 'enroll', 'new_count' => $class->session->enrollmentCount + 1]); exit; }
        header("Location: class.php?id=$id&success=enroll"); exit;
    } else {
        if (isset($_POST['ajax'])) { echo json_encode(['success' => false, 'message' => 'Não autenticado']); exit; }
        header('Location: login.php'); exit;
    }
}

output_header();

if (isset($_GET['error']) && $_GET['error'] === 'membership_limit') {
    echo "<div class='form-error' style='max-width: 800px; margin: 20px auto; padding: 15px; border-radius: 8px; text-align: center; background: #fff0f0; border: 1px solid #ffcdd2; color: #d32f2f;'>Atingiste o limite de aulas semanais do teu plano. Faz upgrade no teu Perfil para acederes a mais aulas!</div>";
}

$reviewStats = ClassSession::getClassReviewsStats($db, $class->id);


$reviewsList = ClassSession::getClassReviews($db, $class->id);


output_class_details($class, $reviewStats, $reviewsList, $user != null, $hasEnrollment);

output_footer();
