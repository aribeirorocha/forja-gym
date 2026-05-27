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

if (!$user || $user['role'] !== 'trainer') {
    header('Location: index.php');
    exit;
}


/*if ($_SERVER['REQUEST_METHOD'] === 'POST' ... ) {

}*/

$classes = GymClass::getClassesFromTrainer($db, $user['id']);

output_header();
output_class_form();
output_class_session_forms($classes);
output_footer();
?>