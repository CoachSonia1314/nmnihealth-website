<?php
session_start();

// 使用絕對路徑包含函數文件
require_once '/var/www/www.nmnihealth.com/html/includes/appointment_functions.php';

// 檢查用戶是否已登錄
if (!isset($_SESSION['user_id'])) {
    // 未登錄用戶重定向到登錄頁面
    header("Location: login.php");
    exit();
}

// 獲取用戶信息
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$membership_level = $_SESSION['membership_level'] ?? 'free';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 處理預約表單提交
    if (isset($_POST['action']) && $_POST['action'] === 'book') {
        try {
            $user_data = [
                'user_id' => $user_id, // 使用已登錄用戶的ID
                'name' => $user_name,  // 使用已登錄用戶的姓名
                'email' => $user_email, // 使用已登錄用戶的郵箱
                'phone' => $_POST['phone'],
                'age' => $_POST['age']
            ];
            
            $appointment_data = [
                'expert_id' => $_POST['expert_id'],
                'date' => $_POST['appointment_date'],
                'time' => $_POST['appointment_time'],
                'notes' => $_POST['notes']
            ];
            
            $appointment_id = createAppointment($user_data, $appointment_data);
            
            // 重定向到成功頁面
            header("Location: appointment_success.php?id=$appointment_id");
            exit();
        } catch (Exception $e) {
            $error = "預約失敗: " . $e->getMessage();
        }
    }
}

// 獲取專家列表
$experts = getExperts();

// 如果有選擇日期，獲取該日期的已預約時段
$booked_times = [];
if (isset($_GET['date'])) {
    $booked_times = getBookedAppointments($_GET['date']);
}

// 根據會員等級定義預約優惠
$membership_benefits = [
    'free' => [
        'name' => '免費會員',
        'discount' => 0,
        'priority_booking' => false
    ],
    'premium' => [
        'name' => '黃金會員',
        'discount' => 10, // 10% 折扣
        'priority_booking' => true
    ],
    'vip' => [
        'name' => '鑽石會員',
        'discount' => 20, // 20% 折扣
        'priority_booking' => true
    ]
];

$current_membership = $membership_benefits[$membership_level];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約專家諮詢 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .appointment-form {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #8A2BE2;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Noto Sans TC', sans-serif;
            font-size: 1rem;
        }
        
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 1rem;
        }
        
        .time-slot {
            padding: 0.75rem;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .time-slot:hover {
            background: #98FB98;
        }
        
        .time-slot.selected {
            background: #8A2BE2;
            color: white;
        }
        
        .time-slot.booked {
            background: #ddd;
            color: #999;
            cursor: not-allowed;
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
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #7A1BD2;
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .experts-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .expert-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .expert-card:hover {
            transform: translateY(-5px);
        }
        
        .expert-card.selected {
            border-color: #8A2BE2;
            background: #f8f0ff;
        }
        
        .expert-card h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .membership-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: center;
        }
        
        .membership-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }
        
        .membership-free {
            background-color: #6c757d;
            color: white;
        }
        
        .membership-premium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .membership-vip {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>預約專家諮詢</h1>
        <p>會員 <?php echo htmlspecialchars($user_name); ?>，歡迎預約專業諮詢服務</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">知識庫</a></li>
            <li><a href="index.html#health">健康保健</a></li>
            <li><a href="index.html#experts">專家諮詢</a></li>
            <li><a href="member.php">會員中心</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="membership-info">
            <p>您的會員等級：<?php echo htmlspecialchars($current_membership['name']); ?>
                <span class="membership-badge membership-<?php echo $membership_level; ?>">
                    <?php echo strtoupper($membership_level); ?>
                </span>
            </p>
            <?php if ($current_membership['discount'] > 0): ?>
                <p>您享有 <?php echo $current_membership['discount']; ?>% 預約折扣</p>
            <?php endif; ?>
            <?php if ($current_membership['priority_booking']): ?>
                <p>您享有優先預約權</p>
            <?php endif; ?>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="appointment-form" id="appointmentForm">
            <input type="hidden" name="action" value="book">
            <input type="hidden" name="expert_id" id="expert_id">
            <input type="hidden" name="appointment_time" id="appointment_time">
            
            <h2>選擇專家</h2>
            <div class="experts-list">
                <?php foreach ($experts as $expert): ?>
                <div class="expert-card" data-expert-id="<?php echo $expert['id']; ?>">
                    <h3><?php echo htmlspecialchars($expert['name']); ?></h3>
                    <p><strong>專長：</strong><?php echo htmlspecialchars($expert['specialty']); ?></p>
                    <p><?php echo htmlspecialchars($expert['bio']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <h2>個人資訊</h2>
            <div class="form-group">
                <label for="name">姓名</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="email">電子郵件</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="phone">電話號碼 *</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="age">年齡</label>
                <input type="number" id="age" name="age" min="30" max="80">
            </div>
            
            <h2>選擇預約時間</h2>
            <div class="form-group">
                <label for="appointment_date">預約日期 *</label>
                <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label>可預約時段</label>
                <div class="time-slots" id="timeSlots">
                    <!-- 時段將通過JavaScript動態生成 -->
                </div>
            </div>
            
            <div class="form-group">
                <label for="notes">備註</label>
                <textarea id="notes" name="notes" rows="4" placeholder="請輸入您想諮詢的問題或特殊需求..."></textarea>
            </div>
            
            <button type="submit" class="btn" id="submitBtn" disabled>確認預約</button>
        </form>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        // 選擇專家
        const expertCards = document.querySelectorAll('.expert-card');
        expertCards.forEach(card => {
            card.addEventListener('click', function() {
                // 移除其他卡片的選中狀態
                expertCards.forEach(c => c.classList.remove('selected'));
                
                // 添加當前卡片的選中狀態
                this.classList.add('selected');
                
                // 設置隱藏字段的值
                document.getElementById('expert_id').value = this.dataset.expertId;
                
                // 啟用提交按鈕（如果有選擇時間）
                checkFormValidity();
            });
        });
        
        // 日期選擇變化時更新時段
        document.getElementById('appointment_date').addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                // 這裏應該發送AJAX請求獲取已預約時段
                // 為了簡化，我們使用模擬數據
                generateTimeSlots(selectedDate);
            }
        });
        
        // 生成時間段
        function generateTimeSlots(date) {
            const timeSlotsContainer = document.getElementById('timeSlots');
            timeSlotsContainer.innerHTML = '';
            
            // 生成預設時段（週一至週五 9:00-12:00 和 14:00-17:00，週六 9:00-12:00）
            const weekdayMorning = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30'];
            const weekdayAfternoon = ['14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];
            const saturdayMorning = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30'];
            
            const dayOfWeek = new Date(date).getDay();
            let timeSlots = [];
            
            if (dayOfWeek === 6) { // 週六
                timeSlots = saturdayMorning;
            } else if (dayOfWeek > 0 && dayOfWeek < 6) { // 週一至週五
                timeSlots = [...weekdayMorning, ...weekdayAfternoon];
            }
            
            // 這裏應該從服務器獲取已預約的時段
            // 為了簡化，我們使用空數組
            const bookedTimes = []; // <?php echo json_encode($booked_times); ?>;
            
            timeSlots.forEach(time => {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.textContent = time;
                timeSlot.dataset.time = time;
                
                if (bookedTimes.includes(time)) {
                    timeSlot.classList.add('booked');
                } else {
                    timeSlot.addEventListener('click', function() {
                        // 移除其他時段的選中狀態
                        document.querySelectorAll('.time-slot').forEach(slot => {
                            if (!slot.classList.contains('booked')) {
                                slot.classList.remove('selected');
                            }
                        });
                        
                        // 添加當前時段的選中狀態
                        this.classList.add('selected');
                        
                        // 設置隱藏字段的值
                        document.getElementById('appointment_time').value = this.dataset.time;
                        
                        // 啟用提交按鈕（如果有選擇專家）
                        checkFormValidity();
                    });
                }
                
                timeSlotsContainer.appendChild(timeSlot);
            });
        }
        
        // 檢查表單有效性
        function checkFormValidity() {
            const expertId = document.getElementById('expert_id').value;
            const appointmentTime = document.getElementById('appointment_time').value;
            
            const submitBtn = document.getElementById('submitBtn');
            if (expertId && appointmentTime) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }
        
        // 表單提交驗證
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const expertId = document.getElementById('expert_id').value;
            const appointmentTime = document.getElementById('appointment_time').value;
            
            if (!expertId || !appointmentTime) {
                e.preventDefault();
                alert('請選擇專家和預約時間');
            }
        });
    </script>
</body>
</html>