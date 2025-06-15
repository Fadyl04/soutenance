<?php
header('Content-Type: application/json');
include 'database/db.php';

$album_id = $_GET['album_id'] ?? null;

if (!$album_id) {
    echo json_encode(['error' => 'ID d\'album non spécifié']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT image_path FROM album_images WHERE album_id = ?");
    $stmt->execute([$album_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($images);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>