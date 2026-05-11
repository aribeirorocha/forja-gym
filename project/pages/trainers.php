<?php 

require_once('../templates/common.php');
require_once('../templates/trainers.php');

require_once('../database/trainer.php');
require_once('../database/connection.php');

$db = getDatabaseConnection();

$trainers = Trainer::getTrainers($db, 99);

output_header();
output_trainers_hero();
output_trainers($trainers);
output_footer();

?>