<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro no sistema</title>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>
<body>
    <main class="empty-state">
        <div class="logo-icon">EM</div>
        <h1>Não foi possível carregar esta página</h1>
        <p><?= e($message) ?></p>
        <p class="muted">Confira se o MySQL está ligado e se o arquivo <strong>database/schema.sql</strong> foi importado.</p>
        <a class="btn btn-primary" href="<?= e(route_url('home')) ?>">Tentar novamente</a>
    </main>
</body>
</html>

