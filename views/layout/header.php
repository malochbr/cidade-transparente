<?php
/**
 * Header Layout Template
 */
$pageTitle = $pageTitle ?? 'Cidade Transparente';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= sanitize($pageTitle) ?> — Cidade Transparente</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="phone-container">
    <!-- Barra de Status Simulada -->
    <div class="status-bar">
        <span>9:41</span>
        <div class="icons">
            <span>📶</span>
            <span>📡</span>
            <span>🔋</span>
        </div>
    </div>
