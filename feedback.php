<?php
// Processamento do formulário de feedback
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $avaliacao = htmlspecialchars($_POST['avaliacao']);
    $comentario = htmlspecialchars($_POST['comentario']);

    $mensagem = "Obrigado pelo seu feedback, $nome!";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forja Gym - Feedback</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        header {
            background-color: #000;
            color: #ff6600;
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 30px auto;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h2 {
            color: #ff6600;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 15px;
            background-color: #ff6600;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e65c00;
        }

        .mensagem {
            background: #dff0d8;
            color: #3c763d;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <header>
        <h1>Forja Gym</h1>
        <p>Feedback dos Clientes</p>
    </header>

    <div class="container">

        <?php if (!empty($mensagem)) : ?>
            <div class="mensagem">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Deixe o seu Feedback</h2>

            <form method="POST">

                <label>Nome</label>
                <input type="text" name="nome" placeholder="Seu nome" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="Seu email" required>

                <label>Avaliação</label>
                <select name="avaliacao">
                    <option>Excelente</option>
                    <option>Bom</option>
                    <option>Regular</option>
                    <option>Mau</option>
                </select>

                <label>Comentário</label>
                <textarea name="comentario" rows="5" placeholder="Escreva o seu feedback..."></textarea>

                <button type="submit">Enviar Feedback</button>

            </form>
        </div>

    </div>

    <footer>
        <p>© 2026 Forja Gym - Todos os direitos reservados</p>
    </footer>

</body>
</html>
