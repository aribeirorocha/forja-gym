<?php function output_membership_plans(array $formData = [], string $error = ''): void { ?>
    <section class="membership-section">
        <div class="membership-header">
            <p class="section-tag">PLANOS</p>
            <h1>Escolhe o teu plano</h1>
            <p class="membership-subtitle">Sem compromissos. Cancela quando quiseres.</p>
        </div>

        <?php if ($error): ?>
            <p class="form-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="register.php" class="register-form">
            
            <input type="hidden" name="username" value="<?= htmlspecialchars($formData['username'] ?? '') ?>">
            <input type="hidden" name="name"     value="<?= htmlspecialchars($formData['name']     ?? '') ?>">
            <input type="hidden" name="email"    value="<?= htmlspecialchars($formData['email']    ?? '') ?>">
            <input type="hidden" name="password" value="<?= htmlspecialchars($formData['password'] ?? '') ?>">
            <input type="hidden" name="step"     value="2">

            <div class="plans-grid">

               
                <label class="plan-card" for="plan-basico">
                    <input type="radio" name="membership" id="plan-basico" value="basico" checked>
                    <div class="plan-inner">
                        <div class="plan-top">
                            <span class="plan-badge">BÁSICO</span>
                            <div class="plan-price">
                                <span class="price-amount">19€</span>
                                <span class="price-period">/mês</span>
                            </div>
                        </div>
                        <ul class="plan-features">
                            <li>✓ Acesso ao ginásio</li>
                            <li>✓ 1 aula por semana</li>
                            <li>✓ Balneários incluídos</li>
                            <li class="disabled">✗ Sessões de PT</li>
                            <li class="disabled">✗ Acesso 24h</li>
                            <li class="disabled">✗ Área premium</li>
                        </ul>
                        <span class="plan-select-btn">Escolher</span>
                    </div>
                </label>

                
                <label class="plan-card plan-popular" for="plan-normal">
                    <input type="radio" name="membership" id="plan-normal" value="normal">
                    <div class="plan-inner">
                        <div class="plan-top">
                            <span class="plan-badge">NORMAL</span>
                            <span class="plan-tag-popular">Mais popular</span>
                            <div class="plan-price">
                                <span class="price-amount">35€</span>
                                <span class="price-period">/mês</span>
                            </div>
                        </div>
                        <ul class="plan-features">
                            <li>✓ Acesso ilimitado ao ginásio</li>
                            <li>✓ 3 aulas por semana</li>
                            <li>✓ Balneários incluídos</li>
                            <li>✓ 1 sessão de PT por mês</li>
                            <li class="disabled">✗ Acesso 24h</li>
                            <li class="disabled">✗ Área premium</li>
                        </ul>
                        <span class="plan-select-btn">Escolher</span>
                    </div>
                </label>

                
                <label class="plan-card plan-vip" for="plan-vip">
                    <input type="radio" name="membership" id="plan-vip" value="vip">
                    <div class="plan-inner">
                        <div class="plan-top">
                            <span class="plan-badge">VIP</span>
                            <div class="plan-price">
                                <span class="price-amount">59€</span>
                                <span class="price-period">/mês</span>
                            </div>
                        </div>
                        <ul class="plan-features">
                            <li>✓ Acesso ilimitado ao ginásio</li>
                            <li>✓ Aulas ilimitadas</li>
                            <li>✓ Balneários incluídos</li>
                            <li>✓ PT dedicado (4 sessões/mês)</li>
                            <li>✓ Acesso 24 horas</li>
                            <li>✓ Área premium exclusiva</li>
                        </ul>
                        <span class="plan-select-btn">Escolher</span>
                    </div>
                </label>

            </div>

            <div class="register-submit">
                <button type="submit" class="btn-register-submit">Criar conta</button>
            </div>
        </form>
    </section>
<?php } ?>