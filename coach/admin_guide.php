<?php
session_start();

// 检查用户是否已登录且为管理员
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>網站管理使用說明 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-guide-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
        }
        
        .section {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .section h2 {
            color: var(--primary-color);
            margin-top: 0;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }
        
        .section h3 {
            color: var(--secondary-color);
            margin-top: 1.5rem;
        }
        
        .guide-steps {
            counter-reset: step-counter;
            list-style: none;
            padding-left: 0;
        }
        
        .guide-steps li {
            counter-increment: step-counter;
            margin-bottom: 1.5rem;
            padding-left: 3rem;
            position: relative;
        }
        
        .guide-steps li:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: var(--primary-color);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
            transition: all 0.3s ease;
            margin: 0.5rem;
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
        
        .note {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            border-left: 4px solid #ffc107;
        }
        
        .tip {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            border-left: 4px solid #17a2b8;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        th {
            background: var(--primary-color);
            color: white;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .quick-link {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .quick-link:hover {
            background: #dee2e6;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>網站管理使用說明</h1>
        <p>管理員專用操作指南</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="admin.php">後台首頁</a></li>
            <li><a href="admin_guide.php">管理說明</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="admin-guide-container">
            <div class="page-header">
                <h1>網站管理系統使用說明</h1>
                <p>歡迎使用NmNiHealth網站管理系統</p>
            </div>
            
            <div class="quick-links">
                <a href="#user-management" class="quick-link">用戶管理</a>
                <a href="#article-management" class="quick-link">文章管理</a>
                <a href="#affiliate-management" class="quick-link">聯盟行銷</a>
                <a href="#appointment-management" class="quick-link">預約管理</a>
                <a href="#expert-management" class="quick-link">專家管理</a>
            </div>
            
            <div class="section">
                <h2>系統概覽</h2>
                <p>本系統為NmNiHealth更年期健康保健網站的後台管理系統，提供完整的內容管理和用戶服務功能。</p>
                
                <h3>主要功能模組</h3>
                <ul class="guide-steps">
                    <li><strong>用戶管理</strong> - 管理會員註冊、會員等級、聯盟行銷分潤等</li>
                    <li><strong>文章管理</strong> - 創建和管理健康保健相關文章，支持SEO優化</li>
                    <li><strong>聯盟行銷</strong> - 設定聯盟行銷連結和分潤比例</li>
                    <li><strong>預約管理</strong> - 管理用戶與專家的諮詢預約</li>
                    <li><strong>專家管理</strong> - 管理健康諮詢專家資訊</li>
                </ul>
            </div>
            
            <div class="section" id="user-management">
                <h2>用戶管理</h2>
                <p>用戶管理模組用於管理網站會員註冊用戶、會員等級和聯盟行銷分潤。</p>
                
                <h3>會員等級設定</h3>
                <table>
                    <thead>
                        <tr>
                            <th>等級</th>
                            <th>費用</th>
                            <th>權益說明</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>免費會員</td>
                            <td>NT$ 0/月</td>
                            <td>基本健康資訊、文章閱讀權限、基礎健康評估</td>
                        </tr>
                        <tr>
                            <td>黃金會員</td>
                            <td>NT$ 299/月</td>
                            <td>免費會員所有功能 + 個人化健康方案、專家諮詢9折、專屬電子報</td>
                        </tr>
                        <tr>
                            <td>鑽石會員</td>
                            <td>NT$ 599/月</td>
                            <td>黃金會員所有功能 + 無限次專家諮詢、優先預約權、專屬健康顧問、線下活動邀請</td>
                        </tr>
                    </tbody>
                </table>
                
                <h3>聯盟行銷分潤</h3>
                <p>會員可透過聯盟行銷賺取分潤，管理員可設定分潤比例並查看分潤記錄。</p>
                <div class="tip">
                    <strong>提示：</strong>建議將分潤比例設定在5-15%之間，以保持競爭力同時確保平台收益。
                </div>
            </div>
            
            <div class="section" id="article-management">
                <h2>文章管理</h2>
                <p>文章管理模組用於創建和管理網站內容，支持SEO優化功能。</p>
                
                <h3>創建新文章步驟</h3>
                <ol class="guide-steps">
                    <li>點擊「文章管理」→「新增文章」</li>
                    <li>填寫文章標題、選擇分類</li>
                    <li>撰寫文章內容（支持富文本編輯）</li>
                    <li>添加特色圖片（可選）</li>
                    <li>設定SEO關鍵字和描述</li>
                    <li>添加聯盟行銷連結（可選）</li>
                    <li>設定會員專屬福利（可選）</li>
                    <li>選擇文章狀態（草稿/待審核/已發布/已封存）</li>
                    <li>點擊「保存文章」</li>
                </ol>
                
                <h3>SEO優化功能</h3>
                <div class="note">
                    <strong>注意：</strong>使用「生成SEO文章」按鈕可自動生成符合SEO標準的文章結構和關鍵字。
                </div>
                
                <h3>文章分類</h3>
                <ul>
                    <li><strong>更年期知識庫</strong> - 基礎知識、症狀解析和醫學新知</li>
                    <li><strong>健康保健</strong> - 營養、運動和預防醫學建議</li>
                    <li><strong>心理調適</strong> - 情緒管理、壓力舒緩和人際關係維護</li>
                    <li><strong>美麗養生</strong> - 肌膚保養、體態管理和個人風格建立</li>
                </ul>
            </div>
            
            <div class="section" id="affiliate-management">
                <h2>聯盟行銷管理</h2>
                <p>聯盟行銷功能允許在文章中添加推薦產品連結，並設定分潤比例。</p>
                
                <h3>添加聯盟行銷連結</h3>
                <ol class="guide-steps">
                    <li>在文章編輯頁面找到「聯盟行銷連結」欄位</li>
                    <li>按照格式填寫：連結URL|產品名稱|分潤比例</li>
                    <li>每行一個產品連結</li>
                    <li>例如：https://example.com/product|更年期保健食品|8%</li>
                </ol>
                
                <h3>分潤計算與支付</h3>
                <p>系統會自動追蹤點擊和轉換，並計算分潤金額。</p>
                <div class="tip">
                    <strong>提示：</strong>每月月底結算分潤，請確保會員已填寫正確的匯款帳戶資訊。
                </div>
            </div>
            
            <div class="section" id="appointment-management">
                <h2>預約管理</h2>
                <p>預約管理模組用於管理用戶與專家的諮詢預約。</p>
                
                <h3>預約狀態說明</h3>
                <ul>
                    <li><strong>待確認</strong> - 用戶已提交預約，等待管理員確認</li>
                    <li><strong>已確認</strong> - 預約已確認，等待諮詢時間</li>
                    <li><strong>已完成</strong> - 咨詢已完成</li>
                    <li><strong>已取消</strong> - 預約已取消</li>
                </ul>
                
                <h3>操作流程</h3>
                <ol class="guide-steps">
                    <li>檢查「預約管理」頁面的新預約請求</li>
                    <li>確認預約時間和專家 availability</li>
                    <li>更新預約狀態為「已確認」</li>
                    <li>在預約時間後將狀態更新為「已完成」</li>
                </ol>
            </div>
            
            <div class="section" id="expert-management">
                <h2>專家管理</h2>
                <p>專家管理模組用於管理健康諮詢專家資訊。</p>
                
                <h3>專家資訊欄位</h3>
                <ul>
                    <li><strong>姓名</strong> - 專家姓名</li>
                    <li><strong>專長</strong> - 專家專業領域</li>
                    <li><strong>簡介</strong> - 專家背景和資歷</li>
                    <li><strong>可預約時間</strong> - 專家可提供諮詢的時間段</li>
                </ul>
                
                <h3>添加新專家</h3>
                <ol class="guide-steps">
                    <li>點擊「專家管理」→「新增專家」</li>
                    <li>填寫專家詳細資訊</li>
                    <li>設定可預約時間</li>
                    <li>上傳專家照片（可選）</li>
                    <li>點擊「保存專家資訊」</li>
                </ol>
            </div>
            
            <div class="section">
                <h2>常見問題</h2>
                
                <h3>如何重設用戶密碼？</h3>
                <p>目前系統不支持管理員直接重設用戶密碼。請指導用戶使用「忘記密碼」功能自行重設。</p>
                
                <h3>如何修改文章分潤比例？</h3>
                <p>編輯文章時，在「聯盟行銷連結」欄位中修改相應產品的分潤比例。</p>
                
                <h3>如何查看聯盟行銷分潤報告？</h3>
                <p>目前分潤數據顯示在用戶的「聯盟行銷分潤」頁面，管理員可通過數據庫查詢獲取詳細報告。</p>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="admin.php" class="btn">返回管理後台</a>
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