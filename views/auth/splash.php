<?php
require_once __DIR__ . '/../layout/header.php';
?>
<div class="splash">
    <div class="splash-logo-area">
        <!-- City icon SVG -->
        <svg class="splash-icon" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="50" width="24" height="50" rx="3" fill="#1B6B35"/>
            <rect x="48" y="30" width="24" height="70" rx="3" fill="#2E8B4A"/>
            <rect x="76" y="42" width="24" height="58" rx="3" fill="#1B6B35"/>
            <rect x="26" y="58" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="26" y="70" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="35" y="58" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="35" y="70" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="55" y="40" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="55" y="53" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="55" y="66" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="64" y="40" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="64" y="53" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="83" y="52" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="83" y="65" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <rect x="92" y="52" width="5" height="7" rx="1" fill="white" opacity="0.7"/>
            <!-- Map pin -->
            <circle cx="60" cy="16" r="10" fill="#43A047"/>
            <path d="M60 10a6 6 0 0 1 6 6c0 4-6 12-6 12S54 20 54 16a6 6 0 0 1 6-6z" fill="#1B6B35"/>
            <circle cx="60" cy="16" r="3" fill="white"/>
        </svg>
        <div>
            <div class="brand-name">Cidade<span>Transparente</span></div>
        </div>
        <div class="brand-tagline">Sua cidade melhor, com a sua participação.</div>
    </div>

    <!-- City illustration SVG -->
    <svg class="splash-illustration" viewBox="0 0 320 180" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Sky -->
        <rect width="320" height="180" fill="#E8F5E9"/>
        <!-- Ground -->
        <rect y="130" width="320" height="50" fill="#A5D6A7"/>
        <!-- Buildings bg -->
        <rect x="10" y="60" width="40" height="90" rx="3" fill="#81C784"/>
        <rect x="60" y="40" width="50" height="110" rx="3" fill="#66BB6A"/>
        <rect x="120" y="70" width="35" height="80" rx="3" fill="#81C784"/>
        <rect x="170" y="50" width="45" height="100" rx="3" fill="#4CAF50"/>
        <rect x="225" y="65" width="40" height="85" rx="3" fill="#66BB6A"/>
        <rect x="275" y="55" width="35" height="95" rx="3" fill="#81C784"/>
        <!-- Windows -->
        <rect x="18" y="70" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <rect x="34" y="70" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <rect x="18" y="85" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <rect x="68" y="50" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <rect x="80" y="50" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <rect x="95" y="50" width="8" height="8" rx="1" fill="white" opacity="0.6"/>
        <!-- Trees -->
        <ellipse cx="50" cy="130" rx="18" ry="22" fill="#388E3C"/>
        <rect x="47" y="148" width="6" height="12" fill="#5D4037"/>
        <ellipse cx="150" cy="128" rx="20" ry="24" fill="#2E7D32"/>
        <rect x="147" y="148" width="6" height="12" fill="#5D4037"/>
        <ellipse cx="255" cy="130" rx="16" ry="20" fill="#388E3C"/>
        <rect x="252" y="148" width="6" height="12" fill="#5D4037"/>
        <!-- Road -->
        <rect y="145" width="320" height="15" fill="#BDBDBD"/>
        <rect x="145" y="151" width="30" height="3" rx="1.5" fill="white" opacity="0.8"/>
    </svg>

    <div class="splash-actions">
        <a href="?page=auth/login" class="btn-primary">Entrar</a>
        <a href="?page=auth/cadastro" class="btn-outline">Criar conta</a>
        <div class="govbr-badge">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <circle cx="7" cy="7" r="6" stroke="#43A047" stroke-width="1.5"/>
                <path d="M4 7L6 9L10 5" stroke="#43A047" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Acesso seguro com <span class="govbr-logo">gov<span class="govbr-br">.br</span></span>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../layout/footer.php';
?>
