<?php
session_start();

// LINE登录处理
// 注意：这需要在LINE开发者控制台注册应用并获取频道ID和密钥

// 为了演示目的，我们显示一个说明页面
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINE 登入 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .btn {
            background: #00c300;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #009a00;
        }
        
        .note {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 5px;
            margin: 1.5rem 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
        <h1>LINE 登入</h1>
        <p>使用您的LINE帳號登入</p>
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
        <div class="login-container">
            <h2>LINE 登入</h2>
            <p>點擊下方按鈕使用LINE帳號登入</p>
            
            <div class="note">
                <h3>實施說明：</h3>
                <p>要啟用LINE登入功能，您需要：</p>
                <ol>
                    <li>在 <a href="https://developers.line.biz/" target="_blank">LINE开发者中心</a> 注册应用</li>
                    <li>获取频道ID和密钥</li>
                    <li>配置重定向URL为 https://www.nmnihealth.com/line_login.php</li>
                    <li>将凭据添加到此文件中</li>
                </ol>
            </div>
            
            <a href="#" class="btn" onclick="alert('在实际部署中，这将重定向到LINE登录页面。')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                    <path d="M0 0v16h16V0H0zm13.56 10.845c-.188.338-.729.539-1.167.539-.608 0-1.09-.235-1.09-.757 0-.394.244-.64.82-1.002l.894-.568c1.305-.8 2.2-1.98 2.2-3.55 0-2.3-1.75-3.78-4.4-3.78-2.8 0-4.3 1.82-4.3 3.66 0 .4.08.94.21 1.35L2.44 9.16c-.18.33-.72.53-1.16.53-.6 0-1.08-.23-1.08-.75 0-.4.24-.64.82-1.01L2.8 6.8C1.5 8 .6 9.18.6 10.75.6 13.05 2.35 14.53 5 14.53c2.8 0 4.3-1.82 4.3-3.66 0-.4-.08-.94-.21-1.35l2.97-1.875z"/>
                </svg>
                使用LINE帳號登入
            </a>
            
            <p><a href="login.php">返回登入頁面</a></p>
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