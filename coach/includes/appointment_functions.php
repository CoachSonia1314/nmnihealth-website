<?php
// 設置正確的路徑
set_include_path('/var/www/www.nmnihealth.com/html/includes:' . get_include_path());

// 引入PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

// 連接資料庫
function connectDatabase() {
    $servername = "localhost";
    $username = "nmnihealth";
    $password = "health2025";
    $dbname = "nmnihealth";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("連接失敗: " . $e->getMessage());
    }
}

// 發送預約確認郵件
function sendAppointmentConfirmationEmail($appointment) {
    $mail = new PHPMailer(true);
    
    try {
        // 服務器設置
        $mail->isSMTP();
        $mail->Host       = 'localhost';
        $mail->SMTPAuth   = false;
        $mail->Port       = 25;
        
        // 發件人和收件人
        $mail->setFrom('noreply@nmnihealth.com', 'NmNiHealth 更年期健康保健');
        $mail->addAddress($appointment['email'], $appointment['user_name']);
        
        // 郵件內容
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = 'NmNiHealth 預約確認通知';
        $mail->Body    = '
        <html>
        <body>
            <h2>預約確認通知</h2>
            <p>親愛的 ' . htmlspecialchars($appointment['user_name']) . ' 您好：</p>
            <p>感謝您預約 NmNiHealth 的專家諮詢服務。您的預約已成功建立，詳細資訊如下：</p>
            
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;"><strong>預約編號：</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . $appointment['id'] . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;"><strong>預約專家：</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($appointment['expert_name']) . '（' . htmlspecialchars($appointment['specialty']) . '）</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;"><strong>預約日期：</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . $appointment['appointment_date'] . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;"><strong>預約時間：</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">' . substr($appointment['appointment_time'], 0, 5) . '</td>
                </tr>
            </table>
            
            <p>請確保您的聯絡資訊正確，我們將在24小時內與您確認預約。</p>
            <p>如有任何問題，請聯繫我們：<a href="mailto:info@nmnihealth.com">info@nmnihealth.com</a></p>
            
            <p>此致</p>
            <p>NmNiHealth 更年期健康保健團隊</p>
        </body>
        </html>
        ';
        $mail->AltBody = '親愛的 ' . $appointment['user_name'] . ' 您好：感謝您預約 NmNiHealth 的專家諮詢服務。您的預約已成功建立，預約編號：' . $appointment['id'] . '，預約專家：' . $appointment['expert_name'] . '，預約日期：' . $appointment['appointment_date'] . '，預約時間：' . substr($appointment['appointment_time'], 0, 5) . '。我們將在24小時內與您確認預約。';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("郵件發送失敗。郵件錯誤: {$mail->ErrorInfo}");
        return false;
    }
}

// 獲取所有專家
function getExperts() {
    $pdo = connectDatabase();
    $stmt = $pdo->query("SELECT * FROM experts WHERE id IN (SELECT DISTINCT expert_id FROM appointments) OR id NOT IN (SELECT DISTINCT expert_id FROM appointments)");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 獲取特定日期的已預約時段
function getBookedAppointments($date) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// 創建預約
function createAppointment($user_data, $appointment_data) {
    $pdo = connectDatabase();
    
    try {
        $pdo->beginTransaction();
        
        // 檢查用戶是否已存在
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$user_data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $user_id = $user['id'];
        } else {
            // 創建新用戶
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, age) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_data['name'], $user_data['email'], '', $user_data['phone'], $user_data['age']]);
            $user_id = $pdo->lastInsertId();
        }
        
        // 創建預約
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, expert_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, 
            $appointment_data['expert_id'], 
            $appointment_data['date'], 
            $appointment_data['time'], 
            $appointment_data['notes']
        ]);
        
        $appointment_id = $pdo->lastInsertId();
        
        $pdo->commit();
        return $appointment_id;
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

// 獲取預約詳情
function getAppointment($appointment_id) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("
        SELECT a.*, u.name as user_name, u.email, u.phone, e.name as expert_name, e.specialty
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN experts e ON a.expert_id = e.id
        WHERE a.id = ?
    ");
    $stmt->execute([$appointment_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>