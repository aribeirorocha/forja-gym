<?php function output_header() { ?>
<!DOCTYPE html>
<html lang = "pt">
    <head>
        <meta charset = "UTF-8">
        <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
        <title>Forja - Ginásio</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/homepage.css">
        <link rel="stylesheet" href="../css/responsive.css">
        <link rel="stylesheet" href="../css/forms.css">
        <link rel="stylesheet" href="../css/trainers.css">
    </head>
    <body>
    <body>
        <header class ="main-header">
            <div class = "logo" >
                <a href="homepage.html">FORJA</a>
            </div>
            <nav>
                <ul>
                    <li> <a href = "#aulas">Aulas</a></li>
                    <li> <a href = "trainers.php">Trainers</a></li>
                    <li> <a href = "#equipamentos">Equipamentos</a></li>
                </ul>
            </nav>
            <div class = "auth-buttons"> 
                <a href = "login.php" class = "btn-login">Login</a>
                <a href = "register.php" class = "btn-register">Registrar</a>
            </div>
        </header>
        <main>
<?php } ?>

<?php function output_footer() { ?>
        </main>
        <footer class= "main-footer">
            <div class = "footer-top">
                <div class = "footer-logo">
                    <a href="index.php">FORJA</a>
                </div>
                <nav class = "footer-nav">
                    <a href="#">Contactos</a>
                    <a href="#">Serviço</a>
                    <a href="#">Privacidade</a>
            </nav>
        </div>
        <div class = "footer-bottom">
            <p>&copy; 2026 FORJA Gym &middot; Porto, Portugal</p>
        </div>
        </footer>
    </body>
</html>
<?php } ?>