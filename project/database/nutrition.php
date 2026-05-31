<?php
declare(strict_types=1);

/**
 * Busca todos os nutricionistas registados no sistema
 */
function getNutritionists(PDO $db): array {
    $stmt = $db->prepare('
        SELECT u.id, u.name, u.photo, n.specialization 
        FROM user u
        JOIN nutritionist_profile n ON u.id = n.id_user
        WHERE u.role = "nutritionist"
    ');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca o plano de nutrição de um membro específico
 */
function getNutritionPlan(PDO $db, int $member_id): ?array {
    $stmt = $db->prepare('
        SELECT p.*, u.name as nutritionist_name
        FROM nutrition_plan p
        JOIN user u ON p.id_nutritionist = u.id
        WHERE p.id_member = ?
        ORDER BY p.id DESC LIMIT 1
    ');
    $stmt->execute([$member_id]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    return $plan ?: null;
}

/**
 * Regista uma nova marcação de consulta
 */
function scheduleAppointment(PDO $db, int $member_id, int $nutritionist_id, string $date): bool {
    $stmt = $db->prepare('
        INSERT INTO appointment (id_member, id_nutritionist, appointment_date, status) 
        VALUES (?, ?, ?, "pendente")
    ');
    return $stmt->execute([$member_id, $nutritionist_id, $date]);
}

/**
 * Envia uma mensagem para a nutricionista
 */
function sendMessage(PDO $db, int $sender_id, int $receiver_id, string $content): bool {
    $stmt = $db->prepare('
        INSERT INTO message (id_sender, id_receiver, content) 
        VALUES (?, ?, ?)
    ');
    return $stmt->execute([$sender_id, $receiver_id, $content]);
}

/**
 * Busca o histórico de mensagens entre o membro e a nutricionista
 */
function getMessages(PDO $db, int $member_id, int $nutritionist_id): array {
    $stmt = $db->prepare('
        SELECT * FROM message 
        WHERE (id_sender = ? AND id_receiver = ?) OR (id_sender = ? AND id_receiver = ?)
        ORDER BY sent_at ASC
    ');
    $stmt->execute([$member_id, $nutritionist_id, $nutritionist_id, $member_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMonthlyNutritionCount(PDO $db, int $memberId): int {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM appointment
        WHERE id_member = ?
          AND strftime('%Y-%m', appointment_date) = strftime('%Y-%m', 'now')
          AND status != 'cancelada'
    ");
    $stmt->execute([$memberId]);
    return (int) $stmt->fetchColumn();
}
?>