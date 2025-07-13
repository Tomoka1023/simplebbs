<?php
require_once 'db.php';
require_once 'header.php';

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff . '秒前';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '分前';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '時間前';
    } elseif ($diff < 172800) {
        return '昨日';
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . '日前';
    } else {
        return date('Y年n月j日', $timestamp); // 古い投稿は日付表示
    }
}


// スレッドID取得
$thread_id = $_GET['thread_id'] ?? null;

if (!$thread_id) {
    header('Location: index.php');
    exit;
}

// スレッド情報取得
$stmt = $pdo->prepare("SELECT * FROM threads WHERE id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    echo "スレッドが見つかりません。";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_SESSION['logged_in'])) {
        echo "<p style='color:red;'>投稿するにはログインが必要です。</p>";
    } else {
        $name = $_POST['name'] ?? '匿名';
        $body = $_POST['body'] ?? '';
        $password = $_POST['password'] ?? null;

        if (!empty($body)) {
            $stmt = $pdo->prepare("INSERT INTO posts (thread_id, name, body, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$thread_id, $name, $body, $password]);
            header("Location: thread.php?thread_id=$thread_id");
            exit;
        }
    }
}


// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "<p style='color:red;'>投稿するにはログインが必要です。</p>";
    } else {
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $body = $_POST['body'] ?? '';
        $password = $_POST['password'] ?? null;
    }
    
    if (!empty($body)) {
        $stmt = $pdo->prepare("INSERT INTO posts (thread_id, user_id, name, body, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$thread_id, $user_id, $name, $body, $password]);
        header("Location: thread.php?thread_id=$thread_id");
        exit;
    }
}

// 投稿一覧取得
$stmt = $pdo->prepare("SELECT * FROM posts WHERE thread_id = ? ORDER BY created_at ASC");
$stmt->execute([$thread_id]);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($thread['title']) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h1><?= htmlspecialchars($thread['title']) ?></h1>
  <a href="index.php">← スレッド一覧に戻る</a>

  <h2>投稿一覧</h2>
  <?php foreach ($posts as $post): ?>
    <div class="post">
      <strong><?= htmlspecialchars($post['name']) ?></strong>：
      <?= nl2br(htmlspecialchars($post['body'])) ?>
      <!-- <div class="timestamp"><?= $post['created_at'] ?></div> -->
      <div class="timestamp"><?= timeAgo($post['created_at']) ?></div>


    <?php
      // ログイン中で、投稿のuser_idと一致した場合のみ削除ボタンを表示
        if (
            isset($_SESSION['user_id']) &&
            isset($post['user_id']) &&
            $post['user_id'] == $_SESSION['user_id']
        ):
    ?>
        <form action="delete.php" method="post" style="display:inline;">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
          <button type="submit">削除</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <h2>新しい投稿</h2>

  <?php if (isset($_SESSION['username'])): ?>
  <p>ログイン中：<?= htmlspecialchars($_SESSION['username']) ?>さん｜<a href="logout.php">ログアウト</a></p>
<?php else: ?>
  <p style="color:red;">※ 投稿・削除にはログインが必要です。<a href="login.php">ログイン</a></p>
<?php endif; ?>

<strong>
  <a href="user.php?id=<?= $post['user_id'] ?>">
    <?= htmlspecialchars($post['name']) ?>
  </a>
</strong>


  <form method="post">
    名前：<input type="text" name="name"><br>
    本文：<br>
    <textarea name="body" rows="5" cols="50"></textarea><br>
    削除用パスワード（任意）：<input type="password" name="password"><br>
    <button type="submit">投稿</button>
  </form>
</div>
</body>
</html>
