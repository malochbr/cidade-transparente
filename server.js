const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');

const PORT = 8000;

const mimeTypes = {
    '.html': 'text/html; charset=utf-8',
    '.css': 'text/css',
    '.js': 'text/javascript',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.gif': 'image/gif',
    '.svg': 'image/svg+xml',
    '.ico': 'image/x-icon',
    '.mp4': 'video/mp4'
};

const server = http.createServer((req, res) => {
    const parsedUrl = url.parse(req.url, true);
    let pathname = parsedUrl.pathname;

    // Serve static files (public, cidade-transparente.html, etc.)
    if (pathname.startsWith('/public/') || pathname.endsWith('.html') || pathname.endsWith('.css') || pathname.endsWith('.js')) {
        const targetFile = pathname === '/' ? 'cidade-transparente.html' : pathname.replace(/^\//, '');
        const filePath = path.join(__dirname, targetFile);
        const ext = path.extname(filePath).toLowerCase();
        const contentType = mimeTypes[ext] || 'text/html; charset=utf-8';

        if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
            fs.readFile(filePath, (err, data) => {
                if (err) {
                    res.writeHead(500, { 'Content-Type': 'text/plain' });
                    res.end('Erro ao ler arquivo');
                } else {
                    res.writeHead(200, { 'Content-Type': contentType });
                    res.end(data);
                }
            });
            return;
        }
    }

    // Default root without query parameter -> serve cidade-transparente.html showcase
    if (pathname === '/' && !parsedUrl.query.page) {
        const filePath = path.join(__dirname, 'cidade-transparente.html');
        if (fs.existsSync(filePath)) {
            fs.readFile(filePath, (err, data) => {
                if (!err) {
                    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
                    res.end(data);
                    return;
                }
            });
            return;
        }
    }

    const page = parsedUrl.query.page || 'auth/splash';
    const id = parsedUrl.query.id || '12345';

    function renderPage(title, content, activeNav = 'home') {
        const nav = `
        <div class="bottom-nav">
            <a href="?page=home" class="nav-item ${activeNav === 'home' ? 'active' : ''}">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 0v9h4v-9m-4 0H6m9 9v-9m0 0l2 2"/></svg>
                <span>Início</span>
            </a>
            <a href="?page=ocorrencias" class="nav-item ${activeNav === 'ocorrencias' ? 'active' : ''}">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Ocorrências</span>
            </a>
            <a href="?page=nova-ocorrencia" class="nav-center-btn">+</a>
            <a href="?page=painel" class="nav-item ${activeNav === 'painel' ? 'active' : ''}">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Painel</span>
            </a>
            <a href="?page=perfil" class="nav-item ${activeNav === 'perfil' ? 'active' : ''}">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Perfil</span>
            </a>
        </div>`;

        return `<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>${title} — Cidade Transparente</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<div class="phone-container">
    ${content}
    ${page.startsWith('auth/') ? '' : nav}
</div>
<script src="/public/js/app.js"></script>
</body>
</html>`;
    }

    let bodyHtml = '';

    if (page === 'auth/splash') {
        bodyHtml = renderPage('Splash', `
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
        </div>`);
    } else if (page === 'auth/login') {
        bodyHtml = renderPage('Login', `
        <div class="screen-body">
            <div class="back-header">
                <a href="?page=auth/splash" class="back-btn">←</a>
            </div>
            <div class="screen-title-area">
                <div class="screen-title">Entrar</div>
                <div class="screen-subtitle">Acesse sua conta para continuar</div>
            </div>
            <div class="tab-bar">
                <div class="tab active">CPF e Senha</div>
                <div class="tab" style="opacity:0.6; cursor:not-allowed;">Entrar com gov.br</div>
            </div>
            <div class="form-area">
                <div class="form-group">
                    <label class="form-label">CPF</label>
                    <input type="text" class="form-input" data-mask="cpf" placeholder="000.000.000-00" value="111.111.111-11" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="senha" class="form-input" value="Teste@123" required>
                        <span class="input-eye" onclick="const s=document.getElementById('senha'); s.type=s.type==='password'?'text':'password';">👁</span>
                    </div>
                </div>
                <a href="?page=auth/recuperar-senha" class="forgot-link">Esqueceu sua senha?</a>
                <a href="?page=home" class="btn-primary">Entrar</a>
                <div class="login-footer">
                    Não tem uma conta? <a href="?page=auth/cadastro">Criar conta</a>
                </div>
                <div class="lgpd-badge">
                    <span class="shield">🛡️</span>
                    <p>Seus dados estão protegidos conforme a LGPD.</p>
                </div>
            </div>
        </div>`);
    } else if (page === 'home') {
        bodyHtml = renderPage('Home', `
        <div class="screen-body">
            <div class="top-bar">
                <span class="menu-icon">☰</span>
                <div class="top-bar-title">
                    Olá, Paulo
                    <small>Bem-vindo de volta!</small>
                </div>
                <a href="?page=notificacoes" class="notif-btn">
                    🔔
                    <span class="notif-badge">3</span>
                </a>
            </div>

            <a href="?page=nova-ocorrencia" class="cta-card">
                <div class="cta-text">
                    <h3>Nova ocorrência</h3>
                    <p>Registre um problema na sua cidade</p>
                </div>
                <div class="cta-plus">+</div>
            </a>

            <div style="display:flex; justify-content:space-between; align-items:center; padding:0 20px 10px;">
                <span style="font-size:15px; font-weight:700;">Minhas ocorrências</span>
                <a href="?page=ocorrencias" style="font-size:13px; color:var(--green); font-weight:600; text-decoration:none;">Ver todas</a>
            </div>

            <a href="?page=ocorrencia/detalhe&id=12345" class="occurrence-card">
                <div class="occ-info">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                        <span class="occ-id">#12345</span>
                        <span class="badge badge-orange">Em andamento</span>
                    </div>
                    <div class="occ-title">Buraco na via</div>
                    <div class="occ-addr">Rua das Flores, 123 – Centro</div>
                    <div class="occ-time">🕙 Hoje, 10:30</div>
                </div>
                <span class="occ-arrow">›</span>
            </a>

            <a href="?page=ocorrencia/detalhe&id=12344" class="occurrence-card">
                <div class="occ-info">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                        <span class="occ-id">#12344</span>
                        <span class="badge badge-blue">Encaminhada</span>
                    </div>
                    <div class="occ-title">Iluminação pública</div>
                    <div class="occ-addr">Av. Brasil, 456 – Centro</div>
                    <div class="occ-time">🕙 Ontem, 18:45</div>
                </div>
                <span class="occ-arrow">›</span>
            </a>

            <div style="display:flex; justify-content:space-between; align-items:center; padding:4px 20px 10px;">
                <span style="font-size:15px; font-weight:700;">Painel público</span>
                <a href="?page=painel" style="font-size:13px; color:var(--green); font-weight:600; text-decoration:none;">Ver painel</a>
            </div>
            <a href="?page=painel" style="margin:4px 16px 16px; background:var(--gray-50); border:1px solid var(--border); border-radius:12px; padding:14px 16px; display:flex; align-items:center; justify-content:space-between; text-decoration:none; color:inherit;">
                <p style="font-size:12px; color:var(--text-muted); line-height:1.4; flex:1;">Acompanhe as ocorrências da sua região e ajude a transformar a cidade.</p>
                <span style="font-size:28px;">📈</span>
            </a>
        </div>`, 'home');
    } else if (page === 'nova-ocorrencia' && parsedUrl.query.step !== '2' && parsedUrl.query.step !== '3') {
        bodyHtml = renderPage('Nova ocorrência – Categoria', `
        <div class="screen-body">
            <div class="page-header">
                <a href="?page=home" class="back-btn">←</a>
                <div class="page-header-info">
                    <h2>Nova ocorrência</h2>
                    <p>Informe os detalhes do problema</p>
                </div>
            </div>
            <div class="stepper">
                <div class="step active"><div class="step-num">1</div><span class="step-label">Categoria</span></div>
                <div class="step-divider"></div>
                <div class="step inactive"><div class="step-num">2</div><span class="step-label">Localização</span></div>
                <div class="step-divider"></div>
                <div class="step inactive"><div class="step-num">3</div><span class="step-label">Detalhes</span></div>
            </div>
            <div class="section-label">
                <strong>Categoria</strong>
                Selecione o tipo de problema
            </div>
            <div class="category-grid">
                <div class="category-card selected" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">⚠️</span><span class="cat-label">Buraco na via</span></div>
                <div class="category-card" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">💡</span><span class="cat-label">Iluminação pública</span></div>
                <div class="category-card" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">💧</span><span class="cat-label">Alagamento</span></div>
                <div class="category-card" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">🌿</span><span class="cat-label">Terreno baldio</span></div>
                <div class="category-card" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">🗑️</span><span class="cat-label">Limpeza urbana</span></div>
                <div class="category-card" onclick="document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected')); this.classList.add('selected');"><span class="cat-icon">•••</span><span class="cat-label">Outros</span></div>
            </div>
            <div class="bottom-action">
                <a href="?page=nova-ocorrencia&step=2" class="btn-primary">Próximo</a>
            </div>
        </div>`, 'ocorrencias');
    } else if (page === 'nova-ocorrencia&step=2' || parsedUrl.query.step === '2') {
        bodyHtml = renderPage('Nova ocorrência – Localização', `
        <div class="screen-body">
            <div class="page-header">
                <a href="?page=nova-ocorrencia" class="back-btn">←</a>
                <div class="page-header-info">
                    <h2>Nova ocorrência</h2>
                    <p>Informe os detalhes do problema</p>
                </div>
            </div>
            <div class="stepper">
                <div class="step done"><div class="step-num">✓</div><span class="step-label">Categoria</span></div>
                <div class="step-divider done"></div>
                <div class="step active"><div class="step-num">2</div><span class="step-label">Localização</span></div>
                <div class="step-divider"></div>
                <div class="step inactive"><div class="step-num">3</div><span class="step-label">Detalhes</span></div>
            </div>
            <div style="padding: 4px 20px 10px; font-size: 13px; color: var(--text-muted);">
                <strong style="font-size:15px; color:var(--text); font-weight:700; display:block; margin-bottom:2px;">Localização</strong>
                Confirme o local da ocorrência
            </div>
            <div class="map-placeholder">
                <div class="map-grid"></div>
                <svg style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 375 220">
                    <line x1="0" y1="110" x2="375" y2="110" stroke="#fff" stroke-width="8" opacity="0.6"/>
                    <line x1="0" y1="70" x2="375" y2="70" stroke="#fff" stroke-width="5" opacity="0.4"/>
                    <line x1="120" y1="0" x2="120" y2="220" stroke="#fff" stroke-width="6" opacity="0.5"/>
                    <rect x="65" y="15" width="50" height="50" rx="4" fill="rgba(255,255,255,0.3)"/>
                    <rect x="125" y="15" width="120" height="50" rx="4" fill="rgba(255,255,255,0.25)"/>
                </svg>
                <div class="map-pin"><div class="map-pin-head"></div></div>
                <div class="map-user-dot"></div>
            </div>
            <div class="address-card">
                <div>
                    <div class="addr-label">Endereço identificado</div>
                    <div class="addr-main">Rua das Flores, 123 – Centro</div>
                    <div class="addr-sub">Sua cidade – UF</div>
                </div>
                <span class="edit-icon">✏️</span>
            </div>
            <div style="padding:0 20px 16px">
                <a href="?page=nova-ocorrencia&step=3" class="btn-primary">Usar esta localização</a>
                <button class="btn-secondary">Selecionar no mapa</button>
            </div>
        </div>`, 'ocorrencias');
    } else if (page === 'nova-ocorrencia&step=3' || parsedUrl.query.step === '3') {
        bodyHtml = renderPage('Nova ocorrência – Detalhes', `
        <div class="screen-body">
            <div class="page-header">
                <a href="?page=nova-ocorrencia&step=2" class="back-btn">←</a>
                <div class="page-header-info">
                    <h2>Nova ocorrência</h2>
                    <p>Informe os detalhes do problema</p>
                </div>
            </div>
            <div class="stepper">
                <div class="step done"><div class="step-num">✓</div><span class="step-label">Categoria</span></div>
                <div class="step-divider done"></div>
                <div class="step done"><div class="step-num">✓</div><span class="step-label">Localização</span></div>
                <div class="step-divider done"></div>
                <div class="step active"><div class="step-num">3</div><span class="step-label">Detalhes</span></div>
            </div>
            <div class="section-label">
                <strong>Detalhes</strong>
                Descreva o problema e inclua fotos
            </div>
            <div class="form-area" style="display:flex; flex-direction:column; flex:1;">
                <div class="form-group">
                    <label class="form-label" for="titulo">Título resumido</label>
                    <input type="text" id="titulo" name="titulo" class="form-input" value="Buraco na via" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="descricao">Descrição</label>
                    <textarea id="descricao" class="textarea-field" maxlength="300" required>Buraco grande na via, dificultando a passagem de veículos e oferecendo risco de acidentes.</textarea>
                    <div class="char-count"><span id="charCounter">92</span>/300</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mídias (opcional)</label>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Adicione fotos ou vídeos (máx 3 mídias)</div>
                    <div class="media-row">
                        <div class="media-thumb">
                            <div style="width:100%;height:100%;background:#555;"></div>
                            <div class="media-remove">✕</div>
                        </div>
                        <div class="media-add">
                            <span class="cam">📷</span>
                            Adicionar
                        </div>
                    </div>
                </div>
                <div class="bottom-action">
                    <a href="?page=ocorrencia/detalhe&id=12345" class="btn-primary">Enviar ocorrência</a>
                </div>
            </div>
        </div>`, 'ocorrencias');
    } else if (page === 'painel') {
        bodyHtml = renderPage('Painel público', `
        <div class="screen-body">
            <div style="padding:14px 20px 6px; display:flex; align-items:center; gap:8px;">
                <span style="font-size:20px">☰</span>
                <span style="font-size:17px; font-weight:800; color:var(--text)">Painel público</span>
            </div>
            <div style="padding:0 20px 8px; font-size:12px; color:var(--text-muted)">Acompanhe as ocorrências da sua região</div>
            <div class="search-bar">
                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" placeholder="Buscar ocorrência...">
                </div>
                <div class="filter-btn">⚙</div>
            </div>
            <div class="chips-row">
                <a href="?page=painel" class="chip active">Todas</a>
                <a href="?page=painel&status=em_andamento" class="chip">Em andamento</a>
                <a href="?page=painel&status=resolvida" class="chip">Resolvidas</a>
                <a href="?page=painel&status=encaminhada" class="chip">Encaminhadas</a>
            </div>
            <a href="?page=ocorrencia/detalhe&id=12345" class="occ-list-card">
                <div class="occ-list-top">
                    <div class="occ-list-title">Buraco na via</div>
                    <span class="badge badge-orange">Em andamento</span>
                </div>
                <div class="occ-list-addr">Rua das Flores, 123 – Centro</div>
                <div class="occ-list-meta">
                    <span>👍 12 apoios</span>
                    <span>💬 3 comentários</span>
                    <span style="margin-left:auto;">Hoje, 10:30</span>
                </div>
            </a>
            <a href="?page=ocorrencia/detalhe&id=12344" class="occ-list-card">
                <div class="occ-list-top">
                    <div class="occ-list-title">Iluminação pública</div>
                    <span class="badge badge-blue">Encaminhada</span>
                </div>
                <div class="occ-list-addr">Av. Brasil, 456 – Centro</div>
                <div class="occ-list-meta">
                    <span>👍 8 apoios</span>
                    <span>💬 1 comentário</span>
                    <span style="margin-left:auto;">Ontem, 18:45</span>
                </div>
            </a>
            <a href="?page=ocorrencia/detalhe&id=12343" class="occ-list-card">
                <div class="occ-list-top">
                    <div class="occ-list-title">Alagamento</div>
                    <span class="badge badge-green">Resolvida</span>
                </div>
                <div class="occ-list-addr">Rua São Paulo, 789 – Centro</div>
                <div class="occ-list-meta">
                    <span>👍 15 apoios</span>
                    <span>💬 5 comentários</span>
                    <span style="margin-left:auto;">15/05/2026</span>
                </div>
            </a>
        </div>`, 'painel');
    } else if (page === 'ocorrencia/detalhe') {
        bodyHtml = renderPage('Detalhe da ocorrência', `
        <div class="screen-body">
            <div class="detail-header">
                <a href="?page=painel" class="back-btn">←</a>
                <span class="detail-title">Ocorrência #${id}</span>
            </div>
            <div class="occ-detail-card">
                <div class="occ-detail-icon">⚠️</div>
                <div class="occ-detail-info">
                    <div class="occ-detail-title">Buraco na via</div>
                    <span class="badge badge-orange" style="display:inline-block; margin:2px 0 4px;">Em andamento</span>
                    <div class="occ-detail-addr">Rua das Flores, 123 – Centro</div>
                    <div class="occ-detail-time">🕙 Hoje, 10:30</div>
                </div>
            </div>
            <div class="detail-tabs">
                <div class="detail-tab">Detalhes</div>
                <div class="detail-tab active">Atualizações</div>
                <div class="detail-tab">Apoios (12)</div>
            </div>
            <div class="timeline">
                <div class="timeline-item"><div class="tl-dot"></div><div class="tl-content"><div class="tl-time">Hoje, 10:30</div><div class="tl-text">Ocorrência registrada pelo cidadão</div></div></div>
                <div class="timeline-item"><div class="tl-dot"></div><div class="tl-content"><div class="tl-time">Hoje, 11:15</div><div class="tl-text">Ocorrência encaminhada para Secretaria de Obras</div></div></div>
                <div class="timeline-item"><div class="tl-dot"></div><div class="tl-content"><div class="tl-time">Hoje, 14:20</div><div class="tl-text">Equipe responsável em análise</div></div></div>
                <div class="timeline-item"><div class="tl-dot"></div><div class="tl-content"><div class="tl-time">Hoje, 16:45</div><div class="tl-text">Serviço em andamento na região</div></div></div>
            </div>
            <div class="support-btn">
                <span class="thumb">👍</span> Apoiar ocorrência
            </div>
        </div>`, 'ocorrencias');
    } else if (page === 'perfil') {
        bodyHtml = renderPage('Meu perfil', `
        <div class="screen-body">
            <div class="profile-top">
                <span style="font-size:20px">☰</span>
                <span class="profile-top-title">Meu perfil</span>
                <a href="?page=perfil/meus-dados" class="settings-icon">⚙️</a>
            </div>
            <div class="profile-card">
                <div class="avatar">👤</div>
                <div>
                    <div class="profile-name">Paulo Sergio</div>
                    <div class="profile-handle">@paulosergio</div>
                    <div class="profile-badges">
                        <div class="profile-badge"><span class="star">⭐</span> Reputação Ótima</div>
                        <div class="profile-badge"><span class="star">⭐</span> 1.250 pontos</div>
                    </div>
                </div>
            </div>
            <div class="menu-list">
                <a href="?page=perfil/meus-dados" class="menu-item">
                    <div class="menu-icon-wrap">👤</div>
                    <span class="menu-item-label">Meus dados</span>
                    <span class="menu-item-arrow">›</span>
                </a>
                <a href="?page=ocorrencias" class="menu-item">
                    <div class="menu-icon-wrap">📋</div>
                    <span class="menu-item-label">Minhas ocorrências</span>
                    <span class="menu-item-arrow">›</span>
                </a>
                <a href="?page=notificacoes" class="menu-item">
                    <div class="menu-icon-wrap">🔔</div>
                    <span class="menu-item-label">Notificações</span>
                    <span class="menu-notif-badge">2</span>
                    <span class="menu-item-arrow">›</span>
                </a>
                <div class="menu-divider"></div>
                <a href="?page=perfil/alterar-senha" class="menu-item">
                    <div class="menu-icon-wrap">🛡️</div>
                    <span class="menu-item-label">Segurança</span>
                    <span class="menu-item-arrow">›</span>
                </a>
                <div class="menu-item">
                    <div class="menu-icon-wrap">❓</div>
                    <span class="menu-item-label">Ajuda e suporte</span>
                    <span class="menu-item-arrow">›</span>
                </div>
                <div class="menu-divider"></div>
                <a href="?page=admin/dashboard" class="menu-item">
                    <div class="menu-icon-wrap" style="background:var(--orange-bg);">⚙️</div>
                    <span class="menu-item-label" style="color:var(--orange); font-weight:700;">Painel Administrativo</span>
                    <span class="menu-item-arrow" style="color:var(--orange);">›</span>
                </a>
                <div class="menu-divider"></div>
                <a href="?page=auth/logout" class="menu-item danger">
                    <div class="menu-icon-wrap" style="background:#FFEBEE">🚪</div>
                    <span class="menu-item-label" style="color:#E53935">Sair</span>
                    <span class="menu-item-arrow" style="color:#E53935">›</span>
                </a>
            </div>
        </div>`, 'perfil');
    } else {
        bodyHtml = renderPage('Detalhes', `
        <div class="screen-body">
            <div style="padding:20px;"><p>Página em carregamento...</p></div>
        </div>`, 'home');
    }

    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(bodyHtml);
});

server.on('error', (err) => {
    console.error('Server error:', err);
});

process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
});

server.listen(PORT, () => {
    console.log(`Cidade Transparente server rodando em http://localhost:${PORT}`);
});

