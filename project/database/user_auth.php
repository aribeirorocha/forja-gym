<?php

declare(strict_types=1);

require_once('user.php');

class UserAuth
{
    static function register(PDO $db, string $username, string $name, string $email, string $password, string $membership): bool
    {
        
        $stmt = $db->prepare('SELECT id FROM user WHERE email = ? OR username = ? LIMIT 1');
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) return false;

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('INSERT INTO user (username, name, email, password, role, membership) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $name, $email, $hashed, 'member', $membership]);

        return true;
    }

    static function login(PDO $db, string $email, string $password): ?User
    {
        // 1. Adicionei 'created_at' e 'age' ao SELECT
        $stmt = $db->prepare('SELECT id, username, name, email, photo, password, role, created_at, age FROM user WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if (!$row) return null;

        // 1. Detetar se o que está na BD é um hash seguro ou texto limpo ('1234')
        $valid = false;
        if (strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2a$') === 0) {
            // Se for um hash válido, usamos a verificação criptográfica padrão
            $valid = password_verify($password, $row['password']);
        } else {
            // Se for texto limpo (visto do teu populate), comparamos diretamente
            $valid = ($password === $row['password']);
            
            // [Bónus de Boas Práticas]: Se estiver correto, encriptamos e atualizamos 
            // logo na BD para que a conta fique segura no próximo login!
            if ($valid) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare('UPDATE user SET password = ? WHERE id = ?');
                $updateStmt->execute([$newHash, $row['id']]);
                $row['password'] = $newHash;
            }
        }
        if (!$valid) return null;

        // 2. Adicionei os 2 argumentos que faltavam aqui (o Date e o Age)
        return new User(
            $row['id'],
            $row['username'],
            $row['name'],
            $row['email'],
            $row['photo'] ?? '',
            $row['role'],
            new DateTime($row['created_at']), // Converte a string da base de dados para DateTime
            $row['age']                       // Passa a idade (pode ser null)
        );
    }

    static function startSession(User $user): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_id']       = $user->id;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_name']     = $user->name;
        $_SESSION['user_role']     = $user->role;
        $_SESSION['user_photo']    = $user->photoPath; // Nova linha!
    }

    static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
    }

    static function currentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) return null;
        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['user_username'],
            'name'     => $_SESSION['user_name'],
            'role'     => $_SESSION['user_role'],
            'photo'    => $_SESSION['user_photo'] ?? '', // Nova linha!
        ];
    }

    static function requireLogin(): void
    {
        if (!self::currentUser()) {
            header('Location: login.php');
            exit;
        }
    }
}