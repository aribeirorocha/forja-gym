<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/equipment.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

$db = getDatabaseConnection();
$user = UserAuth::currentUser();

$equipments = getEquipments($db);

$is_admin = $user && ($user['role'] ?? '') === 'admin';

output_header();

if ($user) {
    output_member_equipment($equipments, $is_admin);
} else {
    output_public_equipment($equipments, $is_admin);
}
?>

<?php
output_footer();
?>