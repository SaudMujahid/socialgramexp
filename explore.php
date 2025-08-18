<?php
include 'includes/session.inc.php';
include 'includes/connection.inc.php';

// Fetch all posts with user info
$postsStmt = $pdo->query("SELECT Post_id, Image_url, Caption, Users.Username, Users.Profile_pic FROM Posts JOIN Users ON Posts.User_id = Users.User_id ORDER BY Posts.created_at DESC");
$posts = $postsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Explore - Socialgram</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo"><a style="text-decoration: none;" href="index.php">Socialgram</a></div>
    <form action="search.php" method="GET">
      <input type="text" name="q" placeholder="Search">
    </form>
    <div class="icons">
      <a title="Home" href="index.php"><i class="fas fa-home"></i></a>
      <a title="Upload" href="upload.php"><i class="fas fa-plus-square"></i></a>
      <a title="Explore" href="explore.php"><i class="fas fa-compass"></i></a>
      <a title="Profile" href="profile.php"><i class="fas fa-user-circle"></i></a>
    </div>
  </nav>

  <div class="container">
    <div class="profile-posts">
      <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
          <a href="post.php?post_id=<?= $post['Post_id'] ?>&from=explore">
            <img src="<?= htmlspecialchars($post['Image_url']) ?>" alt="Post by <?= htmlspecialchars($post['Username']) ?>">
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; padding: 20px;">No posts found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

