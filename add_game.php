<?php

require_once __DIR__ . '/database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = get_pdo();

    $stmt = $db->prepare('INSERT INTO games (title, genre, description) VALUES (:title, :genre, :description)');
    $stmt->execute([
        ':title'       => $_POST['title'],
        ':genre'       => $_POST['genre'],
        ':description' => $_POST['description'],
    ]);

    echo "<p>Game added successfully!</p>";
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

<form method="POST">
    <label>Game Title:</label><br>
    <input type="text" name="title">

    <br><br>

    <label>Genre:</label><br>
    <input type="text" name="genre">

    <br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea>

    <br><br>

    <button type="submit">Submit</button>
</form>

</body>
</html>
