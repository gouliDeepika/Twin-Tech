<?php
session_start();
require '../config.php';

// If not logged in, send back to login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all alerts (latest first)
$stmt = $pdo->query("SELECT * FROM alerts ORDER BY created_at DESC");
$alerts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Twin Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Admin Dashboard</h1>
        <p>Review and manage community alerts.</p>
        <p style="font-size:13px;margin-top:6px;">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['admin_username']); ?></strong>
            | <a href="logout.php">Logout</a>
        </p>
    </header>

    <div class="card">
        <?php if (empty($alerts)): ?>
            <p>No alerts found.</p>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">ID</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Title</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Area</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Category</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Severity</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Time</th>
                        <th style="border-bottom:1px solid #e5e7eb;text-align:left;padding:6px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alerts as $alert): ?>
                        <tr>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;"><?= $alert['id']; ?></td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;"><?= htmlspecialchars($alert['title']); ?></td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;"><?= htmlspecialchars($alert['area']); ?></td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;"><?= htmlspecialchars($alert['category']); ?></td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;"><?= htmlspecialchars($alert['severity']); ?></td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;">
                                <?= date('d M Y, h:i A', strtotime($alert['created_at'])); ?>
                            </td>
                            <td style="border-bottom:1px solid #f3f4f6;padding:6px;">
                                <!-- Delete will work after next step -->
                                <a href="delete_alert.php?id=<?= $alert['id']; ?>"
                                   onclick="return confirm('Delete this alert?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
