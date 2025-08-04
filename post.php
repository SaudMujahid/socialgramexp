<?php
include 'includes/session.inc.php';
include 'includes/connection.inc.php';

if (!isset($_GET['post_id'])) {
  header('Location: index.php');
  exit();
}

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];
$back = $_GET['from'] === 'profile' ? 'profile.php' : 'index.php';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
  $comment = trim($_POST['comment_text']);
  if (!empty($comment)) {
    $stmt = $pdo->prepare("INSERT INTO Comments (Post_id, User_id, Text, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$post_id, $user_id, $comment]);
  }
}

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post'])) {
  $check = $pdo->prepare("SELECT * FROM Likes WHERE Post_id = ? AND User_id = ?");
  $check->execute([$post_id, $user_id]);
  if ($check->rowCount() === 0) {
    $likeStmt = $pdo->prepare("INSERT INTO Likes (Post_id, User_id) VALUES (?, ?)");
    $likeStmt->execute([$post_id, $user_id]);
  } else {
    $unlikeStmt = $pdo->prepare("DELETE FROM Likes WHERE Post_id = ? AND User_id = ?");
    $unlikeStmt->execute([$post_id, $user_id]);
  }
}
//update button dynamically 
$hasLiked = $pdo->prepare("SELECT * FROM Likes WHERE Post_id = ? AND User_id = ?");
$hasLiked->execute([$post_id, $user_id]);
$userLiked = $hasLiked->rowCount() > 0;

// Fetch post info with user
$stmt = $pdo->prepare("SELECT Posts.*, Users.Username, Users.Profile_pic FROM Posts JOIN Users ON Posts.User_id = Users.User_id WHERE Posts.Post_id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
  echo "Post not found.";
  exit();
}

// Fetch comments
$commentsStmt = $pdo->prepare("SELECT Comments.Comment_id, Comments.Text, Comments.created_at, Users.Username, Users.Profile_pic, Users.User_id FROM Comments JOIN Users ON Comments.User_id = Users.User_id WHERE Comments.Post_id = ? ORDER BY Comments.created_at ASC");
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
      <h3>@<?= htmlspecialchars($post['Username']) ?> (ID: <?= $post['User_id'] ?>)</h3>
      <p><?= nl2br(htmlspecialchars($post['Caption'])) ?></p>
        <form method="POST" style="margin-top:10px;">
  <button type="submit" name="like_post"
    style="background:#3897f0; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;">
    <?= $userLiked ? '💔 Unlike' : '❤️ Like' ?> (<?= $likeCount ?>)
  </button>
</form>
      <p><em>Posted on <?= date('F j, Y, g:i a', strtotime($post['created_at'])) ?></em></p>
    </div>
    <div class="comments">
      <h4>Comments:</h4>
      <?php if (count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
          <div class="comment" style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <img src="<?= htmlspecialchars($comment['Profile_pic']) ?>" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
            <div>
              <strong>@<?= htmlspecialchars($comment['Username']) ?></strong>: <?= htmlspecialchars($comment['Text']) ?><br>
              <small><?= date('F j, Y, g:i a', strtotime($comment['created_at'])) ?></small>
              <?php if ($comment['User_id'] == $user_id): ?>
                <form method="POST" action="delete_comment.php" style="display:inline;">
                  <input type="hidden" name="comment_id" value="<?= $comment['Comment_id'] ?>">
                  <input type="hidden" name="post_id" value="<?= $post_id ?>">
                  <button type="submit" style="background:none; color:#e63946; border:none; cursor:pointer; font-size:12px;">Delete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No comments yet.</p>
      <?php endif; ?>

      <form method="POST" style="margin-top:20px;">
        <textarea name="comment_text" placeholder="Add a comment..." rows="2" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;"></textarea>
        <button type="submit" style="margin-top:8px; padding:8px 12px; background:#3897f0; color:white; border:none; border-radius:4px; cursor:pointer;">Post Comment</button>
      </form>
    </div>
  </div>
  <div class="back-link">
      <a href="<?= $back ?>">&larr; Back</a>
  </div>
</body>
</html>

