<?php
require 'config.php';

// Get selected area from URL (if any)
$selectedArea = $_GET['area'] ?? 'all';

// Get list of distinct areas for dropdown
$areaStmt = $pdo->query("SELECT DISTINCT area FROM alerts ORDER BY area ASC");
$areas = $areaStmt->fetchAll();

// Fetch alerts (filter by area if chosen)
if ($selectedArea === 'all' || $selectedArea === '') {
    $stmt = $pdo->query("SELECT * FROM alerts ORDER BY created_at DESC");
    $alerts = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM alerts WHERE area = :area ORDER BY created_at DESC");
    $stmt->execute([':area' => $selectedArea]);
    $alerts = $stmt->fetchAll();
}

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
                    <input type="text" id="area" name="area"
                           placeholder="Vidyanagar, Hubballi"
                           autocomplete="off" required>
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

            <!-- Smart Safety Tips box -->
            <div id="safety-tips" style="margin-top:12px;font-size:13px;"></div>
        </div>

        <!-- RIGHT: Live Alert Wall + Map -->
        <div class="card">
            <div class="alert-list-header">
                <h2>Live Alert Wall</h2>

                <!-- Area filter -->
                <form method="GET" style="display:flex;align-items:center;gap:6px;font-size:12px;">
                    <label for="area-filter">Area:</label>
                    <select id="area-filter" name="area"
                            onchange="this.form.submit()"
                            style="padding:4px 6px;font-size:12px;">
                        <option value="all">All areas</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= htmlspecialchars($a['area']); ?>"
                                <?= ($selectedArea === $a['area']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($a['area']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
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

            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb;">

            <h3 style="font-size:14px;margin-bottom:6px;">Map View (Demo)</h3>
            <div id="alert-map"
                 style="height:260px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;"></div>
        </div>
    </div>
</div>

<!-- Smart Safety Tips script -->
<script>
const tipsByCategory = {
    "Accident": [
        "Stay away from the road and moving vehicles.",
        "Call local emergency services if someone is injured.",
        "Do not block the road while taking photos or videos."
    ],
    "Fire": [
        "Move to an open safe area away from smoke.",
        "Do not use elevators in multi-storey buildings.",
        "Alert others nearby and call the fire department."
    ],
    "Medical": [
        "Call an ambulance or doctor immediately.",
        "Do not crowd around the patient.",
        "If trained, provide only basic first aid."
    ],
    "Other": [
        "Stay aware of your surroundings.",
        "Avoid spreading panic or rumors.",
        "Share only verified information."
    ]
};

function updateSafetyTips() {
    const categorySelect = document.getElementById('category');
    const box = document.getElementById('safety-tips');
    if (!categorySelect || !box) return;

    const category = categorySelect.value;
    const tips = tipsByCategory[category] || tipsByCategory["Other"];

    box.innerHTML = `
        <strong>Smart Safety Tips (${category}):</strong>
        <ul style="margin-top:4px;padding-left:18px;">
            ${tips.map(t => `<li>${t}</li>`).join('')}
        </ul>
    `;
}

// Run on load and when category changes
document.addEventListener('DOMContentLoaded', () => {
    updateSafetyTips();
    const categorySelect = document.getElementById('category');
    if (categorySelect) {
        categorySelect.addEventListener('change', updateSafetyTips);
    }
});
</script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// PHP alerts array converted to JS
const alertsForMap = <?php echo json_encode($alerts); ?>;

// Demo: generic coordinates for big areas / cities
// This will match if the area text CONTAINS the keyword
const areaPatterns = [
    { keyword: "hubballi", lat: 15.3647, lng: 75.1239 },
    { keyword: "hubli",    lat: 15.3647, lng: 75.1239 }, // alternate spelling
    { keyword: "dharwad",  lat: 15.4589, lng: 75.0078 }
];

// Default map center (between Hubballi & Dharwad approx)
const map = L.map('alert-map').setView([15.41, 75.07], 11);

// Tile layer from OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Add markers for alerts
alertsForMap.forEach(alert => {
    if (!alert.area) return;

    const areaText = String(alert.area).toLowerCase();

    // Find first matching pattern whose keyword is contained in the area text
    let match = null;
    for (const pattern of areaPatterns) {
        if (areaText.includes(pattern.keyword)) {
            match = pattern;
            break;
        }
    }

    // If no match found, skip this alert (no coords)
    if (!match) return;

    const marker = L.marker([match.lat, match.lng]).addTo(map);
    marker.bindPopup(
        `<strong>${alert.title}</strong><br>
        ${alert.area}<br>
        Category: ${alert.category}<br>
        Severity: ${alert.severity}`
    );
});
</script>

</body>
</html>
