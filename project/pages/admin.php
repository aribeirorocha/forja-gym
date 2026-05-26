<?php

require_once('../templates/common.php');
require_once('../templates/home.php');
require_once('../templates/admin.php');

require_once('../database/trainer.php');
require_once('../database/user.php');
require_once('../database/gym_class.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');



$db = getDatabaseConnection();
$user = UserAuth::currentUser();

if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$username = isset($_GET['username']) ? $_GET['username'] : '';

$users = User::getUsers($db, $username, 10, $page - 1);

output_header();
output_admin_panel($users, $page, $username);
output_footer();
?>