# Socialgram – A Simple Instagram Clone

**Socialgram** is a lightweight Instagram-inspired web application built using **HTML, CSS, and PHP**. It’s designed as a **DBMS-focused project** that demonstrates relational databases, foreign keys, and cascading deletes in action. Users can post images, comment, like, follow, and explore other profiles—all in a simple, easy-to-understand interface.

---

## Features

* **User Authentication**
  Sign up, log in, and manage your profile.

* **Post Images with Captions**
  Upload images with optional captions. Posts are displayed in a feed and on user profiles.

* **Likes & Comments**
  Users can like/unlike posts and leave comments. Comments and likes update dynamically.

* **Follow System**
  Follow and unfollow other users. Profiles display follower/following counts.

* **Profile Management**
  Update your bio and profile picture. Option to delete your account safely (cascading deletes handled automatically).

* **Explore Page**
  View posts from all users.

* **Responsive Layout**
  Grid-based post display and user-friendly profile layouts.

---

## Technology Stack

* **Frontend:** HTML, CSS (custom styling)
* **Backend:** PHP 8+
* **Database:** MySQL / MariaDB
* **Server:** XAMPP / LAMP stack (any PHP-enabled server)

---

## Getting Started

Follow these steps to run Socialgram locally:

### 1. Clone or Download the Project

```bash
git clone https://github.com/SaudMujahid/socialgram
```

Or download the ZIP and extract it to your server directory (`htdocs` for XAMPP).

### 2. Set Up the Database

1. Open **phpMyAdmin** (or your MySQL client).
2. Create a new database, e.g., `socialgram`.
3. Import the provided `db.sql` file into the database.

```sql
-- Example:
-- File: db.sql
```

This will create all tables with proper **foreign keys** and **ON DELETE CASCADE** rules.

### 3. Configure Database Connection

Edit `includes/connection.inc.php` with your database credentials:

```php
$host = 'localhost';
$db   = 'socialgram';
$user = 'root'; // your MySQL username
$pass = ''; // your MySQL password
$charset = 'utf8mb4';
```

### 4. Start the Server

* **XAMPP:** Start Apache and MySQL.
* **Navigate:** Open your browser and go to `http://localhost/socialgram/`.

---

## Usage

1. **Sign Up:** Create a new account.
2. **Login:** Access your feed.
3. **Post Images:** Upload posts with captions.
4. **Interact:** Like, comment, and follow other users.
5. **Profile:** Update bio, profile picture, or delete your account.
6. **Explore:** See all posts from other users.

> **Note:** All actions are linked to the database to demonstrate **DBMS concepts** like relational keys, cascading deletes, and unique constraints.

---

## Project Structure

```
socialgram/
├── index.php          # Homepage feed
├── explore.php        # Explore posts from all users
├── profile.php        # User profiles
├── post.php           # Single post view
├── upload.php         # Upload images
├── settings.php       # Profile management & account deletion
├── includes/
│   ├── connection.inc.php
│   └── session.inc.php
├── style/
│   └── aryan.css      # CSS styling
├── uploads/
│   └── profile_pics/  # Uploaded profile pictures
└── db.sql             # Database schema with cascading rules
```

---

## Notes

* Designed as a **learning project**, not a production-ready social network.
* Focuses on **database operations** and demonstrating relationships in **MySQL**.
* Images uploaded are stored in the `uploads/` folder.
* Default user profile icon appears if no profile picture is uploaded.

---

## License

This project is **open source** for educational purposes.
