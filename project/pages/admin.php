<?php

require_once('../templates/common.php');
require_once('../templates/home.php');
require_once('../templates/admin.php');

require_once('../database/trainer.php');
require_once('../database/user.php');
require_once('../database/gym_class.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

$db   = getDatabaseConnection();
$user = UserAuth::currentUser();

if (!$user) {
    header('Location: index.php');
    exit;
}

$userFromDB = User::getUser($db, $user['id']);

if (!$userFromDB || $userFromDB->role !== 'admin') {
    header('Location: index.php');
    exit;
}

$_SESSION['user_role'] = $userFromDB->role;

// ── Alterar role ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'], $_POST['user_id'])) {
    $allowed_roles = ['member', 'trainer', 'nutritionist', 'admin'];
    $role = $_POST['role'];
    $id   = intval($_POST['user_id']);
    if (in_array($role, $allowed_roles)) {
        User::updateRole($db, $id, $role);
    }
}

// ── Ações com action + id ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // ── Eliminar utilizador ──────────────────────────────────────────────────
    if ($action === 'delete_user' && isset($_POST['id'])) {
        User::deleteUser($db, intval($_POST['id']));
        exit;
    }

    // ── Eliminar equipamento ─────────────────────────────────────────────────
    if ($action === 'delete_equipment' && isset($_POST['id'])) {
        deleteEquipment($db, intval($_POST['id']));
        exit;
    }

    // ── Gerir membership de utilizador ──────────────────────────────────────
    if ($action === 'update_user_membership' && isset($_POST['user_id'], $_POST['membership'])) {
        $allowed = ['basico', 'normal', 'vip'];
        $new_membership = $_POST['membership'];
        $target_id = intval($_POST['user_id']);
        if (in_array($new_membership, $allowed)) {
            $stmt = $db->prepare('UPDATE user SET membership = ? WHERE id = ? AND role = 'member'');
            $stmt->execute([$new_membership, $target_id]);
        }
        header('Location: admin.php?tab=users');
        exit;
    }

    // ── Adicionar equipamento ────────────────────────────────────────────────
    if ($action === 'add_equipment') {
        $eq_name     = trim($_POST['eq_name']     ?? '');
        $eq_location = trim($_POST['eq_location'] ?? '');
        $eq_video    = trim($_POST['eq_video']    ?? '');
        $eq_muscle   = intval($_POST['eq_muscle'] ?? 0);

        if ($eq_name && $eq_location) {
            // Upload da foto
            $photo_filename = null;
            if (isset($_FILES['eq_photo']) && $_FILES['eq_photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['eq_photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $photo_filename = 'equip_' . md5(time() . $_FILES['eq_photo']['name']) . '.' . $ext;
                    move_uploaded_file($_FILES['eq_photo']['tmp_name'], '../imgs/' . $photo_filename);
                }
            }

            // Inserir equipamento
            $stmt = $db->prepare('
                INSERT INTO equipment (name, location, video_url, photo, status)
                VALUES (?, ?, ?, ?, \'Disponível\')
            ');
            $stmt->execute([$eq_name, $eq_location, $eq_video ?: null, $photo_filename]);
            $eq_id = (int)$db->lastInsertId();

            // Inserir exercício associado ao grupo muscular (se escolhido)
            if ($eq_muscle > 0 && $eq_id > 0) {
                $stmt = $db->prepare('
                    INSERT INTO exercises (name, description, id_equipment, id_muscle_group)
                    VALUES (?, \'\', ?, ?)
                ');
                $stmt->execute([$eq_name, $eq_id, $eq_muscle]);
            }
        }

        header('Location: admin.php?tab=equipment');
        exit;
    }

    // ── Adicionar trainer ────────────────────────────────────────────────────
    if ($action === 'add_trainer') {
        $tr_name           = trim($_POST['tr_name']           ?? '');
        $tr_username       = trim($_POST['tr_username']       ?? '');
        $tr_email          = trim($_POST['tr_email']          ?? '');
        $tr_password       = $_POST['tr_password']            ?? '';
        $tr_sex            = $_POST['tr_sex']                 ?? 'masculino';
        $tr_specialization = trim($_POST['tr_specialization'] ?? '');
        $tr_formation      = trim($_POST['tr_formation']      ?? '');
        $tr_certification  = trim($_POST['tr_certification']  ?? '');
        $tr_location       = trim($_POST['tr_location']       ?? 'Porto, Portugal');

        if ($tr_name && $tr_username && $tr_email && strlen($tr_password) >= 6) {
            // Verifica se email/username já existe
            $chk = $db->prepare('SELECT id FROM user WHERE email = ? OR username = ? LIMIT 1');
            $chk->execute([$tr_email, $tr_username]);

            if (!$chk->fetch()) {
                $hashed = password_hash($tr_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('
                    INSERT INTO user (username, name, email, password, role, membership)
                    VALUES (?, ?, ?, ?, \'trainer\', \'basico\')
                ');
                $stmt->execute([$tr_username, $tr_name, $tr_email, $hashed]);
                $new_id = (int)$db->lastInsertId();

                // Criar trainer_profile
                $stmt = $db->prepare('
                    INSERT INTO trainer_profile (id_user, sex, specialization, formation, certification, location)
                    VALUES (?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([
                    $new_id,
                    in_array($tr_sex, ['masculino','feminino']) ? $tr_sex : 'masculino',
                    $tr_specialization ?: 'Geral',
                    $tr_formation      ?: '—',
                    $tr_certification  ?: '—',
                    $tr_location,
                ]);
            }
        }

        header('Location: admin.php?tab=trainers');
        exit;
    }
}

// ── Paginação e pesquisa de utilizadores ─────────────────────────────────────
$page     = max(1, intval($_GET['page'] ?? 1));
$username = trim($_GET['username'] ?? '');
$users    = User::getUsers($db, $username, 10, $page - 1);

// ── Todas as aulas com próxima sessão e inscrições ───────────────────────────
$stmt = $db->prepare('
    SELECT c.id, c.name, c.type, c.capacity, c.local, c.active,
           u.name as trainer_name,
           MIN(cs.session_datetime) as next_session,
           (SELECT COUNT(*) FROM enrollment e2
            JOIN class_session cs2 ON e2.id_session = cs2.id
            WHERE cs2.id_class = c.id
              AND cs2.session_datetime = MIN(cs.session_datetime)) as enrolled
    FROM class c
    LEFT JOIN user u  ON u.id = c.id_trainer
    LEFT JOIN class_session cs ON cs.id_class = c.id AND cs.status = \'Agendada\'
    GROUP BY c.id
    ORDER BY c.name ASC
');
$stmt->execute();
$all_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Todos os trainers ─────────────────────────────────────────────────────────
$stmt = $db->prepare('
    SELECT u.id, u.name, u.email, u.photo,
           tp.specialization, tp.location,
           (SELECT COUNT(*) FROM class c WHERE c.id_trainer = u.id) as class_count
    FROM user u
    JOIN trainer_profile tp ON tp.id_user = u.id
    ORDER BY u.name ASC
');
$stmt->execute();
$all_trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Todos os nutricionistas ───────────────────────────────────────────────────
$stmt = $db->prepare('
    SELECT u.id, u.name, u.email, u.photo,
           np.specialization, np.license_number
    FROM user u
    JOIN nutritionist_profile np ON np.id_user = u.id
    ORDER BY u.name ASC
');
$stmt->execute();
$all_nutritionists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Todos os pedidos PT ───────────────────────────────────────────────────────
$stmt = $db->prepare('
    SELECT pb.id, pb.booking_date, pb.start_time, pb.end_time, pb.notes, pb.status,
           um.name as member_name,
           ut.name as trainer_name
    FROM pt_booking pb
    JOIN user um ON um.id = pb.id_member
    JOIN user ut ON ut.id = pb.id_trainer
    ORDER BY pb.booking_date DESC, pb.start_time DESC
');
$stmt->execute();
$all_pt_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Todos os equipamentos (para o template via global) ────────────────────────
$GLOBALS['admin_equipments'] = getEquipments($db);

output_header();
output_admin_panel($users, $page, $username, $all_classes, $all_trainers, $all_nutritionists, $all_pt_bookings);
output_footer();