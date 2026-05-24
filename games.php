<?php

$games = [
    ["Minecraft", "Sandbox"],
    ["Stardew Valley", "Simulation"],
    ["The Legend of Zelda", "Adventure"]
];

$user_defined_games = [];

$db = new PDO('sqlite:' . __DIR__ . '/database/members.db');

$results = $db->query("SELECT * FROM games");
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
  $user_defined_games[] = [$row['title'], $row['genre']];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Games</title>
</head>
<body>

<h1>Game List</h1>

<a href="index.php">Home</a>

<hr>

<h2>predefined games</h2>

<?php foreach ($games as $game): ?>
    <p>
        <strong><?php echo htmlspecialchars($game[0]); ?></strong>
        (<?php echo htmlspecialchars($game[1]); ?>)
    </p>
<?php endforeach; ?>

<h2>User defined games</h2>

<?php foreach ($user_defined_games as $game): ?>
    <p>
        <strong><?php echo htmlspecialchars($game[0]); ?></strong>
        (<?php echo htmlspecialchars($game[1]); ?>)
    </p>
<?php endforeach; ?>
</body>
</html>
