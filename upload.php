<?php
include 'includes/session.inc.php';
include 'includes/connection.inc.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
      $success = '❌ Only JPG and PNG files are allowed.';
    } else {
      $uploadDir = 'uploads/posts/';
      if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

      $filename = 'post_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $filePath = $uploadDir . $filename;

      if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $caption = $_POST['caption'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO Posts (User_id, Image_url, Caption, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $filePath, $caption]);
        $success = '✅ Post uploaded successfully!';
        header('Refresh: 2; URL=profile.php');
      } else {
        $success = '❌ Failed to upload image.';
      }
    }
  } else {
    $success = '❌ Please select an image to upload.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Post</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo"><a style="text-decoration: none;" href="index.php">Socialgram</a></div>
    <div class="icons">
      <a title = "Home" href="index.php"><i class="fas fa-home"></i></a>
      <a title="Explore" href="explore.php"><i class="fas fa-compass"></i></a>
      <a title="Profile" href="profile.php"><i class="fas fa-user-circle"></i></a>
    </div>
  </nav>

  <div class="upload-box">
    <h2>Create a New Post</h2>
    <?php if ($success): ?>
      <div class="message"> <?= htmlspecialchars($success) ?> </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="image" accept="image/*" required>
      <textarea name="caption" rows="3" placeholder="Write a caption..."></textarea>
      <button type="submit">Upload</button>
    </form>
  </div>
</body>
</html>

