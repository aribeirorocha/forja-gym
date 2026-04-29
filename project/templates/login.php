<?php function output_login_form(bool $register) { ?>
        <section>
            <p class="logo">FORJA</p>
            <p>Porto &middot; Ginásio</p>
            <input type="radio" name="switch-login" id="radio-login" <?=$register ? '' : 'checked'?>> 
            <label for="radio-login">Login</label>
            <input type="radio" name="switch-login" id="radio-register" <?=$register ? 'checked' : ''?>>
            <label for="radio-register">Registrar</label>
            <div id="login-tab">
                <form name="login">
                    <label>
                        Email <input type="text" name="email" placeholder="O teu email...">
                    </label>
                    <label>
                        Palavra-passe <input type="password" name="password" placeholder="A tua palavra-passe...">
                    </label>
                </form>
                <p>
                    <label>
                        <input type="checkbox"> Lembrar-se
                    </label>
                    <a href="#">Esqueceste-te?</a>
                    
                </p>
                <button formtarget="login" formmethod="post">Entrar</button>
                <p><span>ou</span></p>
                <p>Não tens conta? <label class= "links" for="radio-register">Regista-te</label></p>
            </div>
            <div id="register-tab">
                <form name="register">
                    <label>
                        Nome <input type="text" name="name" placeholder="O teu nome...">
                    </label>
                    <label>
                        Email <input type="text" name="email" placeholder="O teu email...">
                    </label>
                    <label>
                        Palavra-passe <input type="password" name="password" placeholder="A tua palavra-passe...">
                    </label>
                </form>
                <button formtarget="register" formmethod="post">Criar conta</button>
                <p><span>ou</span></p>
                <p>Já tens conta? <label class= "links" for="radio-login">Entrar</label></p>
            </div>
        </section>
<?php } ?>