<?php

require_once('../templates/common.php');
require_once('../templates/home.php');
require_once('../templates/classes.php');

require_once('../database/trainer.php');
require_once('../database/user.php');
require_once('../database/gym_class.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');



$db = getDatabaseConnection();
$user = UserAuth::currentUser();

if (!$user) {
    header('Location: index.php');
    exit;
}

$userFromDB = User::getUser($db, $user['id']);

if(!$userFromDB || $userFromDB->role !== 'trainer') {
    header('Location: index.php');
    exit;
}

$_SESSION['user_role'] = $userFromDB->role;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if($_POST['action'] === 'create_class') {
        GymClass::createClass($db, $_POST['name'], $_POST['type'], intval($_POST['duration_minutes']), $user['id'], intval($_POST['capacity']), $_POST['local']);
    }

    header('Location: classes.php');
    exit;
}

$classes = GymClass::getClassesFromTrainer($db, $user['id']);

output_header();
output_class_form();
output_class_session_forms($classes);
output_footer();
?>