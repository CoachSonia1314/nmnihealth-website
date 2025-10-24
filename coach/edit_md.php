<?php
// 檔案路徑
$md_file = "abc.md";

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 取得編輯後的內容
    $content = $_POST["content"] ?? "";
    
    // 儲存到檔案
    if (file_put_contents($md_file, $content) !== false) {
        $message = "檔案已成功儲存！";
    } else {
        $message = "儲存失敗，請檢查檔案權限。";
    }
}

// 讀取 MD 檔案內容
$content = file_get_contents($md_file);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯 MD 檔案</title>
    <script src="https://cdn.ckeditor.com/4.24.0/standard/ckeditor.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            min-height: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>編輯 MD 檔案</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, "成功") !== false ? "success" : "error"; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <textarea name="content" id="editor"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <button type="submit" class="btn">儲存變更</button>
        </form>
    </div>

    <script>
        // 初始化 CKEditor
        CKEDITOR.replace("editor", {
            height: 500,
            toolbar: [
                ["Bold", "Italic", "Underline"],
                ["NumberedList", "BulletedList"],
                ["Link", "Unlink"],
                ["Source"]
            ]
        });
    </script>
</body>
</html>
