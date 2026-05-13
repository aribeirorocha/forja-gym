<?php
require_once('../templates/common.php');
require_once('../templates/profile.php');
require_once('../database/connection.php');
require_once('../database/user_auth.php');

// 1. Obriga a ter login feito
UserAuth::requireLogin();
$current_user = UserAuth::currentUser();

$success_msg = '';
$error_msg = '';

// 2. Se o formulário for submetido (Botão Guardar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // A. Atualizar Dados Pessoais
    if ($action === 'update_details') {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        
        if ($name && $username) {
            // AQUI: Futuramente farás o UPDATE na base de dados
            // $db = getDatabaseConnection();
            // $stmt = $db->prepare('UPDATE user SET name = ?, username = ? WHERE id = ?');
            // $stmt->execute([$name, $username, $current_user['id']]);
            
            // Para já, atualizamos a sessão para ver o efeito imediato
            $_SESSION['user_name'] = $name;
            $_SESSION['user_username'] = $username;
            $current_user['name'] = $name;
            $current_user['username'] = $username;
            
            $success_msg = "Dados atualizados com sucesso!";
        } else {
            $error_msg = "O nome e username não podem estar vazios.";
        }
    }
    
    // B. Atualizar Password
    if ($action === 'update_password') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password === $confirm_password && strlen($new_password) > 5) {
            $success_msg = "Funcionalidade de password pronta a ligar à base de dados!";
        } else {
            $error_msg = "As passwords não coincidem ou são demasiado curtas.";
        }
    }
    
    // C. Atualizar Foto (Upload Simples)
    if ($action === 'update_photo' && isset($_FILES['profile_photo'])) {
        $success_msg = "Funcionalidade de upload de foto pronta a configurar!";
    }
}

// 3. Renderiza a página
output_header();
output_profile($current_user, $success_msg, $error_msg);
output_footer();
?>