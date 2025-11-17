<?php include 'includes/header.php'; ?>

<main class="container">
    <h2>Como você está se sentindo hoje?</h2>

    <form id="playlistForm" action="gerar.php" method="POST" class="humor-form">
        <div class="humores">
            <button type="button" data-humor="feliz" class="btn-humor feliz">
                <i class="bi"></i>
                <h3>Feliz</h3>
                <p>Músicas alegres e animadas</p>
            </button>

            <button type="button" data-humor="triste" class="btn-humor triste">
                <i class="bi"></i>
                <h3>Triste</h3>
                <p>Para momentos de reflexão</p>
            </button>

            <button type="button" data-humor="foco" class="btn-humor foco">
                <i class="bi"></i>
                <h3>Foco</h3>
                <p>Concentração e produtividade</p>
            </button>

            <button type="button" data-humor="treino" class="btn-humor treino">
                <i class="bi"></i>
                <h3>Treino</h3>
                <p>Energia para treinos e exercícios</p>
            </button>

            <button type="button" data-humor="personalizado" class="btn-humor personalizado">
                <h3>+</h3>
            </button>
        </div>

        <div class="filtros" id="filtros" style="display: none;">
            <h3>Filtros adicionais</h3>

            <input type="hidden" name="humor" id="humorInput">

            <!-- Campo extra pro humor personalizado -->
            <div id="campoPersonalizado" style="display: none;">
                <label>
                    Descreva como você está se sentindo:
                    <textarea name="descricao" rows="2" placeholder="Ex: Calmo mas inspirado, querendo relaxar..."></textarea>
                </label>
            </div>

            <label>
                Quantidade de músicas:
                <input type="number" name="quantidade" min="3" max="20" value="10">
            </label>
            <label>
                Região:
                <select name="regiao">
                    <option value="brasil">Brasil</option>
                    <option value="internacional">Internacional</option>
                </select>
            </label>
            <label>
                Artista base (opcional):
                <input type="text" name="artista" placeholder="Ex: Anitta, Imagine Dragons...">
            </label>

            <button type="submit" class="gerar-btn">Gerar Playlist 🎶</button>
        </div>
    </form>

    <?php
    require 'database/database.php';
    $pdo = getConnection();
    $playlists = $pdo->query('SELECT * FROM playlists ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (!isset($playlists)): ?>
        <h2 style="margin-top: 5rem;">Playlists Salvas</h2>

        <ul>
        <?php foreach ($playlists as $p): ?>
            <li>
                <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                <?= htmlspecialchars($p['description'] ?? '') ?><br>
                <em>Criada em <?= $p['created_at'] ?></em>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script>
    const botoesHumor = document.querySelectorAll('.btn-humor');
    const filtros = document.getElementById('filtros');
    const campoPersonalizado = document.getElementById('campoPersonalizado');
    const humorInput = document.getElementById('humorInput');

    botoesHumor.forEach(btn => {
        btn.addEventListener('click', () => {
            const humor = btn.dataset.humor;
            humorInput.value = humor;

            filtros.style.display = 'block';
            window.scrollTo({ top: filtros.offsetTop, behavior: 'smooth' });

            if (humor === 'personalizado') {
                campoPersonalizado.style.display = 'block';
            } else {
                campoPersonalizado.style.display = 'none';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
