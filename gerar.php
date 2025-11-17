<?php
include 'includes/header.php';

$humor = $_POST['humor'] ?? 'desconhecido';
$quantidade = (int)($_POST['quantidade'] ?? 10);
$regiao = $_POST['regiao'] ?? 'brasileira';
$artista = $_POST['artista'] ?? '';
$descricao = $_POST['descricao'] ?? '';

require_once 'ia/gerar_playlist.php';

$resultadoIA = gerarPlaylistIA($humor, $quantidade, $regiao, $artista, $descricao);
$musicas = json_decode($resultadoIA, true);

if (!is_array($musicas)) {
    preg_match('/\[\s*{.*}\s*\]/s', $resultadoIA, $match);
    if ($match) {
        $musicas = json_decode($match[0], true);
    }
}
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
            foreach ($musicas as $musica): ?>
                <?php $delay = $i * 0.1; ?>
                <div class="musica" style="--delay: <?= $delay ?>s">
                    <strong><?= $i += 1 ?>. <?= htmlspecialchars($musica['titulo'] ?? 'Sem título') ?></strong><br>
                    <?= htmlspecialchars($musica['artista'] ?? 'Artista desconhecido') ?><br>
                    <?php if (!empty($musica['link'])): ?>
                        <a href="<?= htmlspecialchars($musica['link']) ?>" target="_blank">🎵 Ouvir</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="index.php" class="voltar-btn">⬅ Voltar</a>
</main>

<?php include 'includes/footer.php'; ?>
