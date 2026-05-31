<?php 

require_once('../templates/common.php');
require_once('../templates/home.php');
require_once('../templates/trainers.php');
require_once('../templates/classes.php');

require_once('../database/trainer.php');
require_once('../database/gym_class.php');
require_once('../database/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


$db = getDatabaseConnection();

$trainers = Trainer::getTrainers($db, 3);
$classes = GymClass::getClasses($db, 3);

output_header();
output_hero();
output_mission();
output_featured_classes($classes);
output_featured_trainers($trainers);
output_footer();

?>