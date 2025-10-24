<?php
session_start();

// 更年期症状问卷题目
$questions = [
    [
        'id' => 1,
        'question' => '您是否经常感到潮热、出汗？',
        'options' => [
            ['value' => 0, 'label' => '从不'],
            ['value' => 1, 'label' => '偶尔'],
            ['value' => 2, 'label' => '经常'],
            ['value' => 3, 'label' => '总是']
        ]
    ],
    [
        'id' => 2,
        'question' => '您是否经常感到疲倦、乏力？',
        'options' => [
            ['value' => 0, 'label' => '从不'],
            ['value' => 1, 'label' => '偶尔'],
            ['value' => 2, 'label' => '经常'],
            ['value' => 3, 'label' => '总是']
        ]
    ],
    [
        'id' => 3,
        'question' => '您的睡眠质量如何？',
        'options' => [
            ['value' => 0, 'label' => '很好'],
            ['value' => 1, 'label' => '一般'],
            ['value' => 2, 'label' => '较差'],
            ['value' => 3, 'label' => '很差']
        ]
    ],
    [
        'id' => 4,
        'question' => '您是否容易感到焦虑或情绪波动？',
        'options' => [
            ['value' => 0, 'label' => '从不'],
            ['value' => 1, 'label' => '偶尔'],
            ['value' => 2, 'label' => '经常'],
            ['value' => 3, 'label' => '总是']
        ]
    ],
    [
        'id' => 5,
        'question' => '您的记忆力是否有下降？',
        'options' => [
            ['value' => 0, 'label' => '没有'],
            ['value' => 1, 'label' => '轻微'],
            ['value' => 2, 'label' => '明显'],
            ['value' => 3, 'label' => '严重']
        ]
    ],
    [
        'id' => 6,
        'question' => '您是否有关节疼痛或肌肉酸痛？',
        'options' => [
            ['value' => 0, 'label' => '从不'],
            ['value' => 1, 'label' => '偶尔'],
            ['value' => 2, 'label' => '经常'],
            ['value' => 3, 'label' => '总是']
        ]
    ],
    [
        'id' => 7,
        'question' => '您的皮肤是否变得干燥？',
        'options' => [
            ['value' => 0, 'label' => '没有'],
            ['value' => 1, 'label' => '轻微'],
            ['value' => 2, 'label' => '明显'],
            ['value' => 3, 'label' => '严重']
        ]
    ],
    [
        'id' => 8,
        'question' => '您的体重是否有所增加？',
        'options' => [
            ['value' => 0, 'label' => '没有'],
            ['value' => 1, 'label' => '轻微'],
            ['value' => 2, 'label' => '明显'],
            ['value' => 3, 'label' => '严重']
        ]
    ],
    [
        'id' => 9,
        'question' => '您是否感到性欲下降？',
        'options' => [
            ['value' => 0, 'label' => '没有'],
            ['value' => 1, 'label' => '轻微'],
            ['value' => 2, 'label' => '明显'],
            ['value' => 3, 'label' => '严重']
        ]
    ],
    [
        'id' => 10,
        'question' => '您是否经常感到心悸？',
        'options' => [
            ['value' => 0, 'label' => '从不'],
            ['value' => 1, 'label' => '偶尔'],
            ['value' => 2, 'label' => '经常'],
            ['value' => 3, 'label' => '总是']
        ]
    ]
];

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_score = 0;
    $answers = [];
    
    foreach ($questions as $question) {
        $answer = isset($_POST['q' . $question['id']]) ? (int)$_POST['q' . $question['id']] : 0;
        $total_score += $answer;
        $answers[$question['id']] = $answer;
    }
    
    // 保存结果到session
    $_SESSION['quiz_score'] = $total_score;
    $_SESSION['quiz_answers'] = $answers;
    
    // 重定向到结果页面
    header("Location: quiz_result.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更年期症狀自檢表 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .quiz-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .quiz-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .quiz-header h1 {
            color: #8A2BE2;
            margin-bottom: 1rem;
        }
        
        .question {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .question h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .option {
            flex: 1;
            min-width: 120px;
        }
        
        .option input {
            margin-right: 0.5rem;
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
        
        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            margin: 1rem 0;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            background: #8A2BE2;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .instructions {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
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
        <div class="quiz-container">
            <div class="quiz-header">
                <h1>更年期症狀自檢表</h1>
                <p>請根據您最近一個月的感受，如實回答以下問題</p>
            </div>
            
            <div class="instructions">
                <h3>說明：</h3>
                <p>請仔細閱讀每個問題，並選擇最符合您情況的選項。<br>
                完成後將獲得個人化的評估結果和專業建議。</p>
            </div>
            
            <div class="progress-bar">
                <div class="progress" id="progress" style="width: 0%"></div>
            </div>
            <p>進度: <span id="progress-text">0</span>/10</p>
            
            <form method="POST" id="quizForm">
                <?php foreach ($questions as $index => $question): ?>
                <div class="question" id="question-<?php echo $question['id']; ?>">
                    <h3>問題 <?php echo $question['id']; ?>: <?php echo $question['question']; ?></h3>
                    <div class="options">
                        <?php foreach ($question['options'] as $option): ?>
                        <div class="option">
                            <label>
                                <input type="radio" name="q<?php echo $question['id']; ?>" value="<?php echo $option['value']; ?>" required>
                                <?php echo $option['label']; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn">完成測驗並查看結果</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        // 更新进度条
        const radios = document.querySelectorAll('input[type="radio"]');
        const progress = document.getElementById('progress');
        const progressText = document.getElementById('progress-text');
        let answered = 0;
        
        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                // 计算已回答的问题数
                const questions = document.querySelectorAll('.question');
                answered = 0;
                
                questions.forEach(question => {
                    const questionId = question.id.replace('question-', '');
                    const selected = document.querySelector(`input[name="q${questionId}"]:checked`);
                    if (selected) {
                        answered++;
                    }
                });
                
                // 更新进度条
                const percentage = (answered / 10) * 100;
                progress.style.width = percentage + '%';
                progressText.textContent = answered;
            });
        });
    </script>
</body>
</html>