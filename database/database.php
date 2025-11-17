<?php
function getConnection(): PDO {
    $path = __DIR__ . '/data.sqlite';
    return new PDO('sqlite:' . $path);
}

function initializeDatabase(): void {
    $pdo = getConnection();

    $pdo->exec('CREATE TABLE IF NOT EXISTS playlists (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        artist TEXT,
        playlist_id INTEGER,
        FOREIGN KEY (playlist_id) REFERENCES playlists(id)
    )');
}

initializeDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($name) {
        $pdo = getConnection();
        $stmt = $pdo->prepare('INSERT INTO playlists (name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description]);
        header('Location: index.php');
        exit;
    }
}
?>
