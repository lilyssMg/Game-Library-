<?php
require_once __DIR__ . '/database/db.php';

$db = get_pdo();

if (php_sapi_name() === 'cli' && isset($argv[1])) {
    parse_str($argv[1], $_GET);
}

if (!isset($_GET['id'])) {
    $results = $db->query("SELECT * FROM members ORDER BY name");
    $members = $results->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members — Game Library</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php" aria-current="page">Members</a>
</nav>

<main>
    <h1>Group Members</h1>
    <?php if (empty($members)): ?>
        <p class="empty-state">No members found.</p>
    <?php else: ?>
        <ul class="member-list">
            <?php foreach ($members as $row): ?>
                <li><a href="member.php?id=<?php echo (int) $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
<?php
} else {
    $id   = (int) $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $result ? htmlspecialchars($result['name']) : 'Member'; ?> — Game Library</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="games.php">Games</a>
    <a href="add_game.php">Add Game</a>
    <a href="member.php" aria-current="page">Members</a>
</nav>

<main>
<?php if ($result): ?>
    <div class="profile-card">
        <h1><?php echo htmlspecialchars($result['name']); ?></h1>
        <dl>
            <div class="profile-field">
                <dt>Student ID</dt>
                <dd><?php echo htmlspecialchars($result['student_id']); ?></dd>
            </div>
            <div class="profile-field">
                <dt>Email</dt>
                <dd><?php echo htmlspecialchars($result['email']); ?></dd>
            </div>
        </dl>
        <?php if (!empty($result['bio'])): ?>
            <p class="profile-bio"><?php echo htmlspecialchars($result['bio']); ?></p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p class="empty-state">Member not found.</p>
<?php endif; ?>
    <a href="member.php" class="back-link">Back to members</a>
</main>
</body>
</html>
<?php } ?>
