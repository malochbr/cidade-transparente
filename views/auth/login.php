<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
$erros = $erros ?? $errors ?? [];
?>
<div class="screen-body">
    <div class="back-header">
        <a href="?page=auth/splash" class="back-btn" title="Voltar">←</a>
    </div>

    <div class="screen-title-area">
        <div class="screen-title">Entrar</div>
        <div class="screen-subtitle">Acesse sua conta para continuar</div>
    </div>

    <div class="tab-bar">
        <div class="tab active">CPF e Senha</div>
        <div class="tab" style="opacity: 0.6; cursor: not-allowed;" title="Em breve">Entrar com gov.br</div>
    </div>

    <div class="form-area">
        <?php if ($flash): ?>
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 8px;">
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="flash-message flash-error" style="padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 12px; background: #FFEBEE; color: #C62828; border: 1px solid #EF9A9A;">
                <?php foreach ($erros as $err): ?>
                    <p style="margin-bottom: 2px; font-weight: 600;">⚠️ <?= sanitize($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="?page=auth/login" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" class="form-input" data-mask="cpf" placeholder="000.000.000-00" required autofocus value="<?= sanitize($_POST['cpf'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="senha">Senha</label>
                <div class="input-wrapper">
                    <input type="password" id="senha" name="senha" class="form-input" placeholder="Digite sua senha" required>
                    <span class="input-eye" onclick="const s=document.getElementById('senha'); s.type=s.type==='password'?'text':'password';">👁</span>
                </div>
            </div>

            <a href="?page=auth/recuperar-senha" class="forgot-link">Esqueceu sua senha?</a>

            <button type="submit" class="btn-primary">Entrar</button>

            <div class="login-footer">
                Não tem uma conta? <a href="?page=auth/cadastro">Criar conta</a>
            </div>

            <div class="lgpd-badge">
                <span class="shield">🛡️</span>
                <p>Seus dados estão protegidos conforme a LGPD.</p>
            </div>
        </form>
    </div>
</div>
<?php
require_once __DIR__ . '/../layout/footer.php';
?>
