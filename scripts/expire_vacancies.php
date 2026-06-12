<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$expired = (new App\Models\Vacancy())->expireOverdue();

echo 'Expired vacancies: ' . $expired . PHP_EOL;
