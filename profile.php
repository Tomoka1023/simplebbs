<?php
require_once 'db.php';
require_once 'header.php';

// ユーザー情報取得
$username = $_SESSION['username'] ?? null;

if (!$username) {
    header('Location: login.php');
    exit;
}

// ユーザー情報をDBから取得
$stmt = $pdo->prepare("SELECT id, created_at, bio FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// 投稿数取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt->execute([$user['id']]);
$post_count = $stmt->fetchColumn();

// 最近の投稿5件
$stmt = $pdo->prepare("
    SELECT posts.*, threads.title AS thread_title
    FROM posts
    JOIN threads ON posts.thread_id = threads.id
    WHERE posts.user_id = ?
    ORDER BY posts.created_at DESC
    LIMIT 5
");
$stmt->execute([$user['id']]);
$recent_posts = $stmt->fetchAll();
?>
<div class="container">
<h1>プロフィール</h1>

<div class="profile-box">
  <p><strong>👤 ユーザー名：</strong><?= htmlspecialchars($username) ?></p>
  <p><strong>📅 登録日：</strong><?= date('Y年n月j日', strtotime($user['created_at'])) ?></p>
  <p><strong>📝 投稿数：</strong><?= $post_count ?> 件</p>
  <p><strong>🗒️ 自己紹介：</strong></p>
<p><?= nl2br(htmlspecialchars($user['bio'] ?? '（未設定）')) ?></p>
<a href="edit_profile.php"><button>プロフィールを編集</button></a>

</div>

<h2>最近の投稿</h2>
<ul class="recent-posts">
  <?php foreach ($recent_posts as $post): ?>
    <li>
      <a href="thread.php?thread_id=<?= $post['thread_id'] ?>">
        <?= htmlspecialchars($post['thread_title']) ?>
      </a>（<?= date('n月j日 H:i', strtotime($post['created_at'])) ?>）
    </li>
  <?php endforeach; ?>
</ul>

</div> <!-- .container -->
</body>
</html>
