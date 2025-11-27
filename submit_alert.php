<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and clean inputs
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $area        = trim($_POST['area'] ?? '');
    $category    = $_POST['category'] ?? '';
    $severity    = $_POST['severity'] ?? '';

    // Simple validation
    if ($title === '' || $description === '' || $area === '' || $category === '' || $severity === '') {
        header('Location: index.php?error=1');
        exit;
    }

    // Insert into database
    $sql = "INSERT INTO alerts (title, description, area, category, severity)
            VALUES (:title, :description, :area, :category, :severity)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'       => $title,
        ':description' => $description,
        ':area'        => $area,
        ':category'    => $category,
        ':severity'    => $severity
    ]);

    // Redirect back to home with success message
    header('Location: index.php?success=1');
    exit;
} else {
    // If someone opens this file directly
    header('Location: index.php');
    exit;
}
