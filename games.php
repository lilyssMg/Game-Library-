<?php
require_once __DIR__ . '/database/db.php';
$games = [
    ["Minecraft", "Sandbox"],
    ["Stardew Valley", "Simulation"],
    ["The Legend of Zelda", "Adventure"]
];
$user_defined_games = [];
$db = get_pdo();
$results = $db->query("SELECT * FROM games");
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
  $user_defined_games[] = [
      $row['id'],
      $row['title'],
      $row['genre'],
      $row['image'] ?? null,
  ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Games</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            display: block;
            margin-bottom: 8px;
        }
        .card .no-image {
            width: 100px;
            height: 100px;
            background: #e0e0e0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #888;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php">Members</a>
</nav>
<h1>Game List</h1>
<hr>
<h2>predefined games</h2>
<?php foreach ($games as $game): ?>
    <div class="card">
        <div class="no-image">No image</div>
        <strong>
            <?php echo htmlspecialchars($game[0]); ?>
        </strong>
        <br>
        Genre:
        <?php echo htmlspecialchars($game[1]); ?>
    </div>
<?php endforeach; ?>
<h2>User defined games</h2>
<?php foreach ($user_defined_games as $game): ?>
<div class="card">
    <?php if (!empty($game[3])): ?>
        <img src="<?php echo htmlspecialchars($game[3]); ?>"
             alt="Cover of <?php echo htmlspecialchars($game[1]); ?>">
    <?php else: ?>
        <div class="no-image">No image</div>
    <?php endif; ?>
    <strong>
        <?php echo htmlspecialchars($game[1]); ?>
    </strong>
    <br>
    Genre:
    <?php echo htmlspecialchars($game[2]); ?>
    <br><br>
    <a href="delete_game.php?id=<?php echo $game[0]; ?>">
        Delete
    </a>
</div>
<?php endforeach; ?>
</body>
</html>
