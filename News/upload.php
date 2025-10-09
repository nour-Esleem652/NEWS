<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $imageName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            echo "Only JPG, PNG, and GIF files are allowed.";
            exit;
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            echo "Image uploaded successfully: <a href='$targetPath'>$imageName</a>";
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "No image uploaded or upload error.";
    }
}
?>