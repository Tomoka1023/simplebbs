<?php
require_once 'db.php';
require_once 'header.php';

$username = $_SESSION['username'] ?? null;

if (!$username) {
    header('Location: login.php');
    exit;
}

// 現在のユーザー情報を取得
$stmt = $pdo->prepare("SELECT id, bio FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_bio = $_POST['bio'] ?? '';

    // 更新処理
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$new_bio, $user['id']]);

    header("Location: profile.php");
    exit;
}
?>

<h1>プロフィール編集</h1>

<form method="post">
  <label for="bio"><strong>自己紹介：</strong></label><br>
  <textarea name="bio" id="bio" rows="6"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea><br>
  <button type="submit">保存</button>
</form>

</div> <!-- .container -->
</body>
</html>
