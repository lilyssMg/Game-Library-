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

    echo "<div class='card'>Game added successfully!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php">Members</a>
</nav>

<h1>Add Game</h1>

<hr>

<div class="card">

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

</div>

</body>
</html>
