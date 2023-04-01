<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return new Logger('stdout', [new StreamHandler('php://stdout')]);
