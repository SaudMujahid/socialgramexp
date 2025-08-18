-- ==========================
-- DROP TABLES IF EXISTS (for fresh install)
-- ==========================
DROP TABLE IF EXISTS Follow;
DROP TABLE IF EXISTS Likes;
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Posts;
DROP TABLE IF EXISTS Users;

-- ==========================
-- USERS
-- ==========================
CREATE TABLE Users (
    User_id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Bio TEXT DEFAULT NULL,
    Profile_pic TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- POSTS
-- ==========================
CREATE TABLE Posts (
    Post_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Caption TEXT,
    Image_url TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user
        FOREIGN KEY (User_id) REFERENCES Users(User_id)
        ON DELETE CASCADE
);

-- ==========================
-- COMMENTS
-- ==========================
CREATE TABLE Comments (
    Comment_id INT AUTO_INCREMENT PRIMARY KEY,
    Post_id INT NOT NULL,
    User_id INT NOT NULL,
    Text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post
        FOREIGN KEY (Post_id) REFERENCES Posts(Post_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_comments_user
        FOREIGN KEY (User_id) REFERENCES Users(User_id)
        ON DELETE CASCADE
);

-- ==========================
-- LIKES
-- ==========================
CREATE TABLE Likes (
    Like_id INT AUTO_INCREMENT PRIMARY KEY,
    Post_id INT NOT NULL,
    User_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (Post_id, User_id),
    CONSTRAINT fk_likes_post
        FOREIGN KEY (Post_id) REFERENCES Posts(Post_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_likes_user
        FOREIGN KEY (User_id) REFERENCES Users(User_id)
        ON DELETE CASCADE
);

-- ==========================
-- FOLLOW
-- ==========================
CREATE TABLE Follow (
    Follower_id INT NOT NULL,
    Following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (Follower_id, Following_id),
    CONSTRAINT fk_follow_follower
        FOREIGN KEY (Follower_id) REFERENCES Users(User_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_follow_following
        FOREIGN KEY (Following_id) REFERENCES Users(User_id)
        ON DELETE CASCADE
);

