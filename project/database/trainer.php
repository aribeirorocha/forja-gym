<?php

declare(strict_types=1);

require_once('user.php');

class Trainer extends User
{

    public string $formation;
    public string $certification;

    public function __construct(int $id, string $username, string $name, string $email, string $photoPath, string $role, string $formation, string $certification)
    {
        parent::__construct($id, $username, $name, $email, $photoPath, $role);
        $this->formation = $formation;
        $this->certification = $certification;
    }

    static function getTrainers(PDO $db, int $count) : array
    {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, formation, certification FROM user U
                              JOIN trainer_profile T ON T.id_user = U.id LIMIT ?');

        $stmt->execute(array($count));

        $trainers = array();

        while ($trainer = $stmt->fetch()) {
            $trainers[] = new Trainer(
                $trainer['id'],
                $trainer['username'],
                $trainer['name'],
                $trainer['email'],
                "../imgs/{$trainer['photo']}",
                $trainer['role'],
                $trainer['formation'],
                $trainer['certification']
            );
        }

        return $trainers;
    }

    static function getTrainer(PDO $db, int $id) : ?Trainer {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, formation, certification FROM user U
                              JOIN trainer_profile T ON T.id_user = U.id WHERE U.id = ? LIMIT 1');

        $stmt->execute(array($id));

        if ($trainer = $stmt->fetch()) {
            return new Trainer(
                $trainer['id'],
                $trainer['username'],
                $trainer['name'],
                $trainer['email'],
                "../imgs/{$trainer['photo']}",
                $trainer['role'],
                $trainer['formation'],
                $trainer['certification']
            );
        }

        return null;
    }
}
