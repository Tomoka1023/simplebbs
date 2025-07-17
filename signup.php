<?php
require_once 'db.php';
require_once 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードは必須です。';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = 'ユーザー名が既に使われています。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="favicon.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <title>ユーザー登録</title>
</head>
<body>
<div class="container">
  <h1>ユーザー登録</h1>
  <?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post">
    ユーザー名：<input type="text" name="username"><br>
    パスワード：<input type="password" name="password"><br><br>
    <button type="submit">登録</button>
  </form>
  <p>→ <a href="login.php">ログインはこちら</a></p>
  </div>
</body>
</html>
