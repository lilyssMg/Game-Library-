<?php
require_once __DIR__ . '/database/db.php';

$db = get_pdo();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM games WHERE game_id = ?");
$stmt->execute([$id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header("Location: games.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare(
        "UPDATE games SET title = ?, genre = ?, description = ? WHERE game_id = ?"
    );
    $stmt->execute([
        $_POST['title'],
        $_POST['genre'],
        $_POST['description'],
        $id,
    ]);
    header("Location: games.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game — Game Library</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php">Members</a>
</nav>

<main>
    <h1>Edit Game</h1>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required
                       value="<?php echo htmlspecialchars($game['title']); ?>">
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" required
                       value="<?php echo htmlspecialchars($game['genre']); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($game['description']); ?></textarea>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <a href="games.php" class="back-link">Back to games</a>
</main>
</body>
</html>
