<?php
$host = 'localhost'; // エックスサーバーでは常に "localhost"
$dbname = 'xs279861_masabbs'; // 例: xs123456_bbs
$user = 'xs279861_masabou';      // 例: xs123456_user
$password = 'masabouadmin'; // あなたが設定したDBパスワード

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('データベース接続エラー: ' . $e->getMessage());
}
?>
