<?php

require_once('../templates/common.php');
require_once('../templates/register.php');
require_once('../templates/membership.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

$error = '';
$step  = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        die('Invalid CSRF token');
    }

    $step = intval($_POST['step'] ?? 1);

    if ($step === 1) {
        // Validate step 1 fields
        $name     = trim($_POST['name']     ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        if (!$name || !$username || !$email || !$password) {
            $error = 'Por favor preenche todos os campos.';
            $step  = 1;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
            $step  = 1;
        } elseif (strlen($password) < 6) {
            $error = 'A palavra-passe deve ter pelo menos 6 caracteres.';
            $step  = 1;
        } else {
            // Move to step 2 — show membership plans
            $step = 2;
        }

    } elseif ($step === 2) {
        $name       = trim($_POST['name']       ?? '');
        $username   = trim($_POST['username']   ?? '');
        $email      = trim($_POST['email']      ?? '');
        $password   = $_POST['password']        ?? '';
        $membership = $_POST['membership']      ?? 'basico';

        $allowed = ['basico', 'normal', 'vip'];
        if (!in_array($membership, $allowed)) $membership = 'basico';

        $db = getDatabaseConnection();
        $ok = UserAuth::register($db, $username, $name, $email, $password, $membership);

        if ($ok) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = 'Este email ou username já existe. Tenta outro.';
            $step  = 1;
        }
    }
}

output_header();

if ($step === 2) {
    $formData = [
        'name'     => $_POST['name']     ?? '',
        'username' => $_POST['username'] ?? '',
        'email'    => $_POST['email']    ?? '',
        'password' => $_POST['password'] ?? '',
    ];
    output_membership_plans($formData, $error);
} else {
    output_register_form($error);
}

output_footer();
?>