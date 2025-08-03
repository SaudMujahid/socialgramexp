<?php
include 'includes/session.inc.php';
include 'includes/connection.inc.php';

$searchResults = [];
if (isset($_GET['q'])) {
  $search = trim($_GET['q']);
  if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT User_id, Username FROM Users WHERE Username LIKE ?");
    $stmt->execute(["%$search%"]);
    $searchResults = $stmt->fetchAll();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search - Socialgram</title>
  <link rel="stylesheet" href="style/aryan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo"><a href="index.php">Socialgram</a></div>
    <form method="GET" action="search.php">
      <input type="text" name="q" placeholder="Search users..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </form>
    <div class="icons">
      <a href="index.php"><i class="fas fa-home"></i></a>
      <a href="upload.php"><i class="fas fa-plus-square"></i></a>
      <a href="profile.php"><i class="fas fa-user-circle"></i></a>
    </div>
  </nav>

  <div class="container">
    <h2 style="margin: 20px 0;">Search Results:</h2>
    <?php if (!empty($searchResults)): ?>
      <ul style="list-style: none; padding: 0;">
        <?php foreach ($searchResults as $user): ?>
          <li style="padding: 10px 0; border-bottom: 1px solid #ccc;">
            <a href="profile.php?user_id=<?= $user['User_id'] ?>" style="color: #3897f0; text-decoration: none; font-weight: bold;">
              @<?= htmlspecialchars($user['Username']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No users found.</p>
    <?php endif; ?>
  </div>
</body>
</html>

