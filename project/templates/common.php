<?php function output_header() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $user = !empty($_SESSION['user_id']) ? [
        'name'     => $_SESSION['user_name'],
        'username' => $_SESSION['user_username'],
        'photo'    => $_SESSION['user_photo'] ?? '', 
    ] : null;
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
        



    </head>
    <body>
        <header class="main-header">
            <div class="logo">
                <a href="index.php">FORJA</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#aulas">Aulas</a></li>
                    <li><a href="trainers.php">Trainers</a></li>
                    <li><a href="equipment.php">Equipamentos</a></li>
                    <li><a href="nutrition.php">Nutrição</a></li>
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

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const links = document.querySelectorAll(".main-header nav ul li a");
                
                // Descobre a página atual e o hash (ex: index.php#aulas)
                let currentPage = window.location.pathname.split("/").pop();
                if (currentPage === "") currentPage = "index.php"; // Fallback
                let currentHash = window.location.hash;
                
                links.forEach(link => {
                    let linkHref = link.getAttribute("href");
                    
                    // Condição 1: O link é exatamente igual à página + hash (ex: index.php#aulas)
                    if (linkHref === (currentPage + currentHash)) {
                        link.classList.add("active");
                    } 
                    // Condição 2: Estamos numa página base sem hash (ex: index.php)
                    else if (!currentHash && linkHref === currentPage) {
                        link.classList.add("active");
                    }

                    // Muda o destaque instantaneamente ao clicar
                    link.addEventListener("click", function() {
                        links.forEach(l => l.classList.remove("active"));
                        this.classList.add("active");
                    });
                });
            });
        </script>
    </body>
</html>
<?php } ?>