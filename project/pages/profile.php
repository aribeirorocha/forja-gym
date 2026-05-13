<?php
require_once('../templates/common.php');
require_once('../templates/profile.php');
require_once('../database/user_auth.php');

$user = UserAuth::currentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

output_header();
output_profile($user);
output_footer();