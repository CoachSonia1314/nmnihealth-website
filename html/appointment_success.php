<?php
// 使用絕對路徑引入函數文件
require_once '/var/www/www.nmnihealth.com/html/includes/appointment_functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.html");
    exit();
}

$appointment_id = $_GET['id'];
$appointment = getAppointment($appointment_id);

if (!$appointment) {
    header("Location: index.html");
    exit();
}

// 發送預約確認郵件
$email_sent = sendAppointmentConfirmationEmail($appointment);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約成功 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .success-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #98FB98;
            margin-bottom: 1rem;
        }
        
        .appointment-details {
            text-align: left;
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 5px;
            margin: 1.5rem 0;
        }
        
        .detail-item {
            margin-bottom: 0.75rem;
        }
        
        .detail-label {
            font-weight: bold;
            color: #8A2BE2;
            display: inline-block;
            width: 100px;
        }
        
        .btn {
            background: #8A2BE2;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #7A1BD2;
        }
    </style>
</head>
<body>
    <header>
        <h1>預約成功</h1>
        <p>感謝您預約NmNiHealth的專家諮詢服務</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">知識庫</a></li>
            <li><a href="index.html#health">健康保健</a></li>
            <li><a href="index.html#experts">專家諮詢</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="success-container">
            <div class="success-icon">✓</div>
            <h2>預約已完成</h2>
            <p>您的預約已成功提交，我們將盡快與您確認。</p>
            <?php if ($email_sent): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 15px 0;">
                <p><strong>郵件通知：</strong>預約確認郵件已發送至您的電子郵箱，請查收。</p>
            </div>
            <?php else: ?>
            <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 15px 0;">
                <p><strong>郵件通知：</strong>由於系統原因，預約確認郵件發送失敗。請聯繫我們以確認您的預約。</p>
            </div>
            <?php endif; ?>
            
            <div class="appointment-details">
                <h3>預約詳情</h3>
                <div class="detail-item">
                    <span class="detail-label">預約編號：</span>
                    <?php echo $appointment['id']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">預約專家：</span>
                    <?php echo htmlspecialchars($appointment['expert_name']); ?>（<?php echo htmlspecialchars($appointment['specialty']); ?>）
                </div>
                <div class="detail-item">
                    <span class="detail-label">預約日期：</span>
                    <?php echo $appointment['appointment_date']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">預約時間：</span>
                    <?php echo substr($appointment['appointment_time'], 0, 5); ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">您的姓名：</span>
                    <?php echo htmlspecialchars($appointment['user_name']); ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">聯絡電話：</span>
                    <?php echo htmlspecialchars($appointment['phone']); ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">電子郵件：</span>
                    <?php echo htmlspecialchars($appointment['email']); ?>
                </div>
                <?php if ($appointment['notes']): ?>
                <div class="detail-item">
                    <span class="detail-label">備註：</span>
                    <?php echo htmlspecialchars($appointment['notes']); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <p>請確保您的聯絡資訊正確，我們將在24小時內與您確認預約。</p>
            
            <div>
                <a href="index.html" class="btn">返回首頁</a>
                <a href="appointment.php" class="btn">新增預約</a>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
</body>
</html>