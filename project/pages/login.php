<?php

require_once('../templates/common.php');
require_once('../templates/login.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

$error      = '';
$registered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        die('Invalid CSRF token');
    }

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!$email || !$password) {
        $error = 'Por favor preenche todos os campos.';
    } else {
        $db   = getDatabaseConnection();
        $user = UserAuth::login($db, $email, $password);

        if ($user) {
            UserAuth::startSession($user);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email ou palavra-passe incorretos.';
        }
    }
}

output_header();
output_login_form(false, $error, $registered);
output_footer();
?>