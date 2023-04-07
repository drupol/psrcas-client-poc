<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use loophp\psr17\Psr17;

$psr17Factory = new Psr17Factory();

return new Psr17(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);
