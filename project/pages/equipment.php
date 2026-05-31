<?php
declare(strict_types=1);

require_once('../templates/common.php');
require_once('../templates/equipment.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


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