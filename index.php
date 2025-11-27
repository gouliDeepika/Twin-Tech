<?php
require 'config.php';

// Fetch alerts (latest first)
$stmt = $pdo->query("SELECT * FROM alerts ORDER BY created_at DESC");
$alerts = $stmt->fetchAll();

// Handle success / error messages from redirect
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error   = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hyperlocal Emergency Alert Wall</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Hyperlocal Emergency Alert Wall</h1>
        <p>Post and view nearby emergencies in real time-style list.</p>
    </header>

    <?php if ($success === '1'): ?>
        <div class="alert-message alert-success">
            ✅ Alert posted successfully.
        </div>
    <?php elseif ($error === '1'): ?>
        <div class="alert-message alert-error">
            ❌ Please fill all required fields.
        </div>
    <?php endif; ?>

    <div class="layout">
        <!-- LEFT: Post Alert Form -->
        <div class="card">
            <h2 style="margin-bottom:10px;">Post Alert</h2>
            <form action="submit_alert.php" method="POST">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" placeholder="Road Accident" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" placeholder="Describe what happened..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="area">Area / Location *</label>
                    <input type="text" id="area" name="area" placeholder="Vidyanagar, Hubballi" required>
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="Accident">Accident</option>
                        <option value="Fire">Fire</option>
                        <option value="Medical">Medical</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="severity">Severity *</label>
                    <select id="severity" name="severity" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Submit Alert</button>
            </form>
        </div>

        <!-- RIGHT: Live Alert Wall -->
        <div class="card">
            <div class="alert-list-header">
                <h2>Live Alert Wall</h2>
                <span style="font-size:12px;color:#555;">
                    Showing latest alerts first
                </span>
            </div>

            <?php if (empty($alerts)): ?>
                <p style="font-size:14px;color:#555;">No alerts yet. Be the first to post.</p>
            <?php else: ?>
                <?php foreach ($alerts as $alert): ?>
                    <?php
                    $sev = strtolower($alert['severity']); // low / medium / high
                    $badgeClass = 'badge-' . $sev;
                    ?>
                    <div class="alert-item">
                        <div class="alert-top-row">
                            <span class="alert-title">
                                <?= htmlspecialchars($alert['title']); ?>
                            </span>
                            <span class="badge <?= $badgeClass; ?>">
                                <?= htmlspecialchars($alert['severity']); ?>
                            </span>
                        </div>

                        <div class="alert-meta">
                            <span class="category-pill">
                                <?= htmlspecialchars($alert['category']); ?>
                            </span>
                            &nbsp;•&nbsp;
                            <strong><?= htmlspecialchars($alert['area']); ?></strong>
                            &nbsp;•&nbsp;
                            <span>
                                <?= date('d M Y, h:i A', strtotime($alert['created_at'])); ?>
                            </span>
                        </div>

                        <div class="alert-description">
                            <?= nl2br(htmlspecialchars($alert['description'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
