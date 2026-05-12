<?php function output_login_form(bool $register, string $error = '', bool $registered = false): void { ?>
    <section>
        <p class="logo">FORJA</p>
        <p>Porto &middot; Ginásio</p>

        <?php if ($registered): ?>
            <p class="form-success">Conta criada com sucesso! Faz login para entrar.</p>
        <?php endif; ?>

        <input type="radio" name="switch-login" id="radio-login" <?= $register ? '' : 'checked' ?>>
        <label for="radio-login">Login</label>
        <input type="radio" name="switch-login" id="radio-register" <?= $register ? 'checked' : '' ?>>
       

        <div id="login-tab">
            <?php if ($error && !$register): ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form name="login" method="post" action="login.php">
                <label>
                    Email <input type="email" name="email" placeholder="O teu email..." required>
                </label>
                <label>
                    Palavra-passe <input type="password" name="password" placeholder="A tua palavra-passe..." required>
                </label>
            </form>
            <p>
                <label><input type="checkbox"> Lembrar-se</label>
                <a href="#">Esqueceste-te?</a>
            </p>
            <button form="login" type="submit">Entrar</button>
            <p><span>ou</span></p>
            <p>Não tens conta? <a class="links" href="register.php">Regista-te</a></p>
        </div>

        <div id="register-tab">
            <p>Para criar conta <a class="links" href="register.php">clica aqui</a>.</p>
        </div>
    </section>
<?php } ?>