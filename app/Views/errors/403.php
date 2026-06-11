<?php $title = 'Acesso restrito'; ?>
<!doctype html>
<html lang="pt-BR" data-theme="light" data-contrast="normal">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
    <script src="<?= e(asset_url('js/theme-init.js')) ?>"></script>
</head>
<body>
    <main class="empty-state">
        <div class="logo-icon">EM</div>
        <h1>Acesso restrito</h1>
        <p>Seu perfil não tem permissão para acessar esta área.</p>
        <a class="btn btn-primary" href="<?= e(route_url('dashboard')) ?>">Ir para o painel</a>
    </main>
</body>
</html>

