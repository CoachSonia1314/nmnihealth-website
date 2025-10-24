<?php
// 設置文件路徑
$mdFile = 'abc.md';

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['content'])) {
        // 保存內容到MD文件
        file_put_contents($mdFile, $_POST['content']);
        $message = '文件已成功保存！';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'load') {
        // 讀取MD文件內容
        if (file_exists($mdFile)) {
            echo file_get_contents($mdFile);
        } else {
            echo '# 更年期婦女健康保健養生網站商業模式' . "\n\n";
            echo '## 1. 商業模式概述' . "\n\n";
            echo '### 1.1 服務定位' . "\n";
            echo '- 專注於更年期婦女的健康保健與養生專業平台' . "\n";
            echo '- 提供科學證據基礎的健康資訊與個人化建議' . "\n";
            echo '- 建立更年期女性社區，促進經驗分享與互相支持' . "\n\n";
            echo '### 1.2 目標客群' . "\n";
            echo '- 40-60歲女性，特別是處於或即將進入更年期的女性' . "\n";
            echo '- 關注健康與生活品質的成熟女性' . "\n";
            echo '- 對更年期知識有需求但缺乏專業指導的女性' . "\n\n";
        }
        exit;
    }
}

// 讀取MD文件內容以顯示在頁面上
if (file_exists($mdFile)) {
    $content = file_get_contents($mdFile);
} else {
    $content = '# 更年期婦女健康保健養生網站商業模式' . "\n\n";
    $content .= '## 1. 商業模式概述' . "\n\n";
    $content .= '### 1.1 服務定位' . "\n";
    $content .= '- 專注於更年期婦女的健康保健與養生專業平台' . "\n";
    $content .= '- 提供科學證據基礎的健康資訊與個人化建議' . "\n";
    $content .= '- 建立更年期女性社區，促進經驗分享與互相支持' . "\n\n";
    $content .= '### 1.2 目標客群' . "\n";
    $content .= '- 40-60歲女性，特別是處於或即將進入更年期的女性' . "\n";
    $content .= '- 關注健康與生活品質的成熟女性' . "\n";
    $content .= '- 對更年期知識有需求但缺乏專業指導的女性' . "\n\n";
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商業模式編輯器</title>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <style>
        body {
            font-family: 'Microsoft JhengHei', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #8A2BE2;
            text-align: center;
            border-bottom: 3px solid #8A2BE2;
            padding-bottom: 10px;
        }
        
        .controls {
            text-align: center;
            margin: 20px 0;
        }
        
        button {
            background-color: #8A2BE2;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
        }
        
        button:hover {
            background-color: #7A1BD2;
        }
        
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            display: none;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        textarea {
            width: 100%;
            height: 500px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>商業模式編輯器</h1>
        
        <?php if (isset($message)): ?>
        <div class="message success" style="display: block;">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div class="controls">
            <button onclick="saveContent()">保存內容</button>
        </div>
        
        <form id="editorForm" method="POST">
            <textarea name="content" id="content"><?php echo htmlspecialchars($content); ?></textarea>
            <input type="hidden" name="content" id="hiddenContent">
        </form>
    </div>

    <script>
        // 初始化CKEditor
        CKEDITOR.replace('content', {
            height: 500,
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ]
        });
        
        function saveContent() {
            // 更新隱藏字段的值
            const editorData = CKEDITOR.instances.content.getData();
            document.getElementById('hiddenContent').value = editorData;
            
            // 提交表單
            document.getElementById('editorForm').submit();
        }
    </script>
</body>
</html>