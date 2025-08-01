<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/session.inc.php';

include 'includes/connection.inc.php';

$user_id = $_SESSION['user_id'];
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Update bio
  if (isset($_POST['update_bio'])) {
    $bio = $_POST['bio'];
    $stmt = $pdo->prepare("UPDATE Users SET Bio = ? WHERE User_id = ?");
    $stmt->execute([$bio, $user_id]);
  }

  // Update profile picture
  if (isset($_POST['update_pic']) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    if (!in_array($ext, $allowed)) die("Error: Only JPG/PNG allowed!");

    // Set upload dir
    $uploadDir = 'uploads/profile_pics/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    // Generate filename
    $filename = 'user_' . $user_id . '.' . $ext;
    $filePath = $uploadDir . $filename;

    // Move file and update DB
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filePath)) {
      $stmt = $pdo->prepare("UPDATE Users SET Profile_pic = ? WHERE User_id = ?");
      $stmt->execute([$filePath, $user_id]);
      echo "Profile picture updated!";
    }
}  // Delete account
  if (isset($_POST['delete_account'])) {
    $pdo->prepare("DELETE FROM Comments WHERE User_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM Likes WHERE User_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM Posts WHERE User_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM Users WHERE User_id = ?")->execute([$user_id]);
    session_destroy();
    header('Location: login.php');
    exit();
  }
}

// Fetch user info
$stmt = $pdo->prepare("SELECT Bio FROM Users WHERE User_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings - Socialgram</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<body>

<nav class="navbar">
  <div class="logo"><a>Socialgram</a></div>
    <div class="icons">
    <a href="index.php" title="Homepage"><i class="fas fa-home"></i></a>
    <a href="profile.php" title="Back to Profile"><i class="fas fa-times-circle"></i></a>
    <a href="logout.php" title="Log Out"><i class="fas fa-sign-out-alt"></i></a>
  </div>
</nav>


  <div class="container">
    <div class="settings-box">
      <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST">
        <label for="bio">Update Bio</label>
        <textarea name="bio" rows="3" placeholder="Enter your new bio..."><?= htmlspecialchars($user['Bio'] ?? '') ?></textarea>
        <button type="submit" name="update_bio">Update Bio</button>
      </form>

      <form method="POST" enctype="multipart/form-data">
        <label for="profile_pic">Change Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">
        <button type="submit" name="update_pic">Upload Picture</button>
      </form>

      <form method="POST">
        <button type="submit" name="delete_account" class="danger">Delete Account</button>
      </form>
    </div>
  </div>
</body>
</html>
