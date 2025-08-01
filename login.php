<?php
session_start();

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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (isset($_POST['login'])) {
    // LOGIN
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user['Password']) {
      $_SESSION['user_id'] = $user['User_id'];
      $_SESSION['username'] = $user['Username'];
      header('Location: index.php');
      exit();
    } else {
      $error = 'Invalid username or password';
    }
  } elseif (isset($_POST['signup'])) {
    // SIGNUP
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
      $error = 'Username already taken';
    } else {
      $email = $_POST['email'];
      $stmt = $pdo->prepare("INSERT INTO Users (Username, Password, Email) VALUES (?, ?, ?)");
      $stmt->execute([$username, $password, $email]);
      $_SESSION['user_id'] = $pdo->lastInsertId();
      $_SESSION['username'] = $username;
      header('Location: index.php');
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Sign Up</title>
  <link rel="stylesheet" href="style/aryan.css">
  <style>
    .auth-box {
      width: 300px;
      margin: 100px auto;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #dbdbdb;
      border-radius: 6px;
      text-align: center;
    }
    .auth-box h2 {
      margin-bottom: 15px;
    }
    .auth-box input {
      width: 100%;
      padding: 10px;
      margin: 6px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .auth-box button {
      width: 100%;
      padding: 10px;
      background-color: #3897f0;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }
    .auth-box .error {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="auth-box">
    <h2>Socialgram</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="email" name="email" placeholder="Email (for signup only)">
      <button type="submit" name="login">Login</button>
      <button type="submit" name="signup">Sign Up</button>
    </form>
  </div>
</body>
</html>
