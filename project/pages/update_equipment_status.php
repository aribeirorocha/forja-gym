<?php
declare(strict_types=1);

require_once('../database/connection.php');
require_once('../database/user_auth.php');
require_once('../database/equipment.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf'] ?? '';
    if (!UserAuth::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}


header('Content-Type: application/json');

$user = UserAuth::currentUser();

if (!$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faz login novamente.']);
    exit;
}

if ($user['role'] !== 'trainer' && $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sem permissões. Role: ' . $user['role']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

$id     = isset($_POST['id'])     ? intval($_POST['id'])   : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

$allowed = ['Disponível', 'Manutenção'];
if ($id <= 0 || !in_array($status, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos. ID: ' . $id . ' Status: ' . $status]);
    exit;
}

try {
    $db = getDatabaseConnection();
    updateEquipmentStatus($db, $id, $status);
    echo json_encode(['success' => true, 'new_status' => $status]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro na base de dados: ' . $e->getMessage()]);
}