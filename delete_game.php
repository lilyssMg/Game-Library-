<?php

require_once __DIR__ . '/database/db.php';

$db = get_pdo();

if (isset($_GET['id'])) {

    $stmt = $db->prepare(
        "DELETE FROM games WHERE id = ?"
    );

    $stmt->execute([
        $_GET['id']
    ]);
}

header("Location: games.php");
exit;
