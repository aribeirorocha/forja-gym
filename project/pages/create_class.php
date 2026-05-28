<?php

declare(strict_types=1);

require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/gym_class.php');
require_once('../database/class_session.php');

// 1. Garantir que apenas utilizadores logados e do tipo 'trainer' acedem
UserAuth::requireLogin();
$user = UserAuth::currentUser();

if (!$user || $user['role'] !== 'trainer') {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // =========================================================================
    // AÇÃO: Criar Aula Completa com Sessão (Vinda do teu novo Modal do '+')
    // =========================================================================
    if ($action === 'create_class_with_session') {
        $name     = trim($_POST['name'] ?? '');
        $type     = trim($_POST['type'] ?? '');
        $duration = intval($_POST['duration_minutes'] ?? 0);
        $capacity = intval($_POST['capacity'] ?? 0);
        $local    = trim($_POST['local'] ?? '');
        $date     = $_POST['session_date'] ?? '';
        $time     = $_POST['session_time'] ?? '';

        if ($name && $type && $duration > 0 && $capacity > 0 && $local && $date && $time) {
            // Cria a modalidade base
            GymClass::createClass($db, $name, $type, $duration, (int)$user['id'], $capacity, $local);
            
            // Obtém o ID da aula que acabou de ser inserida e cria a sessão na agenda
            $classId = (int)$db->lastInsertId();
            ClassSession::createSession($db, $classId, $date, $time);
        }
        header('Location: classes.php');
        exit;

    // =========================================================================
    // AÇÃO: Alterar Data e Hora (Vinda do 'Alterar' da Data)
    // =========================================================================
    } elseif ($action === 'update_class_session') {
        $sessionId = intval($_POST['id_session'] ?? 0);
        $classId   = intval($_POST['id_class'] ?? 0);
        $date      = $_POST['session_date'] ?? '';
        $time      = $_POST['session_time'] ?? '';

        if ($sessionId > 0 && $classId > 0 && $date && $time) {
            // SEGURANÇA: Validar se a aula pertence mesmo à treinadora logada
            $class = GymClass::getClass($db, $classId);
            if ($class && (int)$class->trainerId === (int)$user['id']) {
                $stmt = $db->prepare('UPDATE class_session SET session_datetime = ? WHERE id = ?');
                $stmt->execute(["{$date} {$time}", $sessionId]);
            }
        }
        header("Location: class.php?id=" . $classId);
        exit;

    // =========================================================================
    // AÇÃO: Alterar Capacidade (Vinda do 'Alterar' da Capacidade)
    // =========================================================================
    } elseif ($action === 'update_class_capacity') {
        $classId  = intval($_POST['id_class'] ?? 0);
        $capacity = intval($_POST['capacity'] ?? 0);

        if ($classId > 0 && $capacity > 0) {
            $class = GymClass::getClass($db, $classId);
            if ($class && (int)$class->trainerId === (int)$user['id']) {
                $stmt = $db->prepare('UPDATE class SET capacity = ? WHERE id = ?');
                $stmt->execute([$capacity, $classId]);
            }
        }
        header("Location: class.php?id=" . $classId);
        exit;

    // =========================================================================
    // AÇÃO: Alterar Duração (Vinda do 'Alterar' da Duração)
    // =========================================================================
    } elseif ($action === 'update_class_duration') {
        $classId  = intval($_POST['id_class'] ?? 0);
        $duration = intval($_POST['duration_minutes'] ?? 0);

        if ($classId > 0 && $duration > 0) {
            $class = GymClass::getClass($db, $classId);
            if ($class && (int)$class->trainerId === (int)$user['id']) {
                $stmt = $db->prepare('UPDATE class SET duration_minutes = ? WHERE id = ?');
                $stmt->execute([$duration, $classId]);
            }
        }
        header("Location: class.php?id=" . $classId);
        exit;

    // =========================================================================
    // AÇÃO: Alterar Local (Vinda do 'Alterar' do Local)
    // =========================================================================
    } elseif ($action === 'update_class_local') {
        $classId = intval($_POST['id_class'] ?? 0);
        $local   = trim($_POST['local'] ?? '');

        if ($classId > 0 && $local !== '') {
            $class = GymClass::getClass($db, $classId);
            if ($class && (int)$class->trainerId === (int)$user['id']) {
                $stmt = $db->prepare('UPDATE class SET local = ? WHERE id = ?');
                $stmt->execute([$local, $classId]);
            }
        }
        header("Location: class.php?id=" . $classId);
        exit;

    // =========================================================================
    // AÇÃO: Remover Aula Assíncrona (Mantida para o funcionamento do teu lixo)
    // =========================================================================
    } elseif ($action === 'remove_class') {
        $classId = intval($_POST['id'] ?? 0);
        if ($classId > 0) {
            $class = GymClass::getClass($db, $classId);
            if ($class && (int)$class->trainerId === (int)$user['id']) {
                GymClass::removeClass($db, $classId);
            } else {
                http_response_code(403);
                exit;
            }
        }
        exit;
    }
}
$classes = GymClass::getClassesFromTrainer($db, $user['id']);

output_header();
output_class_form();
output_class_session_forms($classes);
output_footer();
?>