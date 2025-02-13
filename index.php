<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "blog";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User Registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    $conn->query($query);
}

// User Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
        }
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}

// Create Post
if (isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $conn->query("INSERT INTO posts (title, content) VALUES ('$title', '$content')");
}

// Update Post
if (isset($_POST['update_post'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $conn->query("UPDATE posts SET title='$title', content='$content' WHERE id=$id");
}

// Delete Post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM posts WHERE id=$id");
}

// Fetch Posts
$posts = $conn->query("SELECT * FROM posts");
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP CRUD with Auth</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
    </form>
    <h2>Login</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <?php if (isset($_SESSION['user'])): ?>
        <h2>Welcome, <?= $_SESSION['user'] ?></h2>
        <a href="?logout=1">Logout</a>
    <?php endif; ?>
    <h2>Create Post</h2>
    <form method="post">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="content" placeholder="Content" required></textarea>
        <button type="submit" name="create_post">Add Post</button>
    </form>
    <h2>All Posts</h2>
    <?php while ($post = $posts->fetch_assoc()): ?>
        <h3><?= $post['title'] ?></h3>
        <p><?= $post['content'] ?></p>
        <a href="?delete=<?= $post['id'] ?>">Delete</a>
    <?php endwhile; ?>
</body>
</html>
