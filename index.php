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


// スレッド一覧取得
$stmt = $pdo->query("SELECT * FROM threads ORDER BY created_at DESC");
$threads = $stmt->fetchAll();
?>
<div class="container">
  <h1>掲示板まさ坊(仮)へようこそ</h1>
  <?php if (isset($_SESSION['username'])): ?>
    <p>こんにちは、<?= htmlspecialchars($_SESSION['username']) ?> さん！</p>
  <?php else: ?>
    <p>投稿するにはログインしてください。</p>
  <?php endif; ?>
  <a href="new.php"><button>＋ 新しいスレッドを作成</button></a>
  <?php foreach ($threads as $thread): ?>
      <div class="thread-card">
        <a href="thread.php?thread_id=<?= $thread['id'] ?>">
          <h2><?= htmlspecialchars($thread['title']) ?></h2>
        </a>
        <!-- <div class="timestamp">作成日時：<?= $thread['created_at'] ?></div> -->
        <div class="timestamp">作成：<?= timeAgo($thread['created_at']) ?></div>

      </div>
    <?php endforeach; ?>
  </div>
  </body>
  </html>
