<?php
session_start();
require '../config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        // Hash password (same as DB SHA-256)
        $passwordHash = hash('sha256', $password);

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username AND password_hash = :password_hash");
        $stmt->execute([
            ':username'      => $username,
            ':password_hash' => $passwordHash
        ]);

        $admin = $stmt->fetch();

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Admin Panel</h1>
        <p>Login to manage alerts</p>
    </header>

    <div class="card" style="max-width:400px;margin:0 auto;">
        <?php if ($error): ?>
            <div class="alert-message alert-error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" placeholder="admin" required>
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" placeholder="admin123" required>
            </div>

            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</div>
</body>
</html>
