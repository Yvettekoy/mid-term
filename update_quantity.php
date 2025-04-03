<?php
// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'user_db');
$data = json_decode(file_get_contents('php://input'), true);

$itemId = $data['itemId'];
$quantity = $data['quantity'];
$userId = $_SESSION['id'];

$sql = "INSERT INTO casual_item_stats (item_id, user_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $itemId, $userId, $quantity, $quantity);
$stmt->execute();

echo json_encode(['success' => true]);

$conn
