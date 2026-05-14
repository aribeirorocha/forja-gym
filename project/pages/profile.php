<?php
require_once('../templates/common.php');
require_once('../templates/profile.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');


UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

$db = getDatabaseConnection();
$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_details') {
        $name     = trim($_POST['name']     ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');

        if (!$name || !$username || !$email) {
            $error_msg = 'Preenche todos os campos.';
        } else {
            $stmt = $db->prepare('UPDATE user SET name = ?, username = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $username, $email, $current_user['id']]);
            $_SESSION['user_name']     = $name;
            $_SESSION['user_username'] = $username;
            $success_msg = 'Dados atualizados com sucesso.';
        }

    } elseif ($action === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password     = $_POST['new_password']     ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $stmt = $db->prepare('SELECT password FROM user WHERE id = ? LIMIT 1');
        $stmt->execute([$current_user['id']]);
        $row = $stmt->fetch();

        $valid = password_verify($current_password, $row['password'])
               || $current_password === $row['password'];

        if (!$valid) {
            $error_msg = 'Password atual incorreta.';
        } elseif (strlen($new_password) < 6) {
            $error_msg = 'A nova password deve ter pelo menos 6 caracteres.';
        } elseif ($new_password !== $confirm_password) {
            $error_msg = 'As passwords não coincidem.';
        } else {
            $stmt = $db->prepare('UPDATE user SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $current_user['id']]);
            $success_msg = 'Password atualizada com sucesso.';
        }

    } elseif ($action === 'update_membership') {
        $membership = $_POST['membership'] ?? '';
        if (in_array($membership, ['basico', 'normal', 'vip'])) {
            $stmt = $db->prepare('UPDATE user SET membership = ? WHERE id = ?');
            $stmt->execute([$membership, $current_user['id']]);
            $success_msg = 'Plano atualizado com sucesso.';
        }
    }
}

$stmt = $db->prepare('SELECT id, username, name, email, photo, role, membership, created_at FROM user WHERE id = ? LIMIT 1');
$stmt->execute([$current_user['id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

output_header();
output_profile($user_data, $success_msg, $error_msg);
output_footer();
?>