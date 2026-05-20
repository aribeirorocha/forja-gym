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

    } elseif ($action === 'log_weight') {
        $weight_kg = floatval($_POST['weight_kg'] ?? 0);
        $logged_at = $_POST['logged_at'] ?? date('Y-m-d');
        $notes     = trim($_POST['notes'] ?? '');

        if ($weight_kg <= 0 || $weight_kg > 400) {
            $error_msg = 'Peso inválido.';
        } else {
            $stmt = $db->prepare('
                INSERT INTO weight_log (id_member, weight_kg, logged_at, notes)
                VALUES (?, ?, ?, ?)
                ON CONFLICT(id_member, logged_at) DO UPDATE SET weight_kg = excluded.weight_kg, notes = excluded.notes
            ');
            $stmt->execute([$current_user['id'], $weight_kg, $logged_at, $notes ?: null]);
            $success_msg = 'Peso registado com sucesso.';
        }

    } elseif ($action === 'delete_weight') {
        $weight_id = intval($_POST['weight_id'] ?? 0);
        if ($weight_id > 0) {
            $stmt = $db->prepare('DELETE FROM weight_log WHERE id = ? AND id_member = ?');
            $stmt->execute([$weight_id, $current_user['id']]);
            $success_msg = 'Registo eliminado.';
        }
    }
    elseif ($action === 'update_photo') {
    if (isset($_FILES['profile_photo'])) {
        $error_code = $_FILES['profile_photo']['error'];
        
        if ($error_code === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName    = $_FILES['profile_photo']['name'];
            
            $fileParts = explode(".", $fileName);
            $fileExtension = strtolower(end($fileParts));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = '../imgs/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $stmt = $db->prepare('UPDATE user SET photo = ? WHERE id = ?');
                    $stmt->execute([$newFileName, $current_user['id']]);
                    $_SESSION['user_photo'] = $newFileName;
                    $success_msg = 'Foto de perfil atualizada com sucesso.';
                } else {
                    $error_msg = 'Erro ao mover a imagem. Faltam permissões de escrita na pasta imgs!';
                }
            } else {
                $error_msg = 'Formato inválido. Usa apenas JPG, PNG, GIF ou WEBP.';
            }
        } elseif ($error_code === UPLOAD_ERR_INI_SIZE || $error_code === UPLOAD_ERR_FORM_SIZE) {
            $error_msg = 'A imagem é muito pesada! O servidor PHP local só aceita imagens até 2MB.';
        } elseif ($error_code === UPLOAD_ERR_NO_FILE) {
            $error_msg = 'Nenhuma imagem foi selecionada.';
        } else {
            $error_msg = 'Ocorreu um erro no upload. Código: ' . $error_code;
        }
    } else {
        $error_msg = 'O formulário não enviou os dados do ficheiro.';
    }
}
}

$stmt = $db->prepare('SELECT id, username, name, email, photo, role, membership, created_at FROM user WHERE id = ? LIMIT 1');
$stmt->execute([$current_user['id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare('SELECT id, weight_kg, logged_at, notes FROM weight_log WHERE id_member = ? ORDER BY logged_at DESC LIMIT 30');
$stmt->execute([$current_user['id']]);
$weight_log = $stmt->fetchAll(PDO::FETCH_ASSOC);

output_header();
output_profile($user_data, $weight_log, $success_msg, $error_msg);
output_footer();
?>