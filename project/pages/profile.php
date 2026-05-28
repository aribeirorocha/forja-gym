<?php
require_once('../templates/common.php');
require_once('../templates/profile.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

$db          = getDatabaseConnection();
$success_msg = '';
$error_msg   = '';

$is_trainer = ($current_user['role'] ?? '') === 'trainer';

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

    } elseif ($action === 'update_trainer_profile' && $is_trainer) {
        $formation      = trim($_POST['formation']      ?? '');
        $certification  = trim($_POST['certification']  ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $location       = trim($_POST['location']       ?? '');

        $stmt = $db->prepare('
            INSERT INTO trainer_profile (id_user, formation, certification, specialization, location, sex)
            VALUES (?, ?, ?, ?, ?, \'\')
            ON CONFLICT(id_user) DO UPDATE SET
                formation      = excluded.formation,
                certification  = excluded.certification,
                specialization = excluded.specialization,
                location       = excluded.location
        ');
        $stmt->execute([$current_user['id'], $formation, $certification, $specialization, $location]);
        $success_msg = 'Perfil atualizado com sucesso.';

    } elseif ($action === 'update_membership' && !$is_trainer) {
        $membership = $_POST['membership'] ?? '';
        if (in_array($membership, ['basico', 'normal', 'vip'])) {
            $stmt = $db->prepare('UPDATE user SET membership = ? WHERE id = ?');
            $stmt->execute([$membership, $current_user['id']]);
            $success_msg = 'Plano atualizado com sucesso.';
        }

    } elseif ($action === 'log_weight' && !$is_trainer) {
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

    } elseif ($action === 'delete_weight' && !$is_trainer) {
        $weight_id = intval($_POST['weight_id'] ?? 0);
        if ($weight_id > 0) {
            $stmt = $db->prepare('DELETE FROM weight_log WHERE id = ? AND id_member = ?');
            $stmt->execute([$weight_id, $current_user['id']]);
            $success_msg = 'Registo eliminado.';
        }

    } elseif ($action === 'book_pt' && !$is_trainer) {
        $trainer_id   = intval($_POST['trainer_id'] ?? 0);
        $booking_date = trim($_POST['booking_date'] ?? '');
        $booking_time = trim($_POST['booking_time'] ?? '');
        $notes        = trim($_POST['notes'] ?? '');

        if ($trainer_id <= 0 || !$booking_date || !$booking_time) {
            $error_msg = 'Preenche todos os campos obrigatórios.';
        } elseif ($booking_date < date('Y-m-d', strtotime('+1 day'))) {
            $error_msg = 'Escolhe uma data a partir de amanhã.';
        } else {
            $end_time = date('H:i', strtotime($booking_time) + 3600);
            $stmt = $db->prepare('
                INSERT INTO pt_booking (id_member, id_trainer, booking_date, start_time, end_time, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, \'Pendente\')
            ');
            $stmt->execute([$current_user['id'], $trainer_id, $booking_date, $booking_time, $end_time, $notes ?: null]);
            $success_msg = 'Pedido enviado! Aguarda confirmação do trainer.';
        }

    } elseif ($action === 'update_pt_status' && $is_trainer) {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? '';
        if ($booking_id > 0 && in_array($new_status, ['Confirmado', 'Cancelado'])) {
            // Garante que só altera pedidos direcionados a este trainer
            $stmt = $db->prepare('UPDATE pt_booking SET status = ? WHERE id = ? AND id_trainer = ?');
            $stmt->execute([$new_status, $booking_id, $current_user['id']]);
            $success_msg = $new_status === 'Confirmado' ? 'Sessão confirmada.' : 'Sessão cancelada.';
        }

    } elseif ($action === 'cancel_pt' && !$is_trainer) {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        if ($booking_id > 0) {
            $stmt = $db->prepare('UPDATE pt_booking SET status = \'Cancelado\' WHERE id = ? AND id_member = ? AND status = \'Pendente\'');
            $stmt->execute([$booking_id, $current_user['id']]);
            $success_msg = 'Pedido cancelado.';
        }

    } elseif ($action === 'update_photo') {
        if (isset($_FILES['profile_photo'])) {
            $error_code = $_FILES['profile_photo']['error'];
            if ($error_code === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['profile_photo']['tmp_name'];
                $fileName      = $_FILES['profile_photo']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    if (move_uploaded_file($fileTmpPath, '../imgs/' . $newFileName)) {
                        $stmt = $db->prepare('UPDATE user SET photo = ? WHERE id = ?');
                        $stmt->execute([$newFileName, $current_user['id']]);
                        $_SESSION['user_photo'] = $newFileName;
                        $success_msg = 'Foto de perfil atualizada com sucesso.';
                    } else {
                        $error_msg = 'Erro ao mover a imagem. Verifica as permissões da pasta imgs.';
                    }
                } else {
                    $error_msg = 'Formato inválido. Usa JPG, PNG, GIF ou WEBP.';
                }
            } elseif ($error_code === UPLOAD_ERR_INI_SIZE || $error_code === UPLOAD_ERR_FORM_SIZE) {
                $error_msg = 'A imagem é muito pesada (máx. 2MB).';
            } elseif ($error_code === UPLOAD_ERR_NO_FILE) {
                $error_msg = 'Nenhuma imagem foi selecionada.';
            } else {
                $error_msg = 'Erro no upload. Código: ' . $error_code;
            }
        }
    }
}

// ── Carregar dados do utilizador ──────────────────────────────────────────────
$stmt = $db->prepare('SELECT id, username, name, email, photo, role, membership, created_at FROM user WHERE id = ? LIMIT 1');
$stmt->execute([$current_user['id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Juntar dados do trainer_profile se for trainer
$trainer_classes      = [];
$trainer_reviews      = [];
$trainer_review_stats = [];

if ($is_trainer) {
    $stmt = $db->prepare('SELECT formation, certification, specialization, location FROM trainer_profile WHERE id_user = ? LIMIT 1');
    $stmt->execute([$current_user['id']]);
    $trainer_extra = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($trainer_extra) {
        $user_data = array_merge($user_data, $trainer_extra);
    }

    // Aulas do trainer com próxima sessão
    $stmt = $db->prepare('
        SELECT c.name, c.type, c.duration_minutes, c.local, c.capacity, c.active,
               MIN(cs.session_datetime) as next_session
        FROM class c
        LEFT JOIN class_session cs ON cs.id_class = c.id AND cs.status = \'Agendada\'
        WHERE c.id_trainer = ?
        GROUP BY c.id
        ORDER BY c.name ASC
    ');
    $stmt->execute([$current_user['id']]);
    $trainer_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reviews e estatísticas
    require_once('../database/trainer.php');
    $trainer_review_stats = getTrainerReviewsStats($db, $current_user['id']);
    $trainer_reviews      = getTrainerReviews($db, $current_user['id']);
}

// Registos de peso (só para members)
$weight_log = [];
$pt_bookings = [];
$trainers_list = [];

if (!$is_trainer) {
    $stmt = $db->prepare('SELECT id, weight_kg, logged_at, notes FROM weight_log WHERE id_member = ? ORDER BY logged_at DESC LIMIT 30');
    $stmt->execute([$current_user['id']]);
    $weight_log = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pedidos PT do member
    $stmt = $db->prepare('
        SELECT pb.id, pb.booking_date, pb.start_time, pb.end_time, pb.notes, pb.status, pb.created_at,
               u.name as trainer_name, u.photo as trainer_photo
        FROM pt_booking pb
        JOIN user u ON u.id = pb.id_trainer
        WHERE pb.id_member = ?
        ORDER BY pb.booking_date DESC, pb.start_time DESC
    ');
    $stmt->execute([$current_user['id']]);
    $pt_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lista de trainers para o formulário de pedido
    $stmt = $db->prepare('SELECT u.id, u.name, u.photo, tp.specialization FROM user u JOIN trainer_profile tp ON tp.id_user = u.id ORDER BY u.name ASC');
    $stmt->execute();
    $trainers_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    // Pedidos PT recebidos pelo trainer
    $stmt = $db->prepare('
        SELECT pb.id, pb.booking_date, pb.start_time, pb.end_time, pb.notes, pb.status, pb.created_at,
               u.name as member_name, u.photo as member_photo
        FROM pt_booking pb
        JOIN user u ON u.id = pb.id_member
        WHERE pb.id_trainer = ?
        ORDER BY pb.status ASC, pb.booking_date ASC, pb.start_time ASC
    ');
    $stmt->execute([$current_user['id']]);
    $pt_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

output_header();
output_profile($user_data, $weight_log, $success_msg, $error_msg, $trainer_classes, $trainer_reviews, $trainer_review_stats, $pt_bookings, $trainers_list);
output_footer();
?>