<?php

require_once __DIR__ . '/database/db.php';

$db = get_pdo();

if (php_sapi_name() === 'cli' && isset($argv[1])) {
    parse_str($argv[1], $_GET);
}

if (!isset($_GET['id'])) {

    $results = $db->query("SELECT * FROM members");

    echo "<!DOCTYPE html>
<html>
<head>
<title>Group Members</title>
<link rel='stylesheet' href='style.css'>
</head>
<body>";

echo "
<nav>
<a href='index.php'>Home</a>
<a href='games.php'>Games</a>
<a href='add_game.php'>Add Game</a>
<a href='member.php'>Members</a>
</nav>
";
    echo "<a href='index.php'>Home</a>";
    echo "<h1>Group Members</h1>";

    while ($row = $results->fetch()) {

    echo "<div class='card'>";

    echo "<a href='member.php?id=" . $row['id'] . "'>";

    echo htmlspecialchars($row['name']);

    echo "</a>";

    echo "</div>";
}

    echo "</body></html>";

} else {

    $id = (int) $_GET['id'];

    $stmt = $db->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    echo "<!DOCTYPE html>
<html>
<head>
<title>Member</title>
<link rel='stylesheet' href='style.css'>
</head>
<body>";

echo "
<nav>
<a href='index.php'>Home</a>
<a href='games.php'>Games</a>
<a href='add_game.php'>Add Game</a>
<a href='member.php'>Members</a>
</nav>
";
    echo "<a href='index.php'>Home</a>";

    if ($result) {
        echo "<div class='card'>";

echo "<h1>" . htmlspecialchars($result['name']) . "</h1>";

        echo "<p><strong>Student ID:</strong> "
            . htmlspecialchars($result['student_id']) . "</p>";

        echo "<p><strong>Email:</strong> "
            . htmlspecialchars($result['email']) . "</p>";

        echo "<p>" . htmlspecialchars($result['bio']) . "</p>";

        echo "<p><a href='member.php'>Back to Group Members</a></p>";

echo "</div>";
    } else {
        echo "<p>Member not found.</p>";
    }

    echo "</body></html>";
}

?>
