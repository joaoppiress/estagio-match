<?php
$title = $title ?? app_config('name');
$isDebug = (bool) app_config('debug', false);
?>
<!doctype html>
<html lang="pt-BR" data-theme="light" data-contrast="normal" data-sw="<?= e(base_url('sw.js')) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Plataforma inteligente de estágios: recomendação por perfil, candidatura rápida e reputação bidirecional.">
    <title><?= e($title) ?></title>
    <link rel="manifest" href="<?= e(base_url('manifest.webmanifest')) ?>">
    <link rel="icon" href="<?= e(asset_url('icons/icon.svg')) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= e(asset_url('icons/icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
    <noscript><style>[data-reveal]{opacity:1!important;transform:none!important}</style></noscript>
    <script src="<?= e(asset_url('js/theme-init.js')) ?>"></script>
</head>
<body>
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <?php require BASE_PATH . '/app/Views/partials/a11y_bar.php'; ?>
    <?php require BASE_PATH . '/app/Views/partials/alerts.php'; ?>

    <?= $content ?>

    <!-- VLibras — tradutor de Libras (gov.br) -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js" onload="window.__initVLibras&&window.__initVLibras()"></script>
    <script>
        window.__initVLibras = function () {
            try {
                if (window.VLibras && !window.__vlibrasLoaded) {
                    new window.VLibras.Widget('https://vlibras.gov.br/app');
                    window.__vlibrasLoaded = true;
                }
            } catch (e) { /* widget indisponível */ }
        };
        // fallback caso o onload já tenha disparado
        if (window.VLibras) { window.__initVLibras(); }
    </script>

    <script src="<?= e(asset_url('js/app.js')) ?>" defer></script>
</body>
</html>
