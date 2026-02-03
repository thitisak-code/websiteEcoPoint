<?php
session_start();
require_once '../includes/config.php'; // ปรับ path ตามโครงสร้างโปรเจค

header('Content-Type: application/json');

if (!isset($_GET['ac_id']) || !is_numeric($_GET['ac_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid activity ID']);
    exit();
}

$ac_id = intval($_GET['ac_id']);

$stmt = $conn->prepare("SELECT ac_id, title, image, point, ac_limit FROM activity WHERE ac_id = ?");
$stmt->bind_param("i", $ac_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'ac_id' => $row['ac_id'],
        'title' => $row['title'],
        'image' => $row['image'],
        'point' => $row['point'],
        'ac_limit' => $row['ac_limit']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Activity not found']);
}
?>