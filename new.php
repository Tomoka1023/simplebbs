<?php
require_once 'db.php';
require_once 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $name = trim($_POST['name'] ?? '匿名');
    $body = trim($_POST['body'] ?? '');

    if ($title === '' || $body === '') {
        $error = 'タイトルと本文は必須です。';
    } else {
        try {
            // トランザクション開始
            $pdo->beginTransaction();

            // スレッドを作成
            $stmt = $pdo->prepare("INSERT INTO threads (title) VALUES (?)");
            $stmt->execute([$title]);
            $thread_id = $pdo->lastInsertId();

            // 最初の投稿を作成
            $stmt = $pdo->prepare("INSERT INTO posts (thread_id, name, body) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $name, $body]);

            // 完了
            $pdo->commit();

            header("Location: thread.php?thread_id=$thread_id");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'スレッドの作成に失敗しました。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新しいスレッドを作成</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h1>新しいスレッドを作成</h1>
  <a href="index.php">← スレッド一覧に戻る</a>

  <?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>スレッドタイトル：<br>
      <input type="text" name="title" size="60">
    </label><br><br>

    <label>投稿者名（任意）：<br>
      <input type="text" name="name">
    </label><br><br>

    <label>本文：<br>
      <textarea name="body" rows="6" cols="60"></textarea>
    </label><br><br>

    <button type="submit">スレッドを作成</button>
  </form>
  </div>
</body>
</html>
