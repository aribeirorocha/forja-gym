<?php
declare(strict_types=1);

function getEquipments(PDO $db): array {
    $stmt = $db->prepare('
        SELECT 
            e.*, 
            m.name as muscle_name 
        FROM equipment e
        LEFT JOIN exercises ex ON e.id = ex.id_equipment
        LEFT JOIN muscle_group m ON ex.id_muscle_group = m.id
        ORDER BY e.name ASC
    ');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteEquipment(PDO $db, int $id) {

    $stmt = $db->prepare('DELETE FROM equipment WHERE id = ?');

    $stmt->execute(array($id));

}
function updateEquipmentStatus(PDO $db, int $id, string $status): void {
    $stmt = $db->prepare('UPDATE equipment SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
}
 
?>