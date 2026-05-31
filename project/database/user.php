<?php

declare(strict_types = 1);

class User {

    public int $id;
    public string $username;
    public string $name;
    public string $email;
    public string $photoPath;
    public string $role;
    public DateTime $createdAt;
    public ?int $age;
    public string $membership;

    public function __construct(int $id, string $username, string $name, string $email, string $photoPath, string $role, DateTime $createdAt, ?int $age, string $membership = 'basico') {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->photoPath = $photoPath;
        $this->role = $role;
        $this->createdAt = $createdAt;
        $this->age = $age;
        $this->membership = $membership;
    }

    static function getUsers(PDO $db, string $username, int $count, int $section = 0) : array
    {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, created_at, age, membership
                              FROM user U
                              WHERE username LIKE ? 
                              ORDER BY created_at DESC
                              LIMIT ? OFFSET ?');

        $stmt->execute(array('%' . ($username ?? '') . '%', $count, $count * $section));

        $users = array();

        while ($user = $stmt->fetch()) {
            $users[] = new User(
                $user['id'],
                $user['username'],
                $user['name'],
                $user['email'],
                "../imgs/{$user['photo']}",
                $user['role'],
                new DateTime($user['created_at']),
                $user['age'],
                $user['membership'] ?? 'basico',
            );
        }

        return $users;
    }

    static function getUser(PDO $db, int $id) : ?User {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, created_at, age, membership 
                              FROM user U       
                              WHERE U.id = ? LIMIT 1');

        $stmt->execute(array($id));

        if ($user = $stmt->fetch()) {
            return new User(
                $user['id'],
                $user['username'],
                $user['name'],
                $user['email'],
                "../imgs/{$user['photo']}",
                $user['role'],
                new DateTime($user['created_at']),
                $user['age'],
                $user['membership'] ?? 'basico',
            );
        }

        return null;
    }

    static function updateRole(PDO $db, int $id, string $role) {
        $stmt = $db->prepare('UPDATE user SET role = ? WHERE id = ?');
        $stmt->execute(array($role, $id));

        if ($role === 'trainer') {
            $stmt = $db->prepare('INSERT OR IGNORE INTO trainer_profile (id_user, sex, specialization, formation, certification, location) VALUES (?, "masculino", "Geral", "—", "—", "Local")');
            $stmt->execute([$id]);
        } elseif ($role === 'nutritionist') {
            $stmt = $db->prepare('INSERT OR IGNORE INTO nutritionist_profile (id_user, specialization, license_number) VALUES (?, "Nutrição Geral", "—")');
            $stmt->execute([$id]);
        }
    }

    static function deleteUser(PDO $db, int $id) {
        $stmt = $db->prepare('DELETE FROM user WHERE id = ? AND role NOT LIKE \'admin\'');

        $stmt->execute(array($id));
    }

}