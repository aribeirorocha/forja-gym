<?php

declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/classes.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$class = GymClass::getClass($db, $id);

if (!$class) {
    header('Location: classes.php');
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

        if ($class->session->enrollmentCount >= $class->capacity) {
            header("Location: class.php?id=$id");
            exit;
        }
        if ($hasEnrollment) {
            ClassSession::removeEnrollment($db, $user['id'], $class->session->id);
            header("Location: class.php?id=$id");
            exit;
        }

        ClassSession::enrollInSession($db, $user['id'], $class->session->id);
        header("Location: class.php?id=$id&success=enroll");
        exit;
    } else {

        header('Location: login.php');
        exit;
    }
}

output_header();

$reviewStats = ClassSession::getSessionReviewsStats($db, $class->session->id);
output_class_details($class, $reviewStats, $user != null, $hasEnrollment);

output_footer();
