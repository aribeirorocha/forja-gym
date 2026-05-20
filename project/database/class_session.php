<?php

declare(strict_types=1);


class ClassSession
{

    public int $id;
    public DateTime $datetime;
    public string $status;
    public int $enrolmentCount;

    static array $daysOfWeek = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];


    public function __construct(int $id, DateTime $datetime, string $status, int $enrolmentCount)
    {
        $this->id = $id;
        $this->datetime = $datetime;
        $this->status = $status;
        $this->enrolmentCount = $enrolmentCount;
    }

    static function getNextSession(PDO $db, int $class_id): ?ClassSession
    {

        $stmt = $db->prepare('SELECT id, session_datetime, status FROM class_session 
                              WHERE id_class = ? ORDER BY session_datetime ASC LIMIT 1');

        $stmt->execute(array($class_id));

        if ($session = $stmt->fetch()) {
            $stmt = $db->prepare('SELECT COUNT(1) as count FROM class_session S
                                  JOIN enrollment E ON E.id_session = S.id ');
            $stmt->execute();
            if($count = $stmt->fetch()) {
                return new ClassSession($session['id'], new DateTime($session['session_datetime']), $session['status'], $count['count']);
            }
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
