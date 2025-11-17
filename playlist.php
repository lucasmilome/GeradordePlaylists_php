<?php
include 'includes/header.php'; 
require 'database/database.php'; 

$playlist_id = (int)($_GET['id'] ?? 0);

if ($playlist_id <= 0) {
    echo "<main class='container'><p>Playlist não encontrada.</p></main>";
    include 'includes/footer.php';
    exit;
}

$pdo = getConnection();

$stmt_playlist = $pdo->prepare("SELECT * FROM playlists WHERE id = ?");
$stmt_playlist->execute([$playlist_id]);
$playlist = $stmt_playlist->fetch(PDO::FETCH_ASSOC);

$stmt_songs = $pdo->prepare("SELECT * FROM songs WHERE playlist_id = ? ORDER BY id");
$stmt_songs->execute([$playlist_id]);
$musicas = $stmt_songs->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="container">
    <?php if (!$playlist): ?>
        <h2>Playlist não encontrada</h2>
        <p>A playlist que você está tentando acessar não existe.</p>
    
    <?php else: ?>
        <h2><?= htmlspecialchars($playlist['name']) ?></h2>
        <?php if (!empty($playlist['description'])): ?>
            <p style="font-size: 1.1rem; margin-top: -10px;"><?= htmlspecialchars($playlist['description']) ?></p>
        <?php endif; ?>

        <div class="playlist">
            <?php if (empty($musicas)): ?>
                <p>Esta playlist ainda não tem músicas.</p>
            <?php else: ?>
                <?php 
                $i = 0;
                foreach ($musicas as $musica): 
                    $delay = $i * 0.1; 
                ?>
                    <div class="musica" style="--delay: <?= $delay ?>s">
                        <div class="musica-info">
                            <strong><?= $i += 1 ?>. <?= htmlspecialchars($musica['title']) ?></strong>
                            <span><?= htmlspecialchars($musica['artist']) ?></span>
                        </div>
                        
                        <a href="https://www.youtube.com/results?search_query=<?= urlencode($musica['title'] . ' ' . $musica['artist']) ?>" target="_blank" class="musica-link">
                            <i class="bi bi-play-fill"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <a href="index.php" class="voltar-btn">⬅ Voltar</a>
</main>

<?php include 'includes/footer.php'; ?>