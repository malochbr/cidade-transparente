<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=perfil" class="btn-icon">←</a>
    <span class="top-bar-title">Alterar Senha</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="flash-message flash-error">
            <div>
                <?php foreach ($errors as $err): ?><p>• <?= sanitize($err) ?></p><?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <form action="?page=perfil/alterar-senha" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="senha_atual">Senha Atual</label>
            <div class="input-password-wrapper">
                <input type="password" id="senha_atual" name="senha_atual" class="form-input" required>
                <button type="button" class="toggle-password-btn" data-target="senha_atual">👁️</button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="nova_senha">Nova Senha</label>
            <div class="input-password-wrapper">
                <input type="password" id="nova_senha" name="nova_senha" class="form-input" required>
                <button type="button" class="toggle-password-btn" data-target="nova_senha">👁️</button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="confirmar_senha">Confirmar Nova Senha</label>
            <div class="input-password-wrapper">
                <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-input" required>
                <button type="button" class="toggle-password-btn" data-target="confirmar_senha">👁️</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Atualizar Senha</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
