<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
$erros = $erros ?? $errors ?? [];
$dados = $dados ?? $data ?? [];
?>
<div class="top-bar">
    <a href="?page=auth/login" class="btn-icon" title="Voltar">←</a>
    <span class="top-bar-title">Criar Conta</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($erros)): ?>
        <div class="flash-message flash-error" style="padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; background: #FFEBEE; color: #C62828; border: 1px solid #EF9A9A;">
            <?php foreach ($erros as $chave => $err): ?>
                <p style="margin-bottom: 4px; font-weight: 600;">⚠️ <?= sanitize($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Preencha seus dados para se cadastrar</p>

    <form action="?page=auth/cadastro" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" class="form-input" placeholder="Ex: João da Silva" value="<?= sanitize($dados['nome'] ?? '') ?>" required>
            <?php if (!empty($erros['nome'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;"><?= sanitize($erros['nome']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" class="form-input" data-mask="cpf" placeholder="000.000.000-00" value="<?= sanitize($dados['cpf'] ?? '') ?>" required>
            <?php if (!empty($erros['cpf'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;">⚠️ <?= sanitize($erros['cpf']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="telefone">Telefone / WhatsApp</label>
            <input type="text" id="telefone" name="telefone" class="form-input" data-mask="telefone" placeholder="(83) 90000-0000" value="<?= sanitize($dados['telefone'] ?? '') ?>" required>
            <?php if (!empty($erros['telefone'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;">⚠️ <?= sanitize($erros['telefone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="seuemail@dominio.com" value="<?= sanitize($dados['email'] ?? '') ?>" required>
            <?php if (!empty($erros['email'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;">⚠️ <?= sanitize($erros['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="senha_cadastro">Senha</label>
            <div class="input-password-wrapper">
                <input type="password" id="senha_cadastro" name="senha" class="form-input" placeholder="Mínimo 8 caracteres" required>
                <button type="button" class="toggle-password-btn" data-target="senha_cadastro">👁️</button>
            </div>
            <!-- Indicador de força de senha -->
            <div style="margin-top: 8px;">
                <div style="height: 4px; width: 100%; background: var(--gray-200); border-radius: 2px; overflow: hidden;">
                    <div id="forca_senha_meter" style="height: 100%; width: 0%; transition: all 0.3s ease;"></div>
                </div>
                <div id="forca_senha_texto" style="font-size: 11px; margin-top: 4px; font-weight: 600;"></div>
            </div>
            <?php if (!empty($erros['senha'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;"><?= sanitize($erros['senha']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="confirmar_senha">Confirmar Senha</label>
            <div class="input-password-wrapper">
                <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-input" placeholder="Digite a senha novamente" required>
                <button type="button" class="toggle-password-btn" data-target="confirmar_senha">👁️</button>
            </div>
            <?php if (!empty($erros['confirmar_senha'])): ?>
                <div class="input-error-msg" style="color: #d32f2f; font-size: 12px; margin-top: 4px; font-weight: 600;"><?= sanitize($erros['confirmar_senha']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group" style="display: flex; align-items: flex-start; gap: 10px;">
            <input type="checkbox" id="lgpd" name="lgpd" style="margin-top: 3px; width: 18px; height: 18px;" required>
            <label for="lgpd" style="font-size: 13px; color: var(--text-muted);">
                Li e aceito os <a href="#" style="color: var(--green);">Termos de Uso</a> e autorizo o tratamento de dados conforme a LGPD.
            </label>
        </div>
        <?php if (!empty($erros['lgpd'])): ?>
            <div class="input-error-msg" style="margin-top: -10px; margin-bottom: 16px; color: #d32f2f; font-size: 12px; font-weight: 600;"><?= sanitize($erros['lgpd']) ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Criar conta</button>

        <div style="text-align: center; margin-top: 20px; font-size: 14px;">
            <span style="color: var(--text-muted);">Já tem uma conta?</span>
            <a href="?page=auth/login" style="color: var(--green); font-weight: 700; text-decoration: none;">Entrar</a>
        </div>
    </form>
</div>
<?php
require_once __DIR__ . '/../layout/footer.php';
?>
