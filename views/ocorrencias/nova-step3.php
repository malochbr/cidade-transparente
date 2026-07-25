<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();

$categoria = $_SESSION['nova_ocorrencia']['categoria'] ?? 'buraco_na_via';

$defaults = [
    'buraco_na_via' => [
        'titulo' => 'Buraco na via',
        'descricao' => 'Buraco grande na via, dificultando a passagem de veículos e oferecendo risco de acidentes.'
    ],
    'iluminacao_publica' => [
        'titulo' => 'Iluminação pública',
        'descricao' => 'Poste com lâmpada queimada ou apagada durante a noite, deixando a rua totalmente no escuro.'
    ],
    'alagamento' => [
        'titulo' => 'Alagamento',
        'descricao' => 'Acúmulo de água na via após chuvas, dificultando o trânsito de pedestres e veículos.'
    ],
    'terreno_baldio' => [
        'titulo' => 'Terreno baldio',
        'descricao' => 'Terreno com mato alto e acúmulo de lixo, necessitando de fiscalização e limpeza.'
    ],
    'limpeza_urbana' => [
        'titulo' => 'Limpeza urbana',
        'descricao' => 'Descarte irregular de lixo e entulhos na calçada, necessitando recolhimento urgente.'
    ],
    'outros' => [
        'titulo' => 'Outros problemas',
        'descricao' => 'Descreva aqui os detalhes do problema identificado para que a prefeitura possa analisar.'
    ]
];

$defaultData = $defaults[$categoria] ?? $defaults['buraco_na_via'];
$defaultTitulo = $_POST['titulo'] ?? $defaultData['titulo'];
$defaultDescricao = $_POST['descricao'] ?? $defaultData['descricao'];
$charLen = mb_strlen($defaultDescricao);
?>
<div class="screen-body">
    <div class="page-header">
        <a href="?page=nova-ocorrencia&step=2" class="back-btn" title="Voltar">←</a>
        <div class="page-header-info">
            <h2>Nova ocorrência</h2>
            <p>Informe os detalhes do problema</p>
        </div>
    </div>

    <div class="stepper">
        <div class="step done">
            <div class="step-num">✓</div>
            <span class="step-label">Categoria</span>
        </div>
        <div class="step-divider done"></div>
        <div class="step done">
            <div class="step-num">✓</div>
            <span class="step-label">Localização</span>
        </div>
        <div class="step-divider done"></div>
        <div class="step active">
            <div class="step-num">3</div>
            <span class="step-label">Detalhes</span>
        </div>
    </div>

    <div class="section-label">
        <strong>Detalhes</strong>
        Descreva o problema e inclua fotos
    </div>

    <?php if ($flash): ?>
        <div style="padding: 0 20px 10px;">
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 12px; border-radius: 8px; font-size: 13px;">
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <form action="?page=nova-ocorrencia&action=step3" method="POST" enctype="multipart/form-data" class="form-area" style="display:flex; flex-direction:column; flex:1;">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="titulo">Título resumido</label>
            <input type="text" id="titulo" name="titulo" class="form-input" placeholder="Ex: Buraco grande na via" maxlength="100" required value="<?= sanitize($defaultTitulo) ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="textarea-field" maxlength="300" required placeholder="Descreva os detalhes do problema..."><?= sanitize($defaultDescricao) ?></textarea>
            <div class="char-count"><span id="charCounter"><?= $charLen ?></span>/300</div>
        </div>

        <div class="form-group">
            <label class="form-label">Mídias (opcional)</label>
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Adicione fotos ou vídeos (máx 3 mídias)</div>
            
            <div class="media-row" id="mediaRow">
                <div id="mediaPreviews" style="display:flex; gap:10px; flex-wrap:wrap;"></div>
                <label class="media-add" id="mediaAddBtn">
                    <span class="cam">📷</span>
                    <span id="mediaAddLabel">Adicionar</span>
                    <input type="file" name="midias[]" id="midiasInput" class="media-input" accept="image/*,video/mp4" multiple style="display: none;">
                </label>
            </div>
        </div>

        <div class="bottom-action">
            <button type="submit" class="btn-primary">Enviar ocorrência</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const desc = document.getElementById('descricao');
    const counter = document.getElementById('charCounter');
    if (desc && counter) {
        desc.addEventListener('input', function() {
            counter.textContent = this.value.length;
        });
    }

    const fileInput = document.getElementById('midiasInput');
    const previewsContainer = document.getElementById('mediaPreviews');
    const mediaAddLabel = document.getElementById('mediaAddLabel');

    if (fileInput && previewsContainer) {
        fileInput.addEventListener('change', function() {
            previewsContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                const filesArr = Array.from(this.files).slice(0, 3);
                filesArr.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const thumb = document.createElement('div');
                        thumb.className = 'media-thumb';
                        thumb.innerHTML = `<img src="${e.target.result}"><div class="media-remove" onclick="removeMedia(${index})">✕</div>`;
                        previewsContainer.appendChild(thumb);
                    }
                    reader.readAsDataURL(file);
                });
                if (mediaAddLabel) mediaAddLabel.textContent = 'Adicionar mais';
            } else {
                if (mediaAddLabel) mediaAddLabel.textContent = 'Adicionar';
            }
        });
    }
});

function removeMedia(index) {
    const fileInput = document.getElementById('midiasInput');
    const previewsContainer = document.getElementById('mediaPreviews');
    if (fileInput) {
        fileInput.value = '';
        if (previewsContainer) previewsContainer.innerHTML = '';
    }
}
</script>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
