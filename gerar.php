<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $humor = $_POST['humor'] ?? 'desconhecido';
    $quantidade = (int)($_POST['quantidade'] ?? 10);
    $regiao = $_POST['regiao'] ?? 'brasileira';
    $artista = $_POST['artista'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    require_once 'ia/gerar_playlist.php';
    $resultadoIA = gerarPlaylistIA($humor, $quantidade, $regiao, $artista, $descricao);

    $_SESSION['resultadoIA'] = $resultadoIA;
    $_SESSION['post_data'] = [
        'humor' => $humor,
        'quantidade' => $quantidade,
        'regiao' => $regiao,
        'artista' => $artista,
        'descricao' => $descricao
    ];
    
    header('Location: gerar.php');
    exit;

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_SESSION['resultadoIA']) || !isset($_SESSION['post_data'])) {
        include 'includes/header.php';
        echo "<main class='container'><p>Nenhuma playlist para exibir... Redirecionando.</p><script>setTimeout(() => window.location.href='index.php', 1500);</script></main>";
        include 'includes/footer.php';
        exit;
    }

    $resultadoIA = $_SESSION['resultadoIA'];
    $post_data = $_SESSION['post_data'];

    $humor = $post_data['humor'];
    $quantidade = $post_data['quantidade'];
    $regiao = $post_data['regiao'];
    $artista = $post_data['artista'];
    $descricao = $post_data['descricao'];
    
    $musicas = json_decode($resultadoIA, true);
    if (!is_array($musicas)) {
        preg_match('/\[\s*{.*}\s*\]/s', $resultadoIA, $match);
        if ($match) {
            $musicas = json_decode($match[0], true);
        }
    }

} else {
    header('Location: index.php');
    exit;
}

include 'includes/header.php';
?>

<main class="container">
    <h2>🎧 Playlist Gerada</h2>

    <?php if (!$musicas || !is_array($musicas)): ?>
        <p>❌ Não foi possível gerar a playlist. Tente novamente.</p>
        <details style="margin-top:1rem;">
            <summary>Mostrar detalhes técnicos</summary>
            <pre><?= htmlspecialchars($resultadoIA) ?></pre>
        </details>
    <?php else: ?>
        <p>
            Aqui está sua playlist
            <?php if ($humor !== 'personalizado'): ?>
                de <strong><?= ucfirst($humor) ?></strong>
            <?php endif; ?>
            com <strong><?= $quantidade ?></strong> músicas!
        </p>

        <div class="playlist">
            <?php 
            $i = 0;
            foreach ($musicas as $musica): 
                $delay = $i * 0.1; 
            ?>
                <div class="musica" style="--delay: <?= $delay ?>s">
                    <div class="musica-info">
                        <strong><?= $i += 1 ?>. <?= htmlspecialchars($musica['titulo'] ?? 'Sem título') ?></strong>
                        <span><?= htmlspecialchars($musica['artista'] ?? 'Artista desconhecido') ?></span>
                    </div>

                    <?php if (!empty($musica['link'])): ?>
                        <a href="<?= htmlspecialchars($musica['link']) ?>" target="_blank" class="musica-link">
                            <i class="bi bi-play-fill"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        if ($musicas && is_array($musicas)):
            $nome_sugerido = "Minha Playlist de $humor";
            if ($humor == 'personalizado' && !empty($descricao)) {
                $nome_sugerido = "Playlist: " . substr($descricao, 0, 20) . "...";
            }
            $descricao_sugerida = "Uma playlist de $humor com $quantidade músicas ($regiao). Artista base: $artista.";
        ?>
            <form action="salvar.php" method="POST" class="salvar-form">
                <h3>💾 Salvar esta Playlist</h3>
                <label>
                    Nome da Playlist:
                    <input type="text" name="playlist_nome" value="<?= htmlspecialchars($nome_sugerido) ?>" required>
                </label>
                <label>
                    Descrição (opcional):
                    <textarea name="playlist_descricao" rows="2"><?= htmlspecialchars($descricao_sugerida) ?></textarea>
                </label>

                <input type="hidden" name="playlist_json" value="<?= htmlspecialchars(json_encode($musicas)) ?>">

                <button type="submit" class="salvar-btn">Salvar no Banco de Dados</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>

    <a href="index.php" class="voltar-btn">⬅ Voltar</a>
</main>

<?php include 'includes/footer.php'; ?>