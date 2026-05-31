<?php
require_once('../templates/common.php');
require_once('../templates/profile.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/trainer.php');
require_once('../database/nutrition.php');

UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

$db          = getDatabaseConnection();
$success_msg = '';
$error_msg   = '';

$is_trainer = ($current_user['role'] ?? '') === 'trainer';
$is_nutritionist = ($current_user['role'] ?? '') === 'nutritionist';

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

    } elseif ($action === 'update_nutritionist_profile' && $is_nutritionist) {
        $specialization = trim($_POST['specialization'] ?? '');
        $license_number = trim($_POST['license_number'] ?? '');

        $stmt = $db->prepare('
            INSERT INTO nutritionist_profile (id_user, specialization, license_number)
            VALUES (?, ?, ?)
            ON CONFLICT(id_user) DO UPDATE SET
                specialization = excluded.specialization,
                license_number = excluded.license_number
        ');
        $stmt->execute([$current_user['id'], $specialization, $license_number]);
        $success_msg = 'Perfil de Nutricionista atualizado com sucesso.';

    } elseif ($action === 'update_appointment_status' && $is_nutritionist) {
        $appointment_id = intval($_POST['appointment_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? '';
        if ($appointment_id > 0 && in_array($new_status, ['confirmada', 'cancelada'])) {
            $stmt = $db->prepare('UPDATE appointment SET status = ? WHERE id = ? AND id_nutritionist = ?');
            $stmt->execute([$new_status, $appointment_id, $current_user['id']]);
            $success_msg = $new_status === 'confirmada' ? 'Consulta confirmada.' : 'Consulta cancelada.';
        }

    } elseif ($action === 'update_membership' && !$is_trainer && !$is_nutritionist) {
        $membership = $_POST['membership'] ?? '';
        if (in_array($membership, ['basico', 'normal', 'vip'])) {
            $stmt = $db->prepare('UPDATE user SET membership = ? WHERE id = ?');
            $stmt->execute([$membership, $current_user['id']]);
            $_SESSION['user_membership'] = $membership;
            $success_msg = 'Plano atualizado com sucesso.';
        }

    } elseif ($action === 'log_weight' && !$is_trainer && !$is_nutritionist) {
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

    } elseif ($action === 'delete_weight' && !$is_trainer && !$is_nutritionist) {
        $weight_id = intval($_POST['weight_id'] ?? 0);
        if ($weight_id > 0) {
            $stmt = $db->prepare('DELETE FROM weight_log WHERE id = ? AND id_member = ?');
            $stmt->execute([$weight_id, $current_user['id']]);
            $success_msg = 'Registo eliminado.';
        }

    } elseif ($action === 'book_pt' && !$is_trainer && !$is_nutritionist) {
        $trainer_id   = intval($_POST['trainer_id'] ?? 0);
        $booking_date = trim($_POST['booking_date'] ?? '');
        $booking_time = trim($_POST['booking_time'] ?? '');
        $notes        = trim($_POST['notes'] ?? '');

        if ($trainer_id <= 0 || !$booking_date || !$booking_time) {
            $error_msg = 'Preenche todos os campos obrigatórios.';
        } elseif ($booking_date < date('Y-m-d', strtotime('+1 day'))) {
            $error_msg = 'Escolhe uma data a partir de amanhã.';
        } else {
            $membership = $current_user['membership'] ?? 'basico';
            $monthly_count = getMonthlyPTCount($db, $current_user['id']);
            
            if ($membership === 'basico') {
                $error_msg = 'O plano Básico não inclui sessões de PT. Faz upgrade no teu perfil!';
            } elseif ($membership === 'normal' && $monthly_count >= 1) {
                $error_msg = 'Atingiste o teu limite mensal (1 sessão) de PT. Faz upgrade para VIP!';
            } elseif ($membership === 'vip' && $monthly_count >= 4) {
                $error_msg = 'Atingiste o teu limite mensal (4 sessões) de PT.';
            } else {
                $end_time = date('H:i', strtotime($booking_time) + 3600);
                $stmt = $db->prepare('
                    INSERT INTO pt_booking (id_member, id_trainer, booking_date, start_time, end_time, notes, status)
                    VALUES (?, ?, ?, ?, ?, ?, \'Pendente\')
                ');
                $stmt->execute([$current_user['id'], $trainer_id, $booking_date, $booking_time, $end_time, $notes ?: null]);
                $success_msg = 'Pedido enviado! Aguarda confirmação do trainer.';
            }
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

    } elseif ($action === 'cancel_pt' && !$is_trainer && !$is_nutritionist) {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        if ($booking_id > 0) {
            $stmt = $db->prepare('UPDATE pt_booking SET status = \'Cancelado\' WHERE id = ? AND id_member = ? AND status = \'Pendente\'');
            $stmt->execute([$booking_id, $current_user['id']]);
            $success_msg = 'Pedido cancelado.';
        }

    } elseif ($action === 'book_nutrition' && !$is_trainer && !$is_nutritionist) {
        $nutritionist_id   = intval($_POST['nutritionist_id'] ?? 0);
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $appointment_time = trim($_POST['appointment_time'] ?? '');

        if ($nutritionist_id <= 0 || !$appointment_date || !$appointment_time) {
            $error_msg = 'Preenche todos os campos obrigatórios.';
        } elseif ($appointment_date < date('Y-m-d', strtotime('+1 day'))) {
            $error_msg = 'Escolhe uma data a partir de amanhã.';
        } else {
            $membership = $current_user['membership'] ?? 'basico';
            $monthly_count = getMonthlyNutritionCount($db, $current_user['id']);
            
            if ($membership === 'basico') {
                $error_msg = 'O plano Básico não inclui consultas de nutrição. Faz upgrade no teu perfil!';
            } elseif ($membership === 'normal' && $monthly_count >= 1) {
                $error_msg = 'Atingiste o limite mensal (1 consulta) para o plano Normal. Faz upgrade para VIP!';
            } else {
                $datetime = $appointment_date . ' ' . $appointment_time . ':00';
                $stmt = $db->prepare('
                    INSERT INTO appointment (id_member, id_nutritionist, appointment_date, status)
                    VALUES (?, ?, ?, \'pendente\')
                ');
                $stmt->execute([$current_user['id'], $nutritionist_id, $datetime]);
                $success_msg = 'Pedido de consulta enviado! Aguarda confirmação do nutricionista.';
            }
        }

    } elseif ($action === 'cancel_nutrition' && !$is_trainer && !$is_nutritionist) {
        $appointment_id = intval($_POST['appointment_id'] ?? 0);
        if ($appointment_id > 0) {
            $stmt = $db->prepare('UPDATE appointment SET status = \'cancelada\' WHERE id = ? AND id_member = ? AND status = \'pendente\'');
            $stmt->execute([$appointment_id, $current_user['id']]);
            $success_msg = 'Pedido cancelado.';
        }

    } elseif ($action === 'unenroll_class' && !$is_trainer && !$is_nutritionist) {
        $session_id = intval($_POST['session_id'] ?? 0);
        if ($session_id > 0) {
            $stmt = $db->prepare('DELETE FROM enrollment WHERE id_member = ? AND id_session = ?');
            $stmt->execute([$current_user['id'], $session_id]);
            $success_msg = 'Inscrição cancelada com sucesso.';
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

    // Aulas do trainer
    $stmt = $db->prepare('
        SELECT c.id, c.name, c.type, c.duration_minutes, c.local, c.capacity, c.active
        FROM class c
        WHERE c.id_trainer = ?
        ORDER BY c.name ASC
    ');
    $stmt->execute([$current_user['id']]);
    $trainer_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($trainer_classes as &$c_info) {
        $stmt = $db->prepare('
            SELECT id, session_datetime 
            FROM class_session 
            WHERE id_class = ? AND session_datetime > ?
            ORDER BY session_datetime ASC LIMIT 1
        ');
        $stmt->execute([$c_info['id'], date('Y-m-d H:i:s')]);
        $next_session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($next_session) {
            $c_info['next_session'] = $next_session['session_datetime'];
            $c_info['next_session_id'] = $next_session['id'];
            
            $stmt = $db->prepare('
                SELECT u.id, u.name, u.photo 
                FROM enrollment e
                JOIN user u ON e.id_member = u.id
                WHERE e.id_session = ?
            ');
            $stmt->execute([$next_session['id']]);
            $c_info['enrolled_members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $c_info['next_session'] = null;
            $c_info['enrolled_members'] = [];
        }
    }
    unset($c_info);

    // Reviews e estatísticas
    require_once('../database/trainer.php');
    $trainer_review_stats = getTrainerReviewsStats($db, $current_user['id']);
    $trainer_reviews      = getTrainerReviews($db, $current_user['id']);
}

if ($is_nutritionist) {
    $stmt = $db->prepare('SELECT specialization, license_number FROM nutritionist_profile WHERE id_user = ? LIMIT 1');
    $stmt->execute([$current_user['id']]);
    $nutri_extra = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($nutri_extra) {
        $user_data = array_merge($user_data, $nutri_extra);
    }
}

// Registos de peso (só para members)
$weight_log = [];
$pt_bookings = [];
$trainers_list = [];
$nutritionists_list = [];
$my_appointments = [];
$nutri_appointments = [];
$my_nutrition_plan = null;
$my_meals = [];
$my_classes = [];

if (!$is_trainer && !$is_nutritionist) {
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

    // Lista de nutricionistas para o formulário de pedido
    $stmt = $db->prepare('SELECT u.id, u.name, u.photo, np.specialization FROM user u JOIN nutritionist_profile np ON np.id_user = u.id ORDER BY u.name ASC');
    $stmt->execute();
    $nutritionists_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pedidos de consulta do member
    $stmt = $db->prepare('
        SELECT a.id, a.appointment_date, a.status,
               u.name as nutritionist_name, u.photo as nutritionist_photo
        FROM appointment a
        JOIN user u ON u.id = a.id_nutritionist
        WHERE a.id_member = ?
        ORDER BY a.appointment_date DESC
    ');
    $stmt->execute([$current_user['id']]);
    $my_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Plano de Nutrição Ativo
    $stmt = $db->prepare('
        SELECT p.*, u.name as nutritionist_name 
        FROM nutrition_plan p
        JOIN user u ON p.id_nutritionist = u.id
        WHERE p.id_member = ? AND p.status = "Ativo"
        ORDER BY p.id DESC LIMIT 1
    ');
    $stmt->execute([$current_user['id']]);
    $my_nutrition_plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($my_nutrition_plan) {
        $stmt = $db->prepare('SELECT * FROM meal WHERE id_plan = ? ORDER BY day_of_week, meal_type');
        $stmt->execute([$my_nutrition_plan['id']]);
        $my_meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aulas inscritas do member
    $stmt = $db->prepare('
        SELECT c.id, cs.id as session_id, c.name, c.type, cs.session_datetime, u.name as trainer_name
        FROM enrollment e
        JOIN class_session cs ON e.id_session = cs.id
        JOIN class c ON cs.id_class = c.id
        JOIN user u ON c.id_trainer = u.id
        WHERE e.id_member = ? AND cs.session_datetime > ?
        ORDER BY cs.session_datetime ASC
    ');
    $stmt->execute([$current_user['id'], date('Y-m-d H:i:s')]);
    $my_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else if ($is_trainer) {
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
} else if ($is_nutritionist) {
    // Pedidos de consulta recebidos pelo nutricionista
    $stmt = $db->prepare('
        SELECT a.id, a.appointment_date, a.status,
               u.name as member_name, u.photo as member_photo
        FROM appointment a
        JOIN user u ON u.id = a.id_member
        WHERE a.id_nutritionist = ?
        ORDER BY a.status ASC, a.appointment_date ASC
    ');
    $stmt->execute([$current_user['id']]);
    $nutri_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

output_header();
require_once('../templates/profile.php');
output_profile($user_data, $weight_log, $success_msg, $error_msg, $trainer_classes, $trainer_reviews, $trainer_review_stats, $pt_bookings, $trainers_list, $nutri_appointments, $my_nutrition_plan, $my_meals, $nutritionists_list, $my_appointments, $my_classes);
output_footer();
?>