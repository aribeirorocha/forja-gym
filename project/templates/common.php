<?php include_once('../database/user_auth.php')?>

<?php function output_header() {
    $user = UserAuth::currentUser();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forja - Ginásio</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/homepage.css">
        <link rel="stylesheet" href="../css/responsive.css">
        <link rel="stylesheet" href="../css/forms.css">
        <link rel="stylesheet" href="../css/trainers.css">
        <link rel="stylesheet" href="../css/profile_styles.css">
        <link rel="stylesheet" href="../css/profile.css">
        <link rel="stylesheet" href="../css/nutrition.css">
        <link rel="stylesheet" href="../css/equipment.css">
        <link rel="stylesheet" href="../css/classes.css">
        <link rel="stylesheet" href="../css/trainer_profile.css">
        <?php if ($user['role'] === 'admin') { ?>
        <link rel="stylesheet" href="../css/admin.css">
        <script type="text/javascript" src="../js/admin.js" defer></script>
        <?php } ?>
        <script type="text/javascript" src="../js/script.js" defer></script>
    </head>
    <body>
        <header class="main-header">
            <div class="logo">
                <a href="index.php">FORJA</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="classes.php">Aulas</a></li>
                    <li><a href="trainers.php">Trainers</a></li>
                    <li><a href="equipment.php">Equipamentos</a></li>
                    <li><a href="nutrition.php">Nutrição</a></li>
                    <?php if ($user['role'] === 'admin') { ?>
                    <li><a href="admin.php">Admin</a></li>
                    <?php } ?>
                    <?php if ($user['role'] === 'trainer') { ?>
                    <li><a href="create_class.php">Criar</a></li>
                    <?php } ?>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($user): ?>
                    <a href="profile.php" class="profile-btn">
                        <?php 
                            
                            $initial = strtoupper(substr($user['name'], 0, 1));
                            $fallback = "data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><circle cx=%2220%22 cy=%2220%22 r=%2220%22 fill=%22%23f27a38%22/><text x=%2220%22 y=%2226%22 text-anchor=%22middle%22 font-size=%2218%22 fill=%22white%22 font-family=%22Arial%22>{$initial}</text></svg>";
                            
                            
                            $photo_src = (!empty($user['photo']) && strpos($user['photo'], 'data:image') === false) 
                                         ? "../imgs/" . htmlspecialchars($user['photo']) 
                                         : $fallback;
                        ?>
                        <img src="<?= $photo_src ?>" alt="Perfil" class="profile-avatar" onerror="this.src='<?= $fallback ?>'">
                        <span class="profile-username"><?= htmlspecialchars($user['username']) ?></span>
                    </a>
                    <a href="logout.php" class="btn-login">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Registrar</a>
                <?php endif; ?>
            </div>
        </header>
        <main>
<?php } ?>

<?php function output_footer() { ?>
        </main>
        <footer class="main-footer">
            <div class="footer-top">
                <div class="footer-logo">
                    <a href="index.php">FORJA</a>
                </div>
                <nav class="footer-nav">
                    <a href="#">Contactos</a>
                    <a href="#">Serviço</a>
                    <a href="#">Privacidade</a>
                </nav>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 FORJA Gym &middot; Porto, Portugal</p>
            </div>
        </footer>
    </body>
</html>
<?php } ?>