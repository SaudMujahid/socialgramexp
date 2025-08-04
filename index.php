<?php
// Database connection
include 'includes/connection.inc.php';
//session check
include 'includes/session.inc.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Fetch posts with user, likes, comments
$sql = "
    SELECT 
        Posts.Post_id,
        Posts.Caption,
        Posts.Image_url,
        Users.Username,
        COUNT(DISTINCT Likes.Like_id) AS LikeCount,
        GROUP_CONCAT(CONCAT(Comments.Text, '|||', CommentUsers.Username) SEPARATOR '||') AS CommentList
    FROM Posts
    JOIN Users ON Posts.User_id = Users.User_id
    LEFT JOIN Likes ON Posts.Post_id = Likes.Post_id
    LEFT JOIN Comments ON Posts.Post_id = Comments.Post_id
    LEFT JOIN Users AS CommentUsers ON Comments.User_id = CommentUsers.User_id
    GROUP BY Posts.Post_id
    ORDER BY Posts.created_at DESC
";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instagram Clone</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="logo"><a style="text-decoration: none;" href="index.php">Socialgram</a></div>
<form action="search.php" method="GET" style="margin: 0;">
  <input type="text" name="q" placeholder="Search" style="padding: 7px 12px; border: 1px solid #dbdbdb; border-radius: 4px; background-color: #efefef; width: 200px;">
</form>
    <div class="icons">
      <a title="Home" href="index.php"><i class="fas fa-home"></i></a>
      <a title="Upload"  href="upload.php"><i class="fas fa-plus-square"></i></a>
      <a title="Explore" href="explore.php"><i class="fas fa-compass"></i></a>
      <a title="Profile" href="<?= $isLoggedIn ? 'profile.php' : 'login.php' ?>">
        <i class="fas fa-user-circle"></i>
      </a>

    </div>
  </nav>

  <div class="container">
    <div class="posts">
      <?php foreach ($posts as $post): ?>
        <div class="post">
          <div class="post-header"><span>👤 <?= htmlspecialchars($post['Username']) ?></span></div>
          <a href="post.php?post_id=<?= $post['Post_id'] ?>">
  <img src="<?= htmlspecialchars($post['Image_url']) ?>" />
</a>

          <div class="post-icons">
            <i class="far fa-heart"></i> <?= $post['LikeCount'] ?> Likes
            <i class="far fa-comment"></i>
            <i class="far fa-paper-plane"></i>
          </div>
          <p><strong><?= htmlspecialchars($post['Username']) ?></strong> <?= htmlspecialchars($post['Caption']) ?></p>

          <?php if (!empty($post['CommentList'])): ?>
            <div class="comments">
              <?php 
                $comments = explode('||', $post['CommentList']);
                foreach ($comments as $c): 
                  list($text, $commentUser) = explode('|||', $c);
              ?>
                <p><strong><?= htmlspecialchars($commentUser) ?></strong> <?= htmlspecialchars($text) ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
