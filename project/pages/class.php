<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/classes.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não


output_header();
if ($user && $_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['id']) {
    $id = intval($_GET['id']);
    $class = GymClass::getClass($db, $id);
    if($class)
        output_class_details($class);
}
output_footer();
?>