<?php
require_once '/var/www/www.nmnihealth.com/html/includes/article_functions.php';

// 處理圖片上傳
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/var/www/www.nmnihealth.com/html/images/';
    
    // 確保上傳目錄存在
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = $_FILES['image']['name'];
    $fileTmpName = $_FILES['image']['tmp_name'];
    $fileSize = $_FILES['image']['size'];
    $fileError = $_FILES['image']['error'];
    
    // 檢查是否有上傳錯誤
    if ($fileError === UPLOAD_ERR_OK) {
        // 生成唯一的文件名
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('article_') . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $uniqueFileName;
        
        // 檢查文件類型
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            // 檢查文件大小（限制為10MB）
            if ($fileSize <= 10 * 1024 * 1024) {
                // 移動文件到上傳目錄
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    // 返回圖片URL
                    $imageUrl = 'https://www.nmnihealth.com/images/' . $uniqueFileName;
                    echo json_encode(['success' => true, 'url' => $imageUrl, 'filename' => $uniqueFileName]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'error' => '文件移動失敗']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'error' => '文件大小超過限制（10MB）']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'error' => '不支持的文件類型']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'error' => '上傳錯誤: ' . $fileError]);
        exit();
    }
}

// 獲取所有分類
$categories = getCategories();

// 處理文章表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    try {
        // 调试信息 - 检查接收到的数据
        error_log("POST data received: " . print_r($_POST, true));
        
        $article_data = [
            'title' => $_POST['title'],
            'content' => $_POST['content'] ?? '',
            'excerpt' => $_POST['excerpt'] ?? '',
            'keywords' => $_POST['keywords'] ?? '',
            'description' => $_POST['description'] ?? '',
            'featured_image' => $_POST['featured_image'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'category_id' => $_POST['category_id'] ?? 0
        ];
        
        // 如果是編輯現有文章
        if (isset($_POST['id']) && $_POST['id']) {
            $article_data['id'] = $_POST['id'];
        }
        
        $article_id = saveArticle($article_data);
        
        // 重定向到文章列表頁面
        header("Location: article_list.php");
        exit();
    } catch (Exception $e) {
        $error = "保存文章失敗: " . $e->getMessage();
        error_log("Save article error: " . $e->getMessage());
    }
}

// 如果是編輯現有文章，獲取文章數據
$article = null;
if (isset($_GET['id'])) {
    $article = getArticle($_GET['id']);
}

// 獲取分類名稱
function getCategoryName($category_id) {
    global $categories;
    foreach ($categories as $category) {
        if ($category['id'] == $category_id) {
            return $category['name'];
        }
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? '編輯文章' : '新增文章'; ?> - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <style>
        .article-form {
            max-width: 1000px;
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
        
        textarea {
            min-height: 150px;
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
            margin-right: 1rem;
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
        
        .image-preview {
            max-width: 300px;
            margin-top: 1rem;
        }
        
        .image-preview img {
            width: 100%;
            border-radius: 5px;
        }
        
        .seo-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin: 2rem 0;
        }
        
        .seo-section h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .suggested-keywords {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 0.5rem;
        }
        
        .upload-section {
            border: 2px dashed #ddd;
            padding: 2rem;
            text-align: center;
            margin: 1rem 0;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .upload-section:hover {
            border-color: #8A2BE2;
        }
        
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .image-item {
            position: relative;
            cursor: pointer;
        }
        
        .image-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .image-item .checkmark {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #8A2BE2;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            display: none;
        }
        
        .image-item.selected .checkmark {
            display: flex;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $article ? '編輯文章' : '新增文章'; ?></h1>
        <p>創建和管理您的更年期健康保健文章</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="article_list.php">文章管理</a></li>
            <li><a href="appointment.php">預約管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="article-form" id="articleForm" enctype="multipart/form-data">
            <?php if ($article): ?>
            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title">文章標題 *</label>
                <input type="text" id="title" name="title" value="<?php echo $article ? htmlspecialchars($article['title']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">文章分類 *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">請選擇分類</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($article && $article['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="excerpt">文章摘要</label>
                <textarea id="excerpt" name="excerpt" placeholder="簡短描述文章內容..."><?php echo $article ? htmlspecialchars($article['excerpt']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="content">文章內容 *</label>
                <textarea id="content" name="content" required><?php echo $article ? htmlspecialchars($article['content']) : ''; ?></textarea>
            </div>
            
            <div class="upload-section" id="uploadSection">
                <h3>圖片上傳</h3>
                <p>上傳特色圖片或文章配圖</p>
                <input type="file" id="imageUpload" name="image" accept="image/*" style="display: none;">
                <button type="button" class="btn btn-secondary" id="uploadBtn">選擇圖片</button>
                <div id="uploadStatus"></div>
            </div>
            
            <div class="image-grid" id="imageGallery">
                <!-- 圖片庫將通過JavaScript動態加載 -->
            </div>
            
            <div class="form-group">
                <label for="featured_image">特色圖片 URL</label>
                <input type="text" id="featured_image" name="featured_image" value="<?php echo $article ? htmlspecialchars($article['featured_image']) : ''; ?>" placeholder="https://example.com/image.jpg">
                <div class="image-preview" id="imagePreview">
                    <?php if ($article && $article['featured_image']): ?>
                    <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="特色圖片預覽">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="affiliate_links">聯盟行銷連結</label>
                <textarea id="affiliate_links" name="affiliate_links" placeholder="輸入聯盟行銷連結，每行一個
例如：https://example.com/product1|產品名稱|分潤比例
https://example.com/product2|產品名稱2|分潤比例"><?php echo $article ? htmlspecialchars($article['affiliate_links']) : ''; ?></textarea>
                <small>格式：連結URL|產品名稱|分潤比例（如5%）</small>
            </div>
            
            <div class="form-group">
                <label for="member_benefits">會員專屬福利</label>
                <textarea id="member_benefits" name="member_benefits" placeholder="描述會員獨享的福利和優惠"><?php echo $article ? htmlspecialchars($article['member_benefits'] ?? '') : ''; ?></textarea>
            </div>
            
            <div class="seo-section">
                <h3>SEO優化設定</h3>
                
                <div class="form-group">
                    <label for="keywords">關鍵字 (Keywords)</label>
                    <input type="text" id="keywords" name="keywords" value="<?php echo $article ? htmlspecialchars($article['keywords']) : ''; ?>" placeholder="輸入關鍵字，用逗號分隔">
                    <div class="suggested-keywords" id="suggestedKeywords" style="display: none;">
                        <strong>建議關鍵字：</strong><span id="keywordList"></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">描述 (Description)</label>
                    <textarea id="description" name="description" placeholder="輸入頁面描述，建議不超過160個字符"><?php echo $article ? htmlspecialchars($article['description']) : ''; ?></textarea>
                    <div id="descriptionCount">字符數: <span id="descriptionLength">0</span>/160</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">文章狀態</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo ($article && $article['status'] == 'draft') ? 'selected' : ''; ?>>草稿</option>
                    <option value="pending" <?php echo ($article && $article['status'] == 'pending') ? 'selected' : ''; ?>>待審核</option>
                    <option value="published" <?php echo ($article && $article['status'] == 'published') ? 'selected' : ''; ?>>已發布</option>
                    <option value="archived" <?php echo ($article && $article['status'] == 'archived') ? 'selected' : ''; ?>>已封存</option>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">保存文章</button>
                <a href="article_list.php" class="btn btn-secondary">取消</a>
                <button type="button" class="btn btn-secondary" id="generateArticleBtn">生成SEO文章</button>
                <button type="button" class="btn btn-secondary" id="optimizeSEOBtn">優化SEO</button>
            </div>
        </form>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        // 初始化CKEditor
        CKEDITOR.replace('content', {
            height: 400,
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
        
        // 圖片上傳功能
        document.getElementById('uploadBtn').addEventListener('click', function() {
            document.getElementById('imageUpload').click();
        });
        
        document.getElementById('imageUpload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('image', this.files[0]);
                
                const uploadStatus = document.getElementById('uploadStatus');
                uploadStatus.innerHTML = '上傳中...';
                
                fetch('article_edit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadStatus.innerHTML = '上傳成功！';
                        // 自動將上傳的圖片設置為配圖
                        document.getElementById('featured_image').value = data.url;
                        // 顯示圖片預覽
                        document.getElementById('imagePreview').innerHTML = '<img src="' + data.url + '" alt="特色圖片預覽">';
                        // 清空文件輸入框
                        document.getElementById('imageUpload').value = '';
                        // 重新加載圖片庫
                        loadImageGallery();
                        // 添加成功提示
                        alert('圖片上傳成功並已自動設置為文章配圖！');
                    } else {
                        uploadStatus.innerHTML = '上傳失敗: ' + data.error;
                        console.error('上傳失敗:', data.error);
                    }
                })
                .catch(error => {
                    uploadStatus.innerHTML = '上傳失敗: ' + error.message;
                    console.error('上傳錯誤:', error);
                });
            }
        });
        
        // 加載圖片庫
        function loadImageGallery() {
            // 從服務器獲取已上傳的圖片列表
            const imageGallery = document.getElementById('imageGallery');
            imageGallery.innerHTML = '<p>加載圖片庫中...</p>';
            
            // 在實際應用中，這裡應該調用服務器API獲取圖片列表
            // 為了演示，我們使用一些示例圖片
            setTimeout(() => {
                imageGallery.innerHTML = '';
                
                // 添加示例圖片（實際應用中應該從服務器獲取）
                const sampleImages = [
                    'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
                    'https://images.unsplash.com/photo-1512295767273-ac109ac3acfa?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
                ];
                
                sampleImages.forEach((imageUrl, index) => {
                    const imageItem = document.createElement('div');
                    imageItem.className = 'image-item';
                    imageItem.innerHTML = `
                        <img src="${imageUrl}" alt="圖片 ${index + 1}">
                        <div class="checkmark">✓</div>
                    `;
                    
                    imageItem.addEventListener('click', function() {
                        // 移除其他圖片的選中狀態
                        document.querySelectorAll('.image-item').forEach(item => {
                            item.classList.remove('selected');
                        });
                        
                        // 添加當前圖片的選中狀態
                        this.classList.add('selected');
                        
                        // 設置特色圖片URL
                        document.getElementById('featured_image').value = imageUrl;
                        
                        // 顯示圖片預覽
                        document.getElementById('imagePreview').innerHTML = `<img src="${imageUrl}" alt="特色圖片預覽">`;
                    });
                    
                    imageGallery.appendChild(imageItem);
                });
            }, 500);
        }
        
        // 初始化圖片庫
        loadImageGallery();
        
        // 圖片預覽功能
        document.getElementById('featured_image').addEventListener('change', function() {
            const imageUrl = this.value;
            const previewContainer = document.getElementById('imagePreview');
            
            if (imageUrl) {
                previewContainer.innerHTML = '<img src="' + imageUrl + '" alt="特色圖片預覽">';
            } else {
                previewContainer.innerHTML = '';
            }
        });
        
        // SEO描述字符計數
        const descriptionField = document.getElementById('description');
        const descriptionLength = document.getElementById('descriptionLength');
        
        if (descriptionField) {
            descriptionField.addEventListener('input', function() {
                descriptionLength.textContent = this.value.length;
            });
            
            // 初始化字符計數
            descriptionLength.textContent = descriptionField.value.length;
        }
        
        // 生成SEO文章功能
        document.getElementById('generateArticleBtn').addEventListener('click', function() {
            const title = document.getElementById('title').value;
            const category = document.getElementById('category_id').value;
            const existingKeywords = document.getElementById('keywords').value;
            
            if (!title) {
                alert('請先輸入文章標題');
                return;
            }
            
            if (!category) {
                alert('請先選擇文章分類');
                return;
            }
            
            // 生成文章內容
            const content = generateArticleContent(title);
            CKEDITOR.instances.content.setData(content);
            
            // 生成SEO描述
            const description = "了解" + title + "的相關知識。本文詳細介紹了更年期女性在" + title + "方面的注意事項和實用建議。";
            document.getElementById('description').value = description;
            descriptionLength.textContent = description.length;
            
            // 只有在沒有關鍵字時才生成新的關鍵字
            if (!existingKeywords.trim()) {
                const keywords = generateKeywords(title);
                document.getElementById('keywords').value = keywords;
            }
            
            // 顯示建議關鍵字
            showSuggestedKeywords(title);
            
            alert('SEO文章已生成！您可以進一步編輯內容。');
        });
        
        // 優化SEO功能
        document.getElementById('optimizeSEOBtn').addEventListener('click', function() {
            const title = document.getElementById('title').value;
            const content = CKEDITOR.instances.content.getData();
            const existingKeywords = document.getElementById('keywords').value;
            
            if (!title) {
                alert('請先輸入文章標題');
                return;
            }
            
            // 優化SEO描述
            const description = optimizeDescription(title, content);
            document.getElementById('description').value = description;
            descriptionLength.textContent = description.length;
            
            // 只有在沒有關鍵字時才生成新的關鍵字
            if (!existingKeywords.trim()) {
                const keywords = generateKeywords(title);
                document.getElementById('keywords').value = keywords;
            }
            
            // 顯示建議關鍵字
            showSuggestedKeywords(title);
            
            alert('SEO優化完成！');
        });
        
        // 生成文章內容的函數
        function generateArticleContent(title) {
            const categoryText = getCategoryText();
            const content = `
<h2>什麼是${title}？</h2>
<p>${title}是更年期女性常見的問題之一。隨著年齡增長和荷爾蒙變化，${title}可能會對日常生活產生影響。了解${title}的成因和應對方法，有助於更年期女性更好地管理自己的健康。</p>

<h2>${title}的常見症狀</h2>
<ul>
    <li><strong>身體症狀：</strong>詳細描述可能出現的身體不適</li>
    <li><strong>情緒變化：</strong>說明可能的心理和情緒反應</li>
    <li><strong>睡眠問題：</strong>介紹對睡眠質量的影響</li>
    <li><strong>生活影響：</strong>解釋對日常生活和工作的可能影響</li>
</ul>

<h2>${title}的成因</h2>
<p>多種因素可能導致${title}，包括：</p>
<ol>
    <li><strong>荷爾蒙變化：</strong>雌激素和黃體酮水平下降對身體的影響</li>
    <li><strong>年齡因素：</strong>隨著年齡增長，身體機能的自然變化</li>
    <li><strong>生活習慣：</strong>飲食、運動、壓力管理等對${title}的影響</li>
    <li><strong>遺傳因素：</strong>家族病史可能起到的作用</li>
</ol>

<h2>改善${title}的方法</h2>
<h3>1. 飲食調整</h3>
<p>合理的飲食對於改善${title}至關重要：</p>
<ul>
    <li>增加富含植物雌激素的食物，如大豆製品</li>
    <li>攝取足夠的鈣質和維生素D以維護骨骼健康</li>
    <li>減少咖啡因和酒精的攝取</li>
    <li>保持均衡飲食，多吃新鮮蔬果</li>
</ul>

<h3>2. 運動健身</h3>
<p>適當的運動有助於緩解${title}：</p>
<ul>
    <li>有氧運動如散步、游泳有助於改善心血管健康</li>
    <li>重量訓練有助於預防骨質疏鬆</li>
    <li>瑜伽和太極有助於放鬆身心</li>
    <li>建議每週至少150分鐘中等強度運動</li>
</ul>

<h3>3. 心理調適</h3>
<p>心理狀態對${title}有重要影響：</p>
<ul>
    <li>學習壓力管理技巧，如深呼吸和冥想</li>
    <li>保持積極的心態和社交活動</li>
    <li>尋求家人和朋友的支持</li>
    <li>必要時尋求專業心理諮詢</li>
</ul>

<h3>4. 生活習慣調整</h3>
<ul>
    <li>保持規律的作息時間</li>
    <li>創造良好的睡眠環境</li>
    <li>戒煙限酒</li>
    <li>定期進行健康檢查</li>
</ul>

<h2>何時需要尋求專業幫助？</h2>
<p>在以下情況下，建議諮詢專業醫師：</p>
<ul>
    <li>症狀嚴重影響日常生活和工作</li>
    <li>自我調適方法無效</li>
    <li>出現抑鬱或焦慮症狀</li>
    <li>有其他健康問題需要專業評估</li>
</ul>

<h2>總結</h2>
<p>${title}是更年期女性常見的問題，但通過合理的調整和專業指導，大多數情況都能得到有效改善。${categoryText}，保持積極的心態，尋求適當的支持，您就能順利度過這個特殊時期。記住，關注自己的健康是一種智慧，也是對自己最好的投資。</p>
            `;
            
            return content;
        }
        
        // 獲取分類相關文本
        function getCategoryText() {
            const categorySelect = document.getElementById('category_id');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption.text;
            
            switch(categoryName) {
                case '更年期知識庫':
                    return '了解更多更年期相關知識';
                case '健康保健':
                    return '關注營養和運動對健康的益處';
                case '心理調適':
                    return '重視心理健康的重要性';
                case '美麗養生':
                    return '追求由內而外的健康美';
                default:
                    return '持續關注健康生活';
            }
        }
        
        // 生成關鍵字
        function generateKeywords(title) {
            const baseKeywords = ["更年期", "女性健康", "保健", "養生", "健康管理"];
            const titleKeywords = title.split(" ");
            return [...titleKeywords, ...baseKeywords].join(", ");
        }
        
        // 顯示建議關鍵字
        function showSuggestedKeywords(title) {
            const suggestedKeywords = [
                title,
                "更年期" + title,
                title + "症狀",
                title + "改善方法",
                "更年期健康管理",
                "女性保健",
                "更年期養生"
            ];
            
            document.getElementById('keywordList').textContent = suggestedKeywords.join(", ");
            document.getElementById('suggestedKeywords').style.display = 'block';
        }
        
        // 優化描述
        function optimizeDescription(title, content) {
            // 從文章標題和內容中提取關鍵信息
            const description = `深入了解${title}的成因、症狀和改善方法。專為更年期女性設計的健康指南，提供實用的${title}管理建議。`;
            
            // 確保描述不超過160字符
            return description.length > 160 ? description.substring(0, 157) + "..." : description;
        }
    </script>
</body>
</html>