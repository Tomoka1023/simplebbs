<?php
require_once 'db.php';
require_once 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'ログインに失敗しました。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="favicon.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <title>ログイン</title>
</head>
<body>
<div class="container">
  <h1>ログイン</h1>
  <?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post">
    ユーザー名：<input type="text" name="username"><br>
    パスワード：<input type="password" name="password"><br><br>
    <button type="submit">ログイン</button>
  </form>
  <p>→ <a href="signup.php">新規登録はこちら</a></p>
  </div>
</body>
</html>
