<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Método não permitido.";
    exit;
}

require 'database/database.php';

$nome = $_POST['playlist_nome'] ?? 'Playlist Sem Título';
$descricao = $_POST['playlist_descricao'] ?? '';
$json_musicas = $_POST['playlist_json'] ?? '[]';

$musicas = json_decode($json_musicas, true);

if (!$musicas || !is_array($musicas) || empty($musicas)) {
    echo "Nenhuma música para salvar.";
    header('Location: index.php?erro=salvar');
    exit;
}

$pdo = getConnection();

try {
    $pdo->beginTransaction();

    $stmt_playlist = $pdo->prepare("INSERT INTO playlists (name, description) VALUES (?, ?)");
    $stmt_playlist->execute([$nome, $descricao]);

    $playlist_id = $pdo->lastInsertId();

    $stmt_song = $pdo->prepare("INSERT INTO songs (title, artist, playlist_id) VALUES (?, ?, ?)");

    foreach ($musicas as $musica) {
        $titulo = $musica['titulo'] ?? 'Sem Título';
        $artista = $musica['artista'] ?? 'Artista Desconhecido';
        
        $stmt_song->execute([$titulo, $artista, $playlist_id]);
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erro ao salvar a playlist: " . $e->getMessage();
    exit;
}

header('Location: index.php');
exit;