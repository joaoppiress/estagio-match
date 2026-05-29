<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

require BASE_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register(BASE_PATH . '/app');

require BASE_PATH . '/app/Core/helpers.php';

if (PHP_SAPI !== 'cli') {
    App\Core\Security::startSession();
}
