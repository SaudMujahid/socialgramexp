<?php

include 'includes/session.inc.php';
include 'includes/connection.inc.php';

//Check current user or searched user
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];


if (!$isOwnProfile) {
  // Check if current user is following this user
  $stmt = $pdo->prepare("SELECT * FROM Follow WHERE Follower_id = ? AND Following_id = ?");
  $stmt->execute([$_SESSION['user_id'], $user_id]);
  $isFollowing = $stmt->rowCount() > 0;

  // Handle follow/unfollow actions
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow'])) {
      $pdo->prepare("INSERT INTO Follow (Follower_id, Following_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $user_id]);
      $isFollowing = true;
    } elseif (isset($_POST['unfollow'])) {
      $pdo->prepare("DELETE FROM Follow WHERE Follower_id = ? AND Following_id = ?")->execute([$_SESSION['user_id'], $user_id]);
      $isFollowing = false;
    }
  }
}

// Fetch user info
$userStmt = $pdo->prepare("SELECT Username, Email, Bio FROM Users WHERE User_id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();

// Count stats
//show followers and following count
// Count followers (users who follow this user)
$followerCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Follow WHERE Following_id = ?");
$followerCountStmt->execute([$user_id]);
$followerCount = $followerCountStmt->fetchColumn();

// Count following (users this user follows)
$followingCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Follow WHERE Follower_id = ?");
$followingCountStmt->execute([$user_id]);
$followingCount = $followingCountStmt->fetchColumn();

//follow and unfollow
$isOwnProfile = ($user_id == $_SESSION['user_id']);
$isFollowing = false;

// Fetch user posts
$postsStmt = $pdo->prepare("SELECT Post_id, Image_url FROM Posts WHERE User_id = ?");
$postsStmt->execute([$user_id]);
$userPosts = $postsStmt->fetchAll();
// Count user posts
$postStmt = $pdo->prepare("SELECT COUNT(*) FROM Posts WHERE User_id = ?");
$postStmt->execute([$user_id]);
$postCount = $postStmt->fetchColumn();

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
    <div class="logo"><a style="text-decoration:none;" href="index.php" >Socialgram</a></div>
<form action="search.php" method="GET" style="margin: 0;">
  <input type="text" name="q" placeholder="Search" style="padding: 7px 12px; border: 1px solid #dbdbdb; border-radius: 4px; background-color: #efefef; width: 200px;">
</form>
    <div class="icons">
      <a href="index.php" title ="Home"><i class="fas fa-home"></i></a>
<?php if ($user_id == $_SESSION['user_id']): ?>
  <a href="upload.php" title="Upload"><i class="fas fa-plus-square"></i></a>
<?php endif; ?>
      <a title="Explore" href="explore.php"><i class="fas fa-compass"></i></a>

      <a href="profile.php" title="Profile"><i class="fas fa-user-circle"></i></a>
    </div>
  </nav>

  <div class="container">
    <div class="profile-header">
      <div class="profile-pic">👤</div>
      <div class="profile-info">
        <h2><?= htmlspecialchars($user['Username']) ?></h2>
        <p><strong><?= $postCount ?></strong> posts | <strong><?= $followerCount ?></strong> followers | <strong><?= $followingCount ?></strong> following</p>
          <p><?= nl2br(htmlspecialchars($user['Bio'])) ?></p>
          <?php if($user_id == $_SESSION['user_id']): ?>
        <a href="settings.php" class="settings-link">⚙️ Settings</a>
          <?php endif; ?>
            <?php if (!$isOwnProfile): ?>
    <form method="POST">
      <?php if ($isFollowing): ?>
        <button type="submit" name="unfollow" class="follow-btn unfollow">Unfollow</button>
      <?php else: ?>
        <button type="submit" name="follow" class="follow-btn">Follow</button>
      <?php endif; ?>
    </form>
  <?php endif; ?>
      </div>
    </div>

    <div class="profile-posts">
      <?php if (count($userPosts) > 0): ?>
        <?php foreach ($userPosts as $post): ?>
  <a href="post.php?post_id=<?= $post['Post_id'] ?>">
  <img src="<?= htmlspecialchars($post['Image_url']) ?>" />
</a>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; padding: 20px;">Haven't uploaded any posts yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
