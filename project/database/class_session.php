<?php

declare(strict_types=1);


class ClassSession
{

    public int $id;
    public DateTime $datetime;
    public string $status;

    static array $daysOfWeek = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];


    public function __construct(int $id, DateTime $datetime, string $status)
    {
        $this->id = $id;
        $this->datetime = $datetime;
        $this->status = $status;
    }

    static function getNextSession(PDO $db, int $class_id): ?ClassSession
    {

        $stmt = $db->prepare('SELECT id, session_datetime, status FROM class_session 
                              WHERE id_class = ? ORDER BY session_datetime ASC LIMIT 1');

        $stmt->execute(array($class_id));

        if ($session = $stmt->fetch()) {
            return new ClassSession($session['id'], new DateTime($session['session_datetime']), $session['status']);
        }

        return null;
    }

    public function getDayOfWeek() : string {
        return ClassSession::$daysOfWeek[intval($this->datetime->format('w'))];
    }

    public function getTime() : string {
        return $this->datetime->format('H:i');
    }
}
