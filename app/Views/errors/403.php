<?php $title = 'Acesso restrito'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
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

