<?php
// Database connection
include 'includes/connection.inc.php';
// Session check
include 'includes/session.inc.php';

$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch posts ONLY from followed users (and self)
$sql = "
    SELECT 
        Posts.Post_id,
        Posts.Caption,
        Posts.Image_url,
        Users.Username,
        Users.Profile_pic,
        COUNT(DISTINCT Likes.Like_id) AS LikeCount,
        GROUP_CONCAT(CONCAT(Comments.Text, '|||', CommentUsers.Username, '|||', CommentUsers.Profile_pic) SEPARATOR '||') AS CommentList
    FROM Posts
    JOIN Users ON Posts.User_id = Users.User_id
    LEFT JOIN Likes ON Posts.Post_id = Likes.Post_id
    LEFT JOIN Comments ON Posts.Post_id = Comments.Post_id
    LEFT JOIN Users AS CommentUsers ON Comments.User_id = CommentUsers.User_id
    WHERE Posts.User_id = :user_id OR Posts.User_id IN (
        SELECT Following_id FROM Follow WHERE Follower_id = :user_id
    )
    GROUP BY Posts.Post_id
    ORDER BY Posts.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instagram Clone</title>
    <link rel="stylesheet" href="style/aryan.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Inline tweaks for username + caption row */
        .post-user-row {
            display: flex;
            align-items: center;
            padding: 10px;
        }

        .post-user-row img.post-profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .post-user-row .username-caption {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
        }

        .post-user-row .username-caption strong {
            margin-right: 5px;
        }

        .post p.caption {
            margin: 5px 0 0 0;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo"><a style="text-decoration: none;" href="index.php">Socialgram</a></div>
    <form action="search.php" method="GET" style="margin: 0;">
        <input type="text" name="q" placeholder="Search">
    </form>
    <div class="icons">
        <a title="Home" href="index.php"><i class="fas fa-home"></i></a>
        <a title="Upload" href="upload.php"><i class="fas fa-plus-square"></i></a>
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

                <!-- Post Image -->
                <a href="post.php?post_id=<?= $post['Post_id'] ?>&from=index">
                    <img src="<?= htmlspecialchars($post['Image_url']) ?>" alt="Post Image">
                </a>

                <!-- Profile picture + username + caption in a row -->
                <div class="post-user-row">
                    <?php if (!empty($post['Profile_pic'])): ?>
                        <img src="<?= htmlspecialchars($post['Profile_pic']) ?>" alt="Profile Picture" class="post-profile-pic">
                    <?php else: ?>
                        <span class="default-icon">👤</span>
                    <?php endif; ?>
                    <div class="username-caption">
                        <strong><?= htmlspecialchars($post['Username']) ?></strong>
                        <?php if (!empty($post['Caption'])): ?>
                            <span class="caption"><?= htmlspecialchars($post['Caption']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Post Icons -->
                <div class="post-icons">
                    <i class="far fa-heart"></i> <?= $post['LikeCount'] ?> Likes
                    <i class="far fa-comment"></i>
                    <i class="far fa-paper-plane"></i>
                </div>

                <!-- Comments -->
                <?php
                $comments = explode('||', $post['CommentList']);
                foreach ($comments as $c):
                    if (empty($c)) continue;
                    list($text, $commentUser, $commentPic) = explode('|||', $c);
                ?>
                    <div class="comment">
                        <?php if (!empty($commentPic)): ?>
                            <img src="<?= htmlspecialchars($commentPic) ?>" alt="Commenter Pic" class="comment-profile-pic">
                        <?php else: ?>
                            <span class="default-icon small">👤</span>
                        <?php endif; ?>
                        <p><strong><?= htmlspecialchars($commentUser) ?></strong> <?= htmlspecialchars($text) ?></p>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

