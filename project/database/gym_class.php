<?php

declare(strict_types = 1);

require_once('class_session.php');
require_once('trainer.php');

class GymClass {

    public int $id;
    public string $name;
    public string $type;
    public int $durationMinutes;
    public int $trainerId;
    public int $capacity;
    public string $local;
    public bool $active;

    public ?Trainer $trainer;
    public ?ClassSession $session;


    public function __construct(int $id, string $name, string $type, int $durationMinutes, int $trainerId, int $capacity, string $local, bool $active, ?ClassSession $session, ?Trainer $trainer = null) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->durationMinutes = $durationMinutes;
        $this->trainerId = $trainerId;
        $this->capacity = $capacity;
        $this->local = $local;
        $this->active = $active;
        $this->session = $session;
        $this->trainer = $trainer;
    }

    static function getClasses(PDO $db, int $count) : array
    {
        $stmt = $db->prepare('SELECT id, name, type, duration_minutes, id_trainer, capacity, local, active FROM class LIMIT ?');

        $stmt->execute(array($count));

        $classes = array();

        while ($class = $stmt->fetch()) {
            $classes[] = new GymClass(
                $class['id'],
                $class['name'],
                $class['type'],
                $class['duration_minutes'],
                $class['id_trainer'],
                $class['capacity'],
                $class['local'],
                $class['active'] == 1,
                ClassSession::getNextSession($db, $class['id']),
                Trainer::getTrainer($db, $class['id_trainer'])
            );
        }

        return $classes;
    }

    public function getPhotoPath() : string {
        return "../imgs/{$this->type}.jpg";
    }

}

?>