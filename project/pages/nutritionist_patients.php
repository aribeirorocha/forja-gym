<?php
require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

if (($current_user['role'] ?? '') !== 'nutritionist') {
    header('Location: profile.php');
    exit;
}

$db = getDatabaseConnection();

// Fetch unique members who have had an appointment with this nutritionist
$stmt = $db->prepare('
    SELECT DISTINCT u.id, u.name, u.photo, u.email
    FROM user u
    JOIN appointment a ON u.id = a.id_member
    WHERE a.id_nutritionist = ? AND a.status = 'confirmada'
    ORDER BY u.name ASC
');
$stmt->execute([$current_user['id']]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once('../templates/nutritionist_patients.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


output_header();
output_nutritionist_patients($patients);
output_footer();
?>
