<?php $title = $title ?? app_config('name'); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>
<body>
    <?php require BASE_PATH . '/app/Views/partials/alerts.php'; ?>
    <?= $content ?>
    <script src="<?= e(asset_url('js/app.js')) ?>" defer></script>
</body>
</html>

