<?php
require_once('../templates/common.php');
output_header();
?>
<div class="contacts-page">
    <div class="content-container">
        <h1>Contactos</h1>
        <p class="subtitle">Estamos aqui para ajudar. Entre em contacto connosco para qualquer dúvida ou questão.</p>
        <div class="contacts-grid">
            <div class="contact-card">
                <span class="icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </span>
                <h3>Email</h3>
                <p>info@forja-gym.pt</p>
            </div>
            <div class="contact-card">
                <span class="icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </span>
                <h3>Telefone</h3>
                <p>+351 912 345 678</p>
            </div>
            <div class="contact-card">
                <span class="icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </span>
                <h3>Morada</h3>
                <p>Rua da Constituição 123<br>Porto, Portugal</p>
            </div>
        </div>
    </div>
</div>
<?php
output_footer();
?>
