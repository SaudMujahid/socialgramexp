<?php
include 'includes/session.inc.php';
include 'includes/connection.inc.php';

if (!isset($_GET['post_id'])) {
  header('Location: index.php');
  exit();
}

$post_id = $_GET['post_id'];

// Fetch post info with user
$stmt = $pdo->prepare("SELECT Posts.*, Users.Username, Users.Profile_pic FROM Posts JOIN Users ON Posts.User_id = Users.User_id WHERE Posts.Post_id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
  echo "Post not found.";
  exit();
}

// Fetch comments
$commentsStmt = $pdo->prepare("SELECT Comments.Text, Comments.created_at, Users.Username FROM Comments JOIN Users ON Comments.User_id = Users.User_id WHERE Comments.Post_id = ? ORDER BY Comments.created_at ASC");
$commentsStmt->execute([$post_id]);
$comments = $commentsStmt->fetchAll();

// Fetch like count
$likeCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Likes WHERE Post_id = ?");
$likeCountStmt->execute([$post_id]);
$likeCount = $likeCountStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post - Socialgram</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="post-detail">
    <img src="<?= htmlspecialchars($post['Image_url']) ?>" alt="Post Image">
    <div class="info">
      <h3>@<?= htmlspecialchars($post['Username']) ?></h3>
      <p><?= nl2br(htmlspecialchars($post['Caption'])) ?></p>
      <p><strong><?= $likeCount ?></strong> likes</p>
      <p><em>Posted on <?= date('F j, Y, g:i a', strtotime($post['created_at'])) ?></em></p>
    </div>
    <div class="comments">
      <h4>Comments:</h4>
      <?php if (count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
          <div class="comment">
            <strong>@<?= htmlspecialchars($comment['Username']) ?></strong>: <?= htmlspecialchars($comment['Text']) ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No comments yet.</p>
      <?php endif; ?>
    </div>
  </div>
  <div class="back-link">
    <a href="javascript:history.back()">&larr; Back</a>
  </div>
</body>
</html>

