<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>掲示板まさ坊(仮)</title>
  <link rel="stylesheet" href="css/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
  <div class="navbar">
    <a href="index.php">🏠 ホーム</a>
    <?php if (isset($_SESSION['username'])): ?>
      <a href="profile.php">👤 プロフィール</a>
      <a href="logout.php">🔓 ログアウト</a>
    <?php else: ?>
      <a href="login.php">🔑 ログイン</a>
      <a href="signup.php">📝 新規登録</a>
    <?php endif; ?>
  </div>
    </div>
    </body>
    </html>