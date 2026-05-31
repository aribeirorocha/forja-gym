<?php

declare(strict_types=1);

require_once('user.php');

class Trainer extends User
{
    public string $sex;
    public string $specialization;
    public string $formation;
    public string $certification;
    public DateTime $trainerCreatedAt;
    public string $location;

    public function __construct(int $id, string $username, string $name, string $email, string $photoPath, string $role, DateTime $createdAt, ?int $age, string $sex, string $specialization, string $formation, string $certification, DateTime $trainerCreatedAt, string $location)
    {
        parent::__construct($id, $username, $name, $email, $photoPath, $role, $createdAt, $age);
        $this->sex = $sex;
        $this->specialization = $specialization;
        $this->formation = $formation;
        $this->certification = $certification;
        $this->trainerCreatedAt = $trainerCreatedAt;
        $this->location = $location;
    }

    static function getTrainers(PDO $db, int $count) : array
    {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, U.created_at as user_created_at, age, T.sex, specialization, formation, certification, T.created_at as trainer_created_at, location 
                              FROM user U
                              JOIN trainer_profile T ON T.id_user = U.id 
                              LIMIT ?');

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
                new DateTime($trainer['user_created_at']),
                $trainer['age'],
                $trainer['sex'],
                $trainer['specialization'],
                $trainer['formation'],
                $trainer['certification'],
                new DateTime($trainer['trainer_created_at']),
                $trainer['location']
            );
        }

        return $trainers;
    }

    static function getTrainer(PDO $db, int $id) : ?Trainer {
        $stmt = $db->prepare('SELECT id, username, name, email, photo, role, U.created_at, age, T.sex, specialization, formation, certification, T.created_at as trainer_created_at, location 
                              FROM user U
                              JOIN trainer_profile T ON T.id_user = U.id 
                              WHERE U.id = ? LIMIT 1');

        $stmt->execute(array($id));

        if ($trainer = $stmt->fetch()) {
            return new Trainer(
                $trainer['id'],
                $trainer['username'],
                $trainer['name'],
                $trainer['email'],
                "../imgs/{$trainer['photo']}",
                $trainer['role'],
                new DateTime($trainer['created_at']),
                $trainer['age'],
                $trainer['sex'],
                $trainer['specialization'],
                $trainer['formation'],
                $trainer['certification'],
                new DateTime($trainer['trainer_created_at']),
                $trainer['location']
            );
        }

        return null;
    }
} 

function getTrainerReviewsStats(PDO $db, int $trainerId): array {
    $stmt = $db->prepare('
        SELECT AVG(rate) as avg_rating, COUNT(id) as total_reviews
        FROM rating
        WHERE id_trainer = ?
    ');
    $stmt->execute([$trainerId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'avg' => $result['avg_rating'] ? round((float)$result['avg_rating'], 1) : 0,
        'count' => (int)$result['total_reviews']
    ];
}

function addTrainerReview(PDO $db, int $memberId, int $trainerId, int $rate, string $comment): void {
    $stmt = $db->prepare('
        INSERT INTO rating (id_member, id_trainer, rate, comment)
        VALUES (?, ?, ?, ?)
        ON CONFLICT(id_member, id_trainer) WHERE id_trainer IS NOT NULL
        DO UPDATE SET rate = excluded.rate, comment = excluded.comment, created_at = CURRENT_TIMESTAMP
    ');
    $stmt->execute([$memberId, $trainerId, $rate, $comment]);
}

function getTrainerReviews(PDO $db, int $trainerId): array {
    $stmt = $db->prepare('
        SELECT r.rate, r.comment, r.created_at, u.name, u.photo
        FROM rating r
        JOIN user u ON r.id_member = u.id
        WHERE r.id_trainer = ?
        ORDER BY r.created_at DESC
    ');
    $stmt->execute([$trainerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Vai buscar os horários de disponibilidade do trainer
 */
function getTrainerAvailability(PDO $db, int $trainerId): array {
    $stmt = $db->prepare('
        SELECT day_of_week, start_time, end_time
        FROM trainer_availability
        WHERE id_trainer = ?
        ORDER BY 
             CASE day_of_week
                  WHEN \'Segunda\' THEN 1
                  WHEN \'Terça\' THEN 2
                  WHEN \'Quarta\' THEN 3
                  WHEN \'Quinta\' THEN 4
                  WHEN \'Sexta\' THEN 5
                  WHEN \'Sábado\' THEN 6
                  WHEN \'Domingo\' THEN 7
             END
    ');
    $stmt->execute([$trainerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Pede uma marcação de PT (fica como "Pendente" por defeito)
 */
function bookPTSession(PDO $db, int $memberId, int $trainerId, string $date, string $startTime, string $notes): void {
    // Calculamos o fim da sessão para 1 hora depois da hora de início
    $endTime = date('H:i', strtotime($startTime) + 3600);

    $stmt = $db->prepare('
        INSERT INTO pt_booking (id_member, id_trainer, booking_date, start_time, end_time, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, \'Pendente\')
    ');
    $stmt->execute([$memberId, $trainerId, $date, $startTime, $endTime, $notes]);
}

function getMonthlyPTCount(PDO $db, int $memberId): int {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM pt_booking
        WHERE id_member = ?
          AND strftime('%Y-%m', booking_date) = strftime('%Y-%m', 'now')
          AND status != 'Cancelada'
    ");
    $stmt->execute([$memberId]);
    return (int) $stmt->fetchColumn();
}
?>

