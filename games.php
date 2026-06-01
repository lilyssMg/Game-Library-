<?php
require_once __DIR__ . '/database/db.php';

$db = get_pdo();

$search = trim($_GET['search'] ?? '');
$genre  = trim($_GET['genre']  ?? '');

// Build query with optional filters
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = '(title LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($genre !== '') {
    $where[]  = 'genre = ?';
    $params[] = $genre;
}
$sql = 'SELECT * FROM games' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY title';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$user_defined_games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Distinct genres for filter dropdown
$genres = $db->query("SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != '' ORDER BY genre")
             ->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Games — Game Library</title>
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
<h1>Games</h1>

<form class="search-bar" method="GET" action="games.php">
    <input
        type="search"
        name="search"
        placeholder="Search by title or description"
        value="<?php echo htmlspecialchars($search); ?>"
    >
    <select name="genre">
        <option value="">All genres</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?php echo htmlspecialchars($g); ?>"
                <?php echo $genre === $g ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($g); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
    <?php if ($search !== '' || $genre !== ''): ?>
        <a href="games.php" class="btn-reset">Clear</a>
    <?php endif; ?>
</form>

<?php if ($search !== '' || $genre !== ''): ?>
    <p class="result-count">
        <?php echo count($user_defined_games); ?> result<?php echo count($user_defined_games) !== 1 ? 's' : ''; ?>
        <?php if ($search !== ''): ?>for <strong><?php echo htmlspecialchars($search); ?></strong><?php endif; ?>
        <?php if ($genre  !== ''): ?>in <strong><?php echo htmlspecialchars($genre); ?></strong><?php endif; ?>
    </p>
<?php endif; ?>

<?php if (empty($user_defined_games)): ?>
    <p class="empty-state">No games found. <a href="add_game.php">Add the first one.</a></p>
<?php else: ?>
    <table class="game-table">
        <thead>
            <tr>
                <th>Cover</th>
                <th>Title &amp; Description</th>
                <th>Genre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($user_defined_games as $game): ?>
            <tr>
                <td class="cover-cell">
                    <?php if (!empty($game['image'])): ?>
                        <img src="<?php echo htmlspecialchars($game['image']); ?>"
                             alt="<?php echo htmlspecialchars($game['title']); ?>">
                    <?php else: ?>
                        <span class="no-cover">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($game['title']); ?></strong>
                    <?php if (!empty($game['description'])): ?>
                        <p class="game-desc"><?php echo htmlspecialchars($game['description']); ?></p>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($game['genre']); ?></td>
                <td class="actions">
                    <a href="edit_game.php?id=<?php echo (int) $game['game_id']; ?>">Edit</a>
                    <a href="delete_game.php?id=<?php echo (int) $game['game_id']; ?>"
                       onclick="return confirm('Delete <?php echo htmlspecialchars(addslashes($game['title'])); ?>?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</main>
</body>
</html>
