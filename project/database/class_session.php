<?php

declare(strict_types=1);


class ClassSession
{

    public int $id;
    public DateTime $datetime;
    public string $status;
    public int $enrollmentCount;

    static array $daysOfWeek = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    static array $monthsOfYear = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];


    public function __construct(int $id, DateTime $datetime, string $status, int $enrollmentCount)
    {
        $this->id = $id;
        $this->datetime = $datetime;
        $this->status = $status;
        $this->enrollmentCount = $enrollmentCount;
    }

    static function getNextSession(PDO $db, int $class_id): ?ClassSession
    {

        $stmt = $db->prepare('SELECT id, session_datetime, status FROM class_session 
                              WHERE id_class = ? AND status = \'Agendada\' ORDER BY session_datetime ASC LIMIT 1');

        $stmt->execute(array($class_id));

        if ($session = $stmt->fetch()) {
            $stmt = $db->prepare('SELECT COUNT(1) as count FROM class_session S
                                  JOIN enrollment E ON E.id_session = S.id ');
            $stmt->execute();
            if ($count = $stmt->fetch()) {
                return new ClassSession($session['id'], new DateTime($session['session_datetime']), $session['status'], $count['count']);
            }
        }

        return null;
    }

    public function getDayOfWeek(): string
    {
        return ClassSession::$daysOfWeek[intval($this->datetime->format('w'))];
    }

    public function getTime(): string
    {
        return $this->datetime->format('H:i');
    }

    public function getDateText(): string
    {
        return $this->datetime->format('j') . " de " . ClassSession::$monthsOfYear[intval($this->datetime->format('n')) - 1];
    }

    static function getSessionReviewsStats(PDO $db, int $sessionId): array
    {
        $stmt = $db->prepare('
            SELECT AVG(rate) as avg_rating, COUNT(id) as total_reviews
            FROM rating
            WHERE id_session = ?
        ');

        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'avg' => $result['avg_rating'] ? round((float)$result['avg_rating'], 1) : 0,
            'count' => (int)$result['total_reviews']
        ];
    }


    static function addSessionReview(PDO $db, int $memberId, int $sessionId, int $rate, string $comment): void
    {
        $stmt = $db->prepare('
            INSERT INTO rating (id_member, id_session, rate, comment)
            VALUES (?, ?, ?, ?)
            ON CONFLICT(id_member, id_session) WHERE id_session IS NOT NULL
            DO UPDATE SET rate = excluded.rate, comment = excluded.comment, created_at = CURRENT_TIMESTAMP
        ');
        $stmt->execute([$memberId, $sessionId, $rate, $comment]);
    }

    static function getSessionReviews(PDO $db, int $sessionId): array
    {
        $stmt = $db->prepare('
            SELECT r.rate, r.comment, r.created_at, u.name, u.photo
            FROM rating r
            JOIN user u ON r.id_member = u.id
            WHERE r.id_session = ?
            ORDER BY r.created_at DESC
        ');
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static function enrollInSession(PDO $db, int $memberId, int $sessionId) {
        $stmt = $db->prepare('
            INSERT INTO enrollment (id_member, id_session)
            VALUES (?, ?)
            ON CONFLICT(id_member, id_session) 
            DO UPDATE SET enrollment_date = CURRENT_TIMESTAMP
        ');
        $stmt->execute([$memberId, $sessionId]);
    }

    static function removeEnrollment(PDO $db, int $memberId, int $sessionId) {
        $stmt = $db->prepare('
            DELETE FROM enrollment WHERE id_member = ? AND id_session = ?
        ');
        $stmt->execute([$memberId, $sessionId]);
    }

    static function hasEnrollment(PDO $db, int $memberId, int $sessionId) : bool {
        $stmt = $db->prepare('
            SELECT 1 FROM enrollment WHERE id_member = ? AND id_session = ? LIMIT 1
        ');
        $stmt->execute([$memberId, $sessionId]);
        return $stmt->fetch() != null;
    }

    static function createSession(PDO $db, int $classId, string $date, string $time) {
        $stmt = $db->prepare('
            INSERT INTO class_session (id_class, session_datetime) VALUES (?,?)
        ');
        $stmt->execute([$classId, "{$date} {$time}"]);
    }
}
