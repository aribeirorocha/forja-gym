<?php

require_once('../templates/common.php');
require_once('../templates/trainers.php');
require_once('../database/trainer.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$trainers = Trainer::getTrainers($db, 99);

$is_admin = $user && ($user['role'] ?? '') === 'admin';

output_header();
output_trainers_hero();
output_trainers($trainers, $is_admin);
output_footer();
?>