<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$host = 'localhost';
$db   = 'socialgram';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die('Database connection failed: ' . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$userStmt = $pdo->prepare("SELECT Username, Email FROM Users WHERE User_id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();

// Count stats
$postCount = $pdo->query("SELECT COUNT(*) FROM Posts WHERE User_id = $user_id")->fetchColumn();
$followerCount = 150; // placeholder
$followingCount = 180; // placeholder

// Fetch user posts
$postsStmt = $pdo->prepare("SELECT Image_url FROM Posts WHERE User_id = ?");
$postsStmt->execute([$user_id]);
$userPosts = $postsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile - Instagram Clone</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo"><a href="index.php">Socialgram</a></div>
    <input type="text" placeholder="Search">
    <div class="icons">
      <a href="index.php"><i class="fas fa-home"></i></a>
      <a href="messages.php"><i class="fas fa-paper-plane"></i></a>
      <a href="upload.php"><i class="fas fa-plus-square"></i></a>
      <a href="explore.php"><i class="fas fa-compass"></i></a>
      <a href="profile.php"><i class="fas fa-user-circle"></i></a>
    </div>
  </nav>

  <div class="container">
    <div class="profile-header">
      <div class="profile-pic">👤</div>
      <div class="profile-info">
        <h2><?= htmlspecialchars($user['Username']) ?></h2>
        <p><strong><?= $postCount ?></strong> posts | <strong><?= $followerCount ?></strong> followers | <strong><?= $followingCount ?></strong> following</p>
        <p>👋 Hello! This is my bio.</p>
        <a href="settings.php" class="settings-link">⚙️ Settings</a>
      </div>
    </div>

    <div class="profile-posts">
      <?php if (count($userPosts) > 0): ?>
        <?php foreach ($userPosts as $post): ?>
          <img src="<?= htmlspecialchars($post['Image_url']) ?>" />
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; padding: 20px;">You haven't uploaded any posts yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
