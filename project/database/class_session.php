<?php

declare(strict_types=1); //para ser rigido com os tipos de dados, int tem de ser int, pratica de segurança para previnar bugs.


class ClassSession
{

    public int $id;
    public DateTime $datetime;
    public string $status; // agendada ou cancelada
    public int $enrollmentCount; // numero de inscritos

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
            $stmt = $db->prepare('SELECT COUNT(1) AS count FROM enrollment WHERE id_session = ?');
            $stmt->execute([$session['id']]);
            if ($count = $stmt->fetch()) {
                return new ClassSession((int)$session['id'], new DateTime($session['session_datetime']), $session['status'], (int)$count['count']);
            }
        }

        return null;
    }

    public function getDayOfWeek(): string
    {
        return ClassSession::$daysOfWeek[intval($this->datetime->format('N')) - 1];
    }

    public function getTime(): string
    {
        return $this->datetime->format('H:i');
    }

    public function getDateText(): string
    {
        return $this->datetime->format('j') . " de " . ClassSession::$monthsOfYear[intval($this->datetime->format('n')) - 1];
    }

    static function getClassReviewsStats(PDO $db, int $classId): array
    {
        $stmt = $db->prepare('
            SELECT AVG(rate) as avg_rating, COUNT(r.id) as total_reviews
            FROM rating r
            JOIN class_session cs ON r.id_session = cs.id
            WHERE cs.id_class = ?
        ');

        $stmt->execute([$classId]);
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

    static function getClassReviews(PDO $db, int $classId): array
    {
        $stmt = $db->prepare('
            SELECT r.rate, r.comment, r.created_at, u.name, u.photo
            FROM rating r
            JOIN user u ON r.id_member = u.id
            JOIN class_session cs ON r.id_session = cs.id
            WHERE cs.id_class = ?
            ORDER BY r.created_at DESC
        ');
        $stmt->execute([$classId]);
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

    static function getWeeklyClassCount(PDO $db, int $memberId) : int {
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM enrollment e
            JOIN class_session cs ON e.id_session = cs.id
            WHERE e.id_member = ? 
              AND strftime('%Y-%W', cs.session_datetime) = strftime('%Y-%W', 'now')
        ");
        $stmt->execute([$memberId]);
        return (int) $stmt->fetchColumn();
    }
}
