<?php

require_once __DIR__ . '/database/db.php';

$db = get_pdo();

$id = $_GET['id'];

$stmt = $db->prepare(
    "SELECT * FROM games WHERE id = ?"
);

$stmt->execute([$id]);

$game = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $db->prepare(
        "UPDATE games
        SET title = ?,
            genre = ?,
            description = ?
        WHERE id = ?"
    );

    $stmt->execute([
        $_POST['title'],
        $_POST['genre'],
        $_POST['description'],
        $id
    ]);

    header("Location: games.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php">Members</a>
</nav>

<h1>Edit Game</h1>

<div class="card">

<form method="POST">

    <label>Game Title:</label>
    <br>

    <input
        type="text"
        name="title"
        value="<?php echo htmlspecialchars($game['title']); ?>"
    >

    <br><br>

    <label>Genre:</label>
    <br>

    <input
        type="text"
        name="genre"
        value="<?php echo htmlspecialchars($game['genre']); ?>"
    >

    <br><br>

    <label>Description:</label>
    <br>

    <textarea name="description"><?php echo htmlspecialchars($game['description']); ?></textarea>

    <br><br>

    <button type="submit">
        Update Game
    </button>

</form>

</div>

</body>
</html>
