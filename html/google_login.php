<?php
session_start();

// Google登录处理
// 注意：这需要在Google开发者控制台注册应用并获取客户端ID和密钥

// 在实际应用中，您需要设置以下常量
// define('GOOGLE_CLIENT_ID', 'your_google_client_id');
// define('GOOGLE_CLIENT_SECRET', 'your_google_client_secret');
// define('GOOGLE_REDIRECT_URL', 'https://www.nmnihealth.com/google_login.php');

// 为了演示目的，我们显示一个说明页面
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google 登入 - NmNiHealth</title>
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
            background: #dd4b39;
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
            background: #c23321;
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
        <h1>Google 登入</h1>
        <p>使用您的Google帳號登入</p>
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
            <h2>Google 登入</h2>
            <p>點擊下方按鈕使用Google帳號登入</p>
            
            <div class="note">
                <h3>實施說明：</h3>
                <p>要啟用Google登入功能，您需要：</p>
                <ol>
                    <li>在 <a href="https://console.developers.google.com/" target="_blank">Google开发者控制台</a> 注册应用</li>
                    <li>获取客户端ID和密钥</li>
                    <li>配置重定向URL为 https://www.nmnihealth.com/google_login.php</li>
                    <li>将凭据添加到此文件中</li>
                </ol>
            </div>
            
            <a href="#" class="btn" onclick="alert('在实际部署中，这将重定向到Google登录页面。')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                    <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                </svg>
                使用Google帳號登入
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