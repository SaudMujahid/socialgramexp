<?php  
include 'includes/session.inc.php'; 
include 'includes/connection.inc.php';  

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Determine profile user ID
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (int)$_SESSION['user_id'];

// Check if viewing own profile
$isOwnProfile = ($user_id === (int)$_SESSION['user_id']);

// Default follow state
$isFollowing = false;

// If not own profile, check follow status
if (!$isOwnProfile) {
    $stmt = $pdo->prepare("SELECT 1 FROM Follow WHERE Follower_id = ? AND Following_id = ?");
    $stmt->execute([$_SESSION['user_id'], $user_id]);
    $isFollowing = $stmt->fetchColumn() ? true : false;

    // Handle follow/unfollow actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['follow']) && !$isFollowing) {
            $pdo->prepare("INSERT INTO Follow (Follower_id, Following_id) VALUES (?, ?)")
                ->execute([$_SESSION['user_id'], $user_id]);
            $isFollowing = true;
        } elseif (isset($_POST['unfollow']) && $isFollowing) {
            $pdo->prepare("DELETE FROM Follow WHERE Follower_id = ? AND Following_id = ?")
                ->execute([$_SESSION['user_id'], $user_id]);
            $isFollowing = false;
        }
    }
}

// Fetch user info (with profile pic)
$userStmt = $pdo->prepare("SELECT Username, Email, Bio, Profile_pic FROM Users WHERE User_id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();

// Count followers
$followerCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Follow WHERE Following_id = ?");
$followerCountStmt->execute([$user_id]);
$followerCount = $followerCountStmt->fetchColumn();

// Count following
$followingCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Follow WHERE Follower_id = ?");
$followingCountStmt->execute([$user_id]);
$followingCount = $followingCountStmt->fetchColumn();

// Count posts
$postStmt = $pdo->prepare("SELECT COUNT(*) FROM Posts WHERE User_id = ?");
$postStmt->execute([$user_id]);
$postCount = $postStmt->fetchColumn();

// Fetch posts
$postsStmt = $pdo->prepare("SELECT Post_id, Image_url FROM Posts WHERE User_id = ?");
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
    <div class="logo"><a style="text-decoration:none;" href="index.php">Socialgram</a></div> 
    <form action="search.php" method="GET" style="margin: 0;">   
      <input type="text" name="q" placeholder="Search" style="padding: 7px 12px; border: 1px solid #dbdbdb; border-radius: 4px; background-color: #efefef; width: 200px;"> 
    </form>     
    <div class="icons">       
      <a href="index.php" title="Home"><i class="fas fa-home"></i></a> 
      <?php if ($isOwnProfile): ?>   
        <a href="upload.php" title="Upload"><i class="fas fa-plus-square"></i></a> 
      <?php endif; ?>       
      <a title="Explore" href="explore.php"><i class="fas fa-compass"></i></a>        
      <a href="profile.php" title="Profile"><i class="fas fa-user-circle"></i></a>     
    </div>   
  </nav>    

  <div class="container">     
    <div class="profile-header">       
      <div class="profile-pic">
        <?php if (!empty($user['Profile_pic'])): ?>
            <img src="<?= htmlspecialchars($user['Profile_pic']) ?>" 
                 alt="Profile Picture" 
                 style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
            <?php else: ?>
                  <span style="font-size: 80px; display:inline-block; width:120px; height:120px; 
                     border-radius:50%; background:#eee; display:flex; 
                     align-items:center; justify-content:center;">
            👤
        </span>        <?php endif; ?>
      </div>       
      <div class="profile-info">         
        <h2><?= htmlspecialchars($user['Username']) ?></h2>         
        <p><strong><?= $postCount ?></strong> posts | 
           <strong><?= $followerCount ?></strong> followers | 
           <strong><?= $followingCount ?></strong> following</p>           
        <p><?= nl2br(htmlspecialchars($user['Bio'])) ?></p>           

        <?php if ($isOwnProfile): ?>         
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
          <a href="post.php?post_id=<?= $post['Post_id'] ?>&from=profile">
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

