<?php
// 連接資料庫
function connectDatabase() {
    $servername = "localhost";
    $username = "nmnihealth";
    $password = "health2025";
    $dbname = "nmnihealth";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("連接失敗: " . $e->getMessage());
    }
}

// 建立文章相關資料表
function createArticleTables() {
    $pdo = connectDatabase();
    
    try {
        // 建立categories資料表
        $sql = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        
        // 建立tags資料表
        $sql = "CREATE TABLE IF NOT EXISTS tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            slug VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        
        // 建立articles資料表
        $sql = "CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content TEXT NOT NULL,
            excerpt TEXT,
            keywords TEXT,
            description TEXT,
            featured_image VARCHAR(255),
            affiliate_links TEXT,
            member_benefits TEXT,
            status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft',
            category_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            published_at TIMESTAMP NULL,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )";
        $pdo->exec($sql);
        
        // 建立article_tags資料表
        $sql = "CREATE TABLE IF NOT EXISTS article_tags (
            article_id INT NOT NULL,
            tag_id INT NOT NULL,
            PRIMARY KEY (article_id, tag_id),
            FOREIGN KEY (article_id) REFERENCES articles(id),
            FOREIGN KEY (tag_id) REFERENCES tags(id)
        )";
        $pdo->exec($sql);
        
        // 插入初始分類
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, description) VALUES (?, ?, ?)");
        
        $categories = [
            ["更年期知識庫", "knowledge", "關於更年期的基礎知識、症狀解析和醫學新知"],
            ["健康保健", "health", "更年期女性的營養、運動和預防醫學建議"],
            ["心理調適", "psychology", "情緒管理、壓力舒緩和人際關係維護"],
            ["美麗養生", "beauty", "肌膚保養、體態管理和個人風格建立"]
        ];
        
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
        
        echo "文章管理資料表建立成功！";
        
    } catch(PDOException $e) {
        echo "建立資料表失敗: " . $e->getMessage();
    }
}

// 生成SEO友善的URL別名
function generateSlug($text) {
    // 將非字母數字字符轉換為連字符
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
    // 轉換為小寫
    $text = strtolower($text);
    
    // 移除不必要的字符
    $text = preg_replace('~[^-\w]+~', '', $text);
    
    // 移除開頭和結尾的連字符
    $text = trim($text, '-');
    
    // 移除重複的連字符
    $text = preg_replace('~-+~', '-', $text);
    
    // 如果結果為空，使用隨機字符串
    if (empty($text)) {
        return 'n-a-' . time();
    }
    
    return $text;
}

// 創建或更新文章
function saveArticle($article_data) {
    $pdo = connectDatabase();
    
    try {
        $pdo->beginTransaction();
        
        // 生成slug
        $slug = generateSlug($article_data['title']);
        
        // 檢查slug是否已存在，如果存在則添加時間戳
        $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $article_data['id'] ?? 0]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        if (isset($article_data['id']) && $article_data['id']) {
            // 更新文章
            $sql = "UPDATE articles SET 
                    title = ?, slug = ?, content = ?, excerpt = ?, keywords = ?, 
                    description = ?, featured_image = ?, affiliate_links = ?, member_benefits = ?, status = ?, category_id = ?,
                    published_at = ?
                    WHERE id = ?";
            $params = [
                $article_data['title'], $slug, $article_data['content'], $article_data['excerpt'],
                $article_data['keywords'], $article_data['description'], $article_data['featured_image'],
                $article_data['affiliate_links'], $article_data['member_benefits'], $article_data['status'], $article_data['category_id'],
                $article_data['status'] === 'published' ? ($article_data['published_at'] ?? date('Y-m-d H:i:s')) : null,
                $article_data['id']
            ];
        } else {
            // 創建新文章
            $sql = "INSERT INTO articles (title, slug, content, excerpt, keywords, description, 
                    featured_image, affiliate_links, member_benefits, status, category_id, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $article_data['title'], $slug, $article_data['content'], $article_data['excerpt'],
                $article_data['keywords'], $article_data['description'], $article_data['featured_image'],
                $article_data['affiliate_links'], $article_data['member_benefits'], $article_data['status'], $article_data['category_id'],
                $article_data['status'] === 'published' ? date('Y-m-d H:i:s') : null
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $article_id = isset($article_data['id']) && $article_data['id'] ? $article_data['id'] : $pdo->lastInsertId();
        
        $pdo->commit();
        return $article_id;
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

// 獲取所有文章
function getArticles($status = null) {
    $pdo = connectDatabase();
    
    $sql = "SELECT a.*, c.name as category_name FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id";
    
    $params = [];
    if ($status) {
        $sql .= " WHERE a.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY a.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 獲取單篇文章
function getArticle($id) {
    $pdo = connectDatabase();
    
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 獲取所有分類
function getCategories() {
    $pdo = connectDatabase();
    
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 刪除文章
function deleteArticle($id) {
    $pdo = connectDatabase();
    
    $pdo->beginTransaction();
    
    try {
        // 刪除文章標籤關聯
        $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
        $stmt->execute([$id]);
        
        // 刪除文章
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}
?>