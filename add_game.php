<?php
require_once __DIR__ . '/database/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = get_pdo();

    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($_FILES['image']['tmp_name']);

        if (in_array($mimeType, $allowed)) {
            $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cover_', true) . '.' . strtolower($ext);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                $msg = '<p class="msg-error">Could not save the image.</p>';
            }
        } else {
            $msg = '<p class="msg-error">Invalid file type. JPG, PNG, GIF, and WEBP only.</p>';
        }
    }

    if ($msg === '') {
        $stmt = $db->prepare(
            'INSERT INTO games (title, genre, description, image) VALUES (:title, :genre, :description, :image)'
        );
        $stmt->execute([
            ':title'       => $_POST['title'],
            ':genre'       => $_POST['genre'],
            ':description' => $_POST['description'],
            ':image'       => $imagePath,
        ]);
        $msg = '<p class="msg-success">Game added.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Game — Game Library</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php" aria-current="page">Add Game</a>
    <a href="member.php">Members</a>
</nav>

<main>
    <h1>Add Game</h1>

    <?php echo $msg; ?>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Cover image (JPG, PNG, GIF, WEBP)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
            </div>
            <button type="submit">Add Game</button>
        </form>
    </div>
</main>
</body>
</html>
