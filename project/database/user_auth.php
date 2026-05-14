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
        $stmt = $db->prepare('SELECT id, username, name, email, photo,password, role FROM user WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if (!$row) return null;

       
        $valid = password_verify($password, $row['password']);
            

        if (!$valid) return null;

        return new User(
            $row['id'],
            $row['username'],
            $row['name'],
            $row['email'],
            $row['photo'] ?? '',
            $row['role']
        );
    }

    static function startSession(User $user): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_id']       = $user->id;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_name']     = $user->name;
        $_SESSION['user_role']     = $user->role;
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