<?php
// 資料庫連線設定
$servername = "localhost";
$username = "health_user";
$password = "health_pass123";
$dbname = "health_consultation";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定字符集
$conn->set_charset("utf8");

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得表單資料並進行安全性處理
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    $age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
    $health_concern = isset($_POST['health_concern']) ? $conn->real_escape_string($_POST['health_concern']) : '';
    
    // 驗證必填欄位
    if (empty($name) || empty($phone) || empty($health_concern)) {
        echo "請填寫所有必填欄位";
        exit;
    }
    
    // 準備 SQL 語句
    $sql = "INSERT INTO consultation_form (name, email, phone, age, gender, health_concern) 
            VALUES ('$name', '$email', '$phone', $age, '$gender', '$health_concern')";
    
    // 執行 SQL 語句
    if ($conn->query($sql) === TRUE) {
        echo "諮詢申請已成功提交！我們將盡快與您聯繫。";
    } else {
        echo "錯誤: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "無效的請求";
}

// 關閉連線
$conn->close();
?>
