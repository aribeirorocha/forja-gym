<?php require_once('../database/user.php') ?>

<?php function output_admin_panel(array $users, int $page, string $username)
{ ?>

    <section class="admin-dashboard">
        <header class="admin-header">
            <p class="section-tag">ADMIN</p>
            <h1>Painel de Administrador</h1>
            <p>Pesquisa utilizadores por username e altera permissoes diretamente na tabela.</p>
        </header>

        <section class="profile-card">
            <header class="admin-toolbar">
                <form action="admin.php" method="get" class="admin-search">
                    <fieldset>
                        <label for="admin-username-search">
                            Username
                            <input type="search" id="admin-username-search" name="username" placeholder="Procurar por username">
                        </label>
                        <button type="submit" class="btn-save">Pesquisar</button>
                    </fieldset>
                </form>

                <nav class="admin-pagination">
                    <button type="submit" form="admin-prev-page" class="btn-text" <?= $page == 1 ? 'disabled' : '' ?>>Anterior</button>
                    <span class="admin-page-indicator">Página <?= $page ?></span>
                    <button type="submit" form="admin-next-page" class="btn-text" <?= count($users) < 10 ? 'disabled' : '' ?>>Seguinte</button>
                </nav>
            </header>

            <form action="admin.php" method="get" id="admin-prev-page">
                <input type="hidden" name="page" value=<?= $page - 1 ?>>
                <?php if ($username !== '') { ?>
                    <input type="hidden" name="username" value=<?= htmlspecialchars($username, ENT_QUOTES) ?>>
                <?php } ?>
            </form>
            <form action="admin.php" method="get" id="admin-next-page">
                <input type="hidden" name="page" value=<?= $page + 1 ?>>
                <?php if ($username !== '') { ?>
                    <input type="hidden" name="username" value=<?= htmlspecialchars($username, ENT_QUOTES) ?>>
                <?php } ?>
            </form>

            <section class="admin-table-wrap">
                <h2 id="admin-users-title" class="card-title">Utilizadores</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Data de Registo</th>
                            <th>Username</th>
                            <th>Nome</th>
                            <th>Função</th>
                            <th>Email</th>
                            <th>Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) {
                            output_user_row($user->id, $user->createdAt->format('Y/m/d, H:i'), $user->username, $user->name, $user->role, $user->email);
                        } ?>
                    </tbody>
                </table>
            </section>
        </section>
    </section>

<?php } ?>

<?php function output_user_row(int $id, string $join_date, string $username, string $display_name, string $role, string $email)
{ ?>

    <tr>
        <td><?= htmlspecialchars($join_date) ?></td>
        <td class="admin-user">@<?= htmlspecialchars($username) ?></td>
        <td><?= htmlspecialchars($display_name) ?></td>
        <td>
            <select name="role" class="role-select" data-user-id=<?= $id ?>>
                <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>Membro</option>
                <option value="trainer" <?= $role === 'trainer' ? 'selected' : '' ?>>Trainer</option>
                <option value="nutritionist" <?= $role === 'nutritionist' ? 'selected' : '' ?>>Nutricionista</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </td>
        <td class="admin-email"><?= htmlspecialchars($email) ?></td>
        <td>
            <button
                type="button"
                class="delete-user-btn btn-delete"
                data-user-id="<?= $id ?>">
                X
            </button>
        </td>
    </tr>

<?php } ?>