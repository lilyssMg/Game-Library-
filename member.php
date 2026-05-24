<?php

$db = new SQLite3(__DIR__ . '/database/members.db');

if (php_sapi_name() === 'cli' && isset($argv[1])) {
    parse_str($argv[1], $_GET);
}

if (!isset($_GET['id'])) {

    $results = $db->query("SELECT * FROM members");

    echo "<!DOCTYPE html><html><head><title>Group Members</title></head><body>";
    echo "<a href='index.php'>Home</a>";
    echo "<h1>Group Members</h1>";

    while ($row = $results->fetchArray()) {
        echo "<a href='member.php?id=" . $row['id'] . "'>";
        echo htmlspecialchars($row['name']);
        echo "</a><br><br>";
    }

    echo "</body></html>";

} else {

    $id = (int) $_GET['id'];

    $result = $db->querySingle(
        "SELECT * FROM members WHERE id = $id",
        true
    );

    echo "<!DOCTYPE html><html><head><title>Member</title></head><body>";
    echo "<a href='index.php'>Home</a>";

    if ($result) {
        echo "<h1>" . htmlspecialchars($result['name']) . "</h1>";

        echo "<p><strong>Student ID:</strong> "
            . htmlspecialchars($result['student_id']) . "</p>";

        echo "<p><strong>Email:</strong> "
            . htmlspecialchars($result['email']) . "</p>";

        echo "<p>" . htmlspecialchars($result['bio']) . "</p>";

        echo "<p><a href='member.php'>Back to Group Members</a></p>";
    } else {
        echo "<p>Member not found.</p>";
    }

    echo "</body></html>";
}

?>
