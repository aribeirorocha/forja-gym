<?php function output_register_form(string $error = ''): void { ?>
    <section>
        <p class="logo">FORJA</p>
        <p>Porto &middot; Ginásio</p>

        <input type="radio" name="switch-login" id="radio-login">

        <input type="radio" name="switch-login" id="radio-register" checked>
        <label for="radio-register">Registrar</label>

        <div id="register-tab">
            <?php if ($error): ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form name="register" id="register" method="post" action="register.php">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(UserAuth::generateCsrfToken()) ?>">
                <input type="hidden" name="step" value="1">
                <label>
                    Nome <input type="text" name="name" placeholder="O teu nome completo..." required>
                </label>
                <label>
                    Username <input type="text" name="username" placeholder="O teu username..." required>
                </label>
                <label>
                    Email <input type="email" name="email" placeholder="O teu email..." required>
                </label>
                <label>
                    Palavra-passe <input type="password" name="password" placeholder="A tua palavra-passe..." required minlength="8">
                </label>
            </form>

            <button form="register" type="submit">Continuar</button>
            <p><span>ou</span></p>
            <p>Já tens conta? <a class="links" href="login.php">Entrar</a></p>
        </div>
    </section>
<?php } ?>