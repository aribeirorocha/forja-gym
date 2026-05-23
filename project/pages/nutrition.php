<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/nutrition.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/nutrition.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$nutritionists = getNutritionists($db);
$plan = $user ? getNutritionPlan($db, $user['id']) : null;

output_header();

if ($user) {
    output_member_nutrition_view($plan, $nutritionists);
} else {
    output_public_nutritionists($nutritionists);
}

output_footer();
?>