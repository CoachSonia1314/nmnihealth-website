<?php
session_start();

// 检查管理员权限
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => '權限不足']);
    exit();
}

// 获取POST数据
$bookingId = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';

// 验证数据
if (!$bookingId || !in_array($status, ['confirmed', 'cancelled'])) {
    echo json_encode(['success' => false, 'message' => '無效的數據']);
    exit();
}

// 连接数据库
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 更新预约状态
    $stmt = $pdo->prepare("UPDATE coach_bookings SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $bookingId]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => '狀態更新成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '更新失敗']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫錯誤']);
}
?>