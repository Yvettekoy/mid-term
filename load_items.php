<?php
// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'user_db');

// 檢查資料庫連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uploadId = $_GET['upload_id'];
$sql = "SELECT * FROM casual_items WHERE upload_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $uploadId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    // 查詢品項統計數量
    $itemId = $row['id'];
    $statSql = "SELECT SUM(quantity) AS total_quantity FROM casual_item_stats WHERE item_id = ?";
    $statStmt = $conn->prepare($statSql);
    $statStmt->bind_param('i', $itemId);
    $statStmt->execute();
    $statResult = $statStmt->get_result();
    $statRow = $statResult->fetch_assoc();

    $items[] = [
        'id' => $row['id'],
        'item_name' => $row['item_name'],
        'quantity' => $statRow['total_quantity'] ?? 0
    ];
}

echo json_encode(['items' => $items]);

$conn->close();
?>
