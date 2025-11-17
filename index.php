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

        <div class="filtros" id="filtros">
            <h3>Filtros adicionais</h3>

            <input type="hidden" name="humor" id="humorInput">

            <div id="campoPersonalizado" style="display: none;">
                <label>
                    Descreva como você está se sentindo:
                    <textarea name="descricao" rows="2" placeholder="Ex: Calmo mas inspirado, querendo relaxar..."></textarea>
                </label>
            </div>

            <label>
                Quantidade de músicas: <strong id="valorQuantidade">10</strong>
                <input type="range" name="quantidade" min="3" max="30" value="10" id="sliderQuantidade">
            </label>
            <label for="select-real" style="margin-bottom: 0px;">Região ou Gênero:</label>

            <select name="regiao" id="select-real" style="display:none;">
                <option value="" disabled selected>Escolha uma opção</option>
                <option value="afrobeat">Afrobeat</option>
                <option value="brasil">Brasil</option>
                <option value="eletronica">Eletrônica (Global)</option>
                <option value="hiphop">Hip-Hop (Global)</option>
                <option value="internacional">Internacional (Geral)</option>
                <option value="jpop">J-Pop (Japão)</option>
                <option value="kpop">K-Pop (Coreia)</option>
                <option value="latina">Música Latina</option>
                <option value="pop">Pop (Global)</option>
                <option value="rock">Rock (Global)</option>
            </select>

            <div class="custom-select-wrapper">
                <div class="custom-select-trigger">
                    <span>Escolha uma opção</span>
                    <div class="arrow"></div>
                </div>
                <div class="custom-options">
                    <span class="custom-option" data-value="afrobeat">Afrobeat</span>
                    <span class="custom-option" data-value="brasil">Brasil</span>
                    <span class="custom-option" data-value="eletronica">Eletrônica (Global)</span>
                    <span class="custom-option" data-value="hiphop">Hip-Hop (Global)</span>
                    <span class="custom-option" data-value="internacional">Internacional (Geral)</span>
                    <span class="custom-option" data-value="jpop">J-Pop (Japão)</span>
                    <span class="custom-option" data-value="kpop">K-Pop (Coreia)</span>
                    <span class="custom-option" data-value="latina">Música Latina</span>
                    <span class="custom-option" data-value="pop">Pop (Global)</span>
                    <span class="custom-option" data-value="rock">Rock (Global)</span>
                </div>
            </div>

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

    <?php if (!empty($playlists)): ?>
        <h2 style="margin-top: 5rem;">Playlists Salvas</h2>

        <input type="text" id="campoBuscaPlaylist" placeholder="🔎 Buscar pelo nome...">

        <ul class="playlists-salvas"> <?php foreach ($playlists as $p): ?>
            <li>
                <a href="playlist.php?id=<?= $p['id'] ?>">
                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                </a>
                <p><?= htmlspecialchars($p['description'] ?? 'Sem descrição') ?></p>
                <em>Criada em <?= date('d/m/Y', strtotime($p['created_at'])) ?></em>
            </li>
        <?php endforeach; ?>
        </ul>
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

            filtros.classList.add('filtros-visivel'); 
            
            window.scrollTo({ top: filtros.offsetTop, behavior: 'smooth' });

            if (humor === 'personalizado') {
                campoPersonalizado.style.display = 'block';
            } else {
                campoPersonalizado.style.display = 'none';
            }
        });
    });

    const wrapper = document.querySelector('.custom-select-wrapper');
    
    if (wrapper) { 
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const triggerText = trigger.querySelector('span');
        const options = wrapper.querySelectorAll('.custom-option');
        const realSelect = document.getElementById('select-real');

        trigger.addEventListener('click', () => {
            wrapper.classList.toggle('open');
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const selectedValue = option.dataset.value;
                const selectedText = option.textContent;

                triggerText.textContent = selectedText;
                
                realSelect.value = selectedValue;

                wrapper.classList.remove('open');
            });
        });

        window.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                wrapper.classList.remove('open');
            }
        });
    };

    const slider = document.getElementById('sliderQuantidade');
    const valorDisplay = document.getElementById('valorQuantidade');

    if (slider && valorDisplay) {
        
        slider.addEventListener('input', () => {
            valorDisplay.textContent = slider.value;
        });
    };

    const campoBusca = document.getElementById('campoBuscaPlaylist');

    if (campoBusca) {
        
        campoBusca.addEventListener('keyup', () => {
            const textoBusca = campoBusca.value.toLowerCase();
            const listaPlaylists = document.querySelectorAll('.playlists-salvas li');

            listaPlaylists.forEach(item => {
                const nomePlaylist = item.querySelector('strong').textContent.toLowerCase();

                if (nomePlaylist.includes(textoBusca)) {
                    item.style.display = 'block'; 
                } else {
                    item.style.display = 'none'; 
                }
            });
        });
    };

</script>
<?php include 'includes/footer.php'; ?>
