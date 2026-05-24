php<?php

$games = [
    ["Minecraft", "Sandbox"],
    ["Stardew Valley", "Simulation"],
    ["The Legend of Zelda", "Adventure"]
];

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

<?php foreach ($games as $game): ?>
    <p>
        <strong><?php echo htmlspecialchars($game[0]); ?></strong>
        (<?php echo htmlspecialchars($game[1]); ?>)
    </p>
<?php endforeach; ?>

</body>
</html>
