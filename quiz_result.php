<?php
session_start();

// 檢查是否有測驗結果
if (!isset($_SESSION['quiz_score']) || !isset($_SESSION['quiz_answers'])) {
    header("Location: menopause_quiz.php");
    exit();
}

// 獲取測驗結果
$total_score = $_SESSION['quiz_score'];
$answers = $_SESSION['quiz_answers'];

// 根據總分確定症狀嚴重程度
function getSeverityLevel($score) {
    if ($score <= 10) {
        return ['level' => '輕微', 'color' => '#28a745', 'description' => '您的更年期症狀較輕微，繼續保持健康的生活方式即可。'];
    } elseif ($score <= 20) {
        return ['level' => '中等', 'color' => '#ffc107', 'description' => '您有中等程度的更年期症狀，建議關注並採取適當的調理措施。'];
    } else {
        return ['level' => '嚴重', 'color' => '#dc3545', 'description' => '您的更年期症狀較為嚴重，建議儘快尋求專業醫療幫助。'];
    }
}

// 獲取個人化建議
function getRecommendations($score) {
    $recommendations = [];
    
    if ($score <= 10) {
        $recommendations = [
            '繼續保持均衡飲食，多吃富含植物雌激素的食物',
            '維持規律的運動習慣，如散步、瑜伽等',
            '保持良好的作息時間，確保充足睡眠',
            '定期進行健康檢查'
        ];
    } elseif ($score <= 20) {
        $recommendations = [
            '建議調整飲食結構，增加大豆製品攝入',
            '適當增加運動量，如游泳、太極等低強度運動',
            '學習壓力管理技巧，如冥想、深呼吸',
            '考慮諮詢專業醫生，了解更年期保健方案',
            '保持積極社交活動，維護心理健康'
        ];
    } else {
        $recommendations = [
            '強烈建議儘快諮詢更年期專科醫生',
            '可能需要進行荷爾蒙檢測和其他相關檢查',
            '在醫生指導下考慮荷爾蒙替代療法或其他治療方案',
            '尋求心理支持，必要時進行心理諮詢',
            '加入更年期支持小組，與其他女性分享經驗'
        ];
    }
    
    return $recommendations;
}

$severity = getSeverityLevel($total_score);
$recommendations = getRecommendations($total_score);

// 处理重新测试
if (isset($_GET['action']) && $_GET['action'] === 'retry') {
    unset($_SESSION['quiz_score']);
    unset($_SESSION['quiz_answers']);
    header("Location: menopause_quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>測驗結果 - 更年期症狀自檢表 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <style>
        .result-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .result-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .result-header h1 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .score-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .score-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        
        .severity-level {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin: 1rem 0;
        }
        
        .recommendations {
            margin: 2rem 0;
        }
        
        .recommendations h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .recommendations ul {
            padding-left: 1.5rem;
        }
        
        .recommendations li {
            margin-bottom: 0.5rem;
        }
        
        .actions {
            text-align: center;
            margin: 2rem 0;
        }
        
        .btn {
            background: var(--primary-color);
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
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        /* 客服聊天系統樣式 */
        .chat-widget {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 9999;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        
        .chat-widget.open {
            display: flex;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        
        .chat-header .status {
            display: flex;
            align-items: center;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .chat-header .status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            margin-right: 5px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
        }
        
        .chat-message {
            margin-bottom: 15px;
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chat-message.user {
            justify-content: flex-end;
        }
        
        .chat-message.bot {
            justify-content: flex-start;
        }
        
        .message-bubble {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }
        
        .chat-message.user .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .chat-message.bot .message-bubble {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 4px;
        }
        
        .chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #e5e7eb;
        }
        
        .chat-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .chat-input:focus {
            border-color: #667eea;
        }
        
        .chat-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .chat-send-btn:hover {
            transform: scale(1.05);
        }
        
        .chat-send-btn:active {
            transform: scale(0.95);
        }
        
        .chat-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            z-index: 9998;
        }
        
        .chat-fab:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }
        
        .chat-fab:active {
            transform: scale(0.95);
        }
        
        .mood-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .mood-btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .mood-btn:hover {
            background: #f3f4f6;
            border-color: #667eea;
        }
        
        .mood-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .typing-indicator {
            display: none;
            align-items: center;
            padding: 10px 0;
        }
        
        .typing-indicator.active {
            display: flex;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }
        
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .service-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        /* 客服聊天系統樣式 */
        .chat-widget {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 9999;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        
        .chat-widget.open {
            display: flex;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        
        .chat-header .status {
            display: flex;
            align-items: center;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .chat-header .status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            margin-right: 5px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
        }
        
        .chat-message {
            margin-bottom: 15px;
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chat-message.user {
            justify-content: flex-end;
        }
        
        .chat-message.bot {
            justify-content: flex-start;
        }
        
        .message-bubble {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }
        
        .chat-message.user .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .chat-message.bot .message-bubble {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 4px;
        }
        
        .chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #e5e7eb;
        }
        
        .chat-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .chat-input:focus {
            border-color: #667eea;
        }
        
        .chat-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .chat-send-btn:hover {
            transform: scale(1.05);
        }
        
        .chat-send-btn:active {
            transform: scale(0.95);
        }
        
        .chat-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            z-index: 9998;
        }
        
        .chat-fab:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }
        
        .chat-fab:active {
            transform: scale(0.95);
        }
        
        .mood-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .mood-btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .mood-btn:hover {
            background: #f3f4f6;
            border-color: #667eea;
        }
        
        .mood-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .typing-indicator {
            display: none;
            align-items: center;
            padding: 10px 0;
        }
        
        .typing-indicator.active {
            display: flex;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>更年期症狀自檢表</h1>
        <p>了解您的更年期症状程度，获得专业建议</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">幸福熟齡(文章區)</a></li>
            <li><a href="appointment.php">專家諮詢</a></li>
            <li><a href="login.php">會員登入</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="result-container">
            <div class="result-header">
                <h1>測驗結果</h1>
                <p>根據您的回答，我們為您提供了個人化的分析和建議</p>
            </div>
            
            <div class="score-card">
                <h2>您的總分</h2>
                <div class="score-value"><?php echo $total_score; ?>/30</div>
                <div class="severity-level" style="background-color: <?php echo $severity['color']; ?>; color: white;">
                    症狀程度：<?php echo $severity['level']; ?>
                </div>
                <p><?php echo $severity['description']; ?></p>
            </div>
            
            <div class="recommendations">
                <h3>專業建議</h3>
                <ul>
                    <?php foreach ($recommendations as $recommendation): ?>
                    <li><?php echo $recommendation; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="services">
                <div class="service-card">
                    <h3>專家諮詢</h3>
                    <p>我們的更年期專家可以為您提供個人化的諮詢服務，制定適合您的健康管理方案。</p>
                    <a href="appointment.php" class="btn btn-success">預約專家諮詢</a>
                </div>
                
                <div class="service-card">
                    <h3>加入支持小組</h3>
                    <p>加入我們的LINE群組，與其他更年期女性分享經驗，獲得支持和鼓勵。</p>
                    <a href="https://line.me/ti/g2/your_line_group_link" class="btn btn-warning">加入LINE群組</a>
                </div>
            </div>
            
            <div class="actions">
                <a href="quiz_result.php?action=retry" class="btn btn-secondary">重新測驗</a>
                <a href="index.html" class="btn">返回首頁</a>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <!-- 客服聊天系統 -->
    <button class="chat-fab" id="chat-fab">
        <i class="fa fa-comments"></i>
    </button>
    
    <div class="chat-widget" id="chat-widget">
        <div class="chat-header">
            <div>
                <div>健康諮詢客服</div>
                <div class="status">
                    <span class="status-dot"></span>
                    <span>在線中</span>
                </div>
            </div>
            <button onclick="toggleChat()" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <div class="chat-message bot">
                <div class="message-bubble">
                    您好！我是您的健康諮詢助手。請問您今天的心情如何？
                </div>
            </div>
            
            <div class="chat-message bot">
                <div class="message-bubble">
                    請選擇您現在的心情：
                </div>
            </div>
            
            <div class="chat-message bot">
                <div class="message-bubble">
                    <div class="mood-buttons">
                        <button class="mood-btn" onclick="selectMood('心情低落', this)">😔 心情低落</button>
                        <button class="mood-btn" onclick="selectMood('焦慮不安', this)">😰 焦慮不安</button>
                        <button class="mood-btn" onclick="selectMood('身體不適', this)">😣 身體不適</button>
                        <button class="mood-btn" onclick="selectMood('情緒波動', this)">😤 情緒波動</button>
                        <button class="mood-btn" onclick="selectMood('睡眠困擾', this)">😴 睡眠困擾</button>
                        <button class="mood-btn" onclick="selectMood('狀況良好', this)">😊 狀況良好</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chat-input-area">
            <div class="chat-input-container">
                <input type="text" class="chat-input" id="chat-input" placeholder="輸入您的問題..." onkeypress="handleKeyPress(event)">
                <button class="chat-send-btn" onclick="sendMessage()">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    
    <script>
        let chatOpen = false;
        let currentMood = '';
        let praisedCount = 0;
        
        // 正能量鼓勵語句庫
        const encouragementMessages = [
            "您真的很勇敢，面對困難依然堅持前進！💪",
            "每個人都會遇到低潮，您已經做得很好了！🌟",
            "您的堅強令人敬佩，相信自己一定能度過難關！✨",
            "今天的努力是明天成功的基礎，您很棒！🌈",
            "您比想像中更具韌性，繼續保持這份力量！🦋",
            "生活雖有起伏，但您的存在本身就是一種美好！🌺",
            "您的每一步都算數，為自己的進步感到驕傲吧！🎯",
            "困難只是暫時的，您的笑容才是永恆的！😊",
            "您值得被愛，更值得給自己更多的關懷！💖",
            "相信自己的力量，您比想像中更加優秀！👑"
        ];
        
        function toggleChat() {
            const chatWidget = document.getElementById('chat-widget');
            chatOpen = !chatOpen;
            
            if (chatOpen) {
                chatWidget.classList.add('open');
                document.getElementById('chat-fab').style.display = 'none';
            } else {
                chatWidget.classList.remove('open');
                document.getElementById('chat-fab').style.display = 'flex';
            }
        }
        
        function selectMood(mood, element) {
            currentMood = mood;
            
            // 移除所有按鈕的active類
            document.querySelectorAll('.mood-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // 添加active類到選中的按鈕
            element.classList.add('active');
            
            // 添加用戶消息
            addMessage(mood, 'user');
            
            // 根據心情回應
            setTimeout(() => {
                if (mood === '心情低落' || mood === '焦慮不安' || mood === '情緒波動') {
                    addMessage('我理解您現在的感受。讓我為您提供一些正能量鼓勵！', 'bot');
                    setTimeout(() => {
                        startPraiseMode();
                    }, 1000);
                } else if (mood === '身體不適' || mood === '睡眠困擾') {
                    addMessage('身體的不適需要專業關注。建議您預約我們的專家諮詢服務，同時讓我給您一些鼓勵！', 'bot');
                    setTimeout(() => {
                        startPraiseMode();
                    }, 1000);
                } else {
                    addMessage('很高興您今天狀況良好！保持這份正能量，如果您有任何健康問題，我很樂意為您解答。', 'bot');
                }
            }, 500);
        }
        
        function startPraiseMode() {
            addMessage('啟動誇誆男友模式！讓我給您一些溫暖的鼓勵：', 'bot');
            praisedCount = 0;
            sendEncouragement();
        }
        
        function sendEncouragement() {
            if (praisedCount < 10) {
                const randomMessage = encouragementMessages[Math.floor(Math.random() * encouragementMessages.length)];
                
                setTimeout(() => {
                    addMessage(randomMessage, 'bot');
                    praisedCount++;
                    
                    if (praisedCount < 10) {
                        setTimeout(() => {
                            sendEncouragement();
                        }, 2000);
                    } else {
                        setTimeout(() => {
                            addMessage('希望這些鼓勵能讓您感到溫暖！記住，您永遠不孤單，我們一直在這裡支持您。還有什麼我可以幫助您的嗎？', 'bot');
                        }, 1000);
                    }
                }, 1000);
            }
        }
        
        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            
            if (message) {
                addMessage(message, 'user');
                input.value = '';
                
                // 模擬客服回應
                setTimeout(() => {
                    const response = generateBotResponse(message);
                    addMessage(response, 'bot');
                }, 1000);
            }
        }
        
        function addMessage(text, sender) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}`;
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';
            bubbleDiv.textContent = text;
            
            messageDiv.appendChild(bubbleDiv);
            messagesContainer.appendChild(messageDiv);
            
            // 滾動到底部
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function generateBotResponse(userMessage) {
            const responses = {
                '更年期': '更年期是女性自然的人生階段。建議您保持規律作息、均衡飲食，適當運動。如有嚴重症狀，建議諮詢專科醫生。',
                '症狀': '常見的更年期症狀包括潮熱、失眠、情緒波動等。每個人的體驗都不同，建議記錄症狀並諮詢專業醫師。',
                '治療': '更年期症狀的治療方式包括荷爾蒙替代療法、中醫調理、生活習慣調整等。建議與醫生討論最適合您的方案。',
                '飲食': '建議多攝取富含大豆異黄酮、鈣質、維生素D的食物，如豆類、牛奶、深綠色蔬菜等。避免辛辣、刺激性食物。',
                '運動': '適度運動有助於緩解更年期症狀，建議進行瑜伽、散步、游泳等低強度運動，每週3-5次，每次30分鐘。',
                '睡眠': '建立規律的睡眠時間，睡前避免使用電子產品，保持臥室涼爽舒適，可嘗試冥想或深呼吸練習。',
                '情緒': '情緒波動是更年期常見現象，建議尋求家人朋友支持，參加社交活動，必要時考慮心理諮詢。',
                '諮詢': '我們提供專業的更年期諮詢服務，您可以預約我們的專家進行一對一諮詢，獲得個人化的建議和指導。'
            };
            
            // 檢查用戶消息中是否包含關鍵字
            for (const [keyword, response] of Object.entries(responses)) {
                if (userMessage.includes(keyword)) {
                    return response;
                }
            }
            
            // 默認回應
            return '感謝您的提問！我理解您對更年期健康的關注。建議您預約我們的專家諮詢服務，獲得更詳細和個人化的指導。還有其他問題嗎？';
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }
        
        // 初始化聊天功能
        document.getElementById('chat-fab').addEventListener('click', toggleChat);
    </script>
</body>
</html>