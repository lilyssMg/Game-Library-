<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: insert into database
    echo "<p>Game submitted (DB not connected yet).</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Game</title>
</head>
<body>

<h1>Add Game</h1>

<a href="index.php">Home</a>

<hr>

<form method="POST">
    <label>Game Title:</label><br>
    <input type="text" name="title">

    <br><br>

    <label>Genre:</label><br>
    <input type="text" name="genre">

    <br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea>

    <br><br>

    <button type="submit">Submit</button>
</form>

</body>
</html>
