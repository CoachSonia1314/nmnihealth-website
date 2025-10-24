<?php
session_start();

// Facebook登录处理
// 注意：这需要在Facebook开发者平台注册应用并获取应用ID和密钥

// 为了演示目的，我们显示一个说明页面
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook 登入 - NmNiHealth</title>
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
            background: #3b5998;
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
            background: #2d4373;
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
        <h1>Facebook 登入</h1>
        <p>使用您的Facebook帳號登入</p>
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
            <h2>Facebook 登入</h2>
            <p>點擊下方按鈕使用Facebook帳號登入</p>
            
            <div class="note">
                <h3>實施說明：</h3>
                <p>要啟用Facebook登入功能，您需要：</p>
                <ol>
                    <li>在 <a href="https://developers.facebook.com/" target="_blank">Facebook开发者平台</a> 注册应用</li>
                    <li>获取应用ID和密钥</li>
                    <li>配置重定向URL为 https://www.nmnihealth.com/facebook_login.php</li>
                    <li>将凭据添加到此文件中</li>
                </ol>
            </div>
            
            <a href="#" class="btn" onclick="alert('在实际部署中，这将重定向到Facebook登录页面。')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                </svg>
                使用Facebook帳號登入
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