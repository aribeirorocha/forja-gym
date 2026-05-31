<?php
require_once('../templates/common.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

if (($current_user['role'] ?? '') !== 'nutritionist') {
    header('Location: profile.php');
    exit;
}

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
if ($member_id <= 0) {
    header('Location: nutritionist_patients.php');
    exit;
}

$db = getDatabaseConnection();
$success_msg = '';
$error_msg = '';

$stmt = $db->prepare('SELECT id, name, email, photo FROM user WHERE id = ?');
$stmt->execute([$member_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header('Location: nutritionist_patients.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_plan') {
        $goal = trim($_POST['goal'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $start_date = trim($_POST['start_date'] ?? date('Y-m-d'));
        $end_date = trim($_POST['end_date'] ?? '');
        if (empty($end_date)) $end_date = null;
        
        $plan_id = intval($_POST['plan_id'] ?? 0);
        
        if (empty($goal)) {
            $error_msg = 'O objetivo é obrigatório.';
        } else {
            if ($plan_id > 0) {
                $stmt = $db->prepare('UPDATE nutrition_plan SET goal = ?, notes = ?, start_date = ?, end_date = ? WHERE id = ? AND id_nutritionist = ?');
                $stmt->execute([$goal, $notes, $start_date, $end_date, $plan_id, $current_user['id']]);
                $success_msg = 'Plano atualizado com sucesso.';
            } else {
                $stmt = $db->prepare('INSERT INTO nutrition_plan (id_member, id_nutritionist, goal, notes, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, "Ativo")');
                $stmt->execute([$member_id, $current_user['id'], $goal, $notes, $start_date, $end_date]);
                $success_msg = 'Plano criado com sucesso.';
            }
        }
    } elseif ($action === 'add_meal') {
        $plan_id = intval($_POST['plan_id'] ?? 0);
        $day_of_week = $_POST['day_of_week'] ?? '';
        $meal_type = $_POST['meal_type'] ?? '';
        $description = trim($_POST['description'] ?? '');
        
        $calories = $_POST['calories'] !== '' ? intval($_POST['calories']) : null;
        $protein = $_POST['protein_g'] !== '' ? intval($_POST['protein_g']) : null;
        $carbs = $_POST['carbs_g'] !== '' ? intval($_POST['carbs_g']) : null;
        $fat = $_POST['fat_g'] !== '' ? intval($_POST['fat_g']) : null;
        
        if ($plan_id > 0 && $day_of_week && $meal_type && $description) {
            $stmt = $db->prepare('INSERT INTO meal (id_plan, day_of_week, meal_type, description, calories, protein_g, carbs_g, fat_g) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$plan_id, $day_of_week, $meal_type, $description, $calories, $protein, $carbs, $fat]);
            $success_msg = 'Refeição adicionada.';
        } else {
            $error_msg = 'Preenche os campos obrigatórios da refeição (Dia, Tipo e Descrição).';
        }
    } elseif ($action === 'delete_meal') {
        $meal_id = intval($_POST['meal_id'] ?? 0);
        $plan_id = intval($_POST['plan_id'] ?? 0);
        if ($meal_id > 0 && $plan_id > 0) {
            $stmt = $db->prepare('DELETE FROM meal WHERE id = ? AND id_plan = ?');
            $stmt->execute([$meal_id, $plan_id]);
            $success_msg = 'Refeição removida.';
        }
    }
}

// Obter o plano mais recente que seja Ativo
$stmt = $db->prepare('SELECT * FROM nutrition_plan WHERE id_member = ? AND id_nutritionist = ? ORDER BY id DESC LIMIT 1');
$stmt->execute([$member_id, $current_user['id']]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

$meals = [];
if ($plan) {
    $stmt = $db->prepare('SELECT * FROM meal WHERE id_plan = ? ORDER BY day_of_week, meal_type');
    $stmt->execute([$plan['id']]);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

require_once('../templates/edit_nutrition_plan.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


output_header();
output_edit_nutrition_plan($patient, $plan, $meals, $success_msg, $error_msg);
output_footer();
?>
