<?php
require_once __DIR__ . '/database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = get_pdo();

    $imagePath = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';

        // Create uploads folder if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($_FILES['image']['tmp_name']);

        if (in_array($mimeType, $allowed)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cover_', true) . '.' . strtolower($ext);
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                echo "<p style='color:red;'>Error: could not save the image.</p>";
            }
        } else {
            echo "<p style='color:red;'>Error: invalid file type. Only JPG, PNG, GIF and WEBP are allowed.</p>";
        }
    }

    $stmt = $db->prepare(
        'INSERT INTO games (title, genre, description, image)
         VALUES (:title, :genre, :description, :image)'
    );
    $stmt->execute([
        ':title'       => $_POST['title'],
        ':genre'       => $_POST['genre'],
        ':description' => $_POST['description'],
        ':image'       => $imagePath,
    ]);

    echo "<p style='color:green;'>Game added successfully!</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Game</title>
</head>
<body>
<h1>Add Game</h1>
<a href="index.php">Home</a>
<hr>
<form method="POST" enctype="multipart/form-data">
    <label>Game Title:</label><br>
    <input type="text" name="title" required>
    <br><br>
    <label>Genre:</label><br>
    <input type="text" name="genre" required>
    <br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea>
    <br><br>
    <label>Cover Image (JPG, PNG, GIF, WEBP):</label><br>
    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
    <br><br>
    <button type="submit">Submit</button>
</form>
</body>
</html>
