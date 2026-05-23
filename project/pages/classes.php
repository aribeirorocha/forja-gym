<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/classes.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser(); // Retorna array se logado, null se não

// Buscar dados comuns
$classes = GymClass::getClasses($db, 6);

output_header();
output_classes($classes, $user != null);
output_footer();
?>