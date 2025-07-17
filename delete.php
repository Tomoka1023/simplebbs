<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "ログインが必要です。<a href='login.php'>ログイン</a>";
    exit;
}


require_once 'db.php';
require_once 'header.php';

$post_id = $_POST['post_id'] ?? null;
$thread_id = $_POST['thread_id'] ?? null;

if (!$post_id || !$thread_id) {
    header("Location: index.php");
    exit;
}

// 該当投稿を取得
$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    echo "この投稿は削除できません。<a href='thread.php?thread_id=$thread_id'>戻る</a>";
    exit;
}

// 本人の投稿なら削除
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
header("Location: thread.php?thread_id=$thread_id");
exit;

$error = '';
$step = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_check'])) {
    $password = $_POST['password_check'];

    $stmt = $pdo->prepare("SELECT password FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post && $post['password'] === $password) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        header("Location: thread.php?thread_id=$thread_id");
        exit;
    } else {
        $error = 'パスワードが違います。';
        $step = 2;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="favicon.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <title>投稿削除</title>
</head>
<body>
<div class="container">
  <h1>投稿を削除</h1>
  <a href="thread.php?thread_id=<?= htmlspecialchars($thread_id) ?>">← スレッドに戻る</a>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if ($step === 1 || $step === 2): ?>
    <form method="post">
      <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
      <input type="hidden" name="thread_id" value="<?= htmlspecialchars($thread_id) ?>">
      パスワードを入力してください：<br>
      <input type="password" name="password_check"><br><br>
      <button type="submit">削除する</button>
    </form>
  <?php endif; ?>
  </div>
</body>
</html>
