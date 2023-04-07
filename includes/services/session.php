<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

/** @var \PSR7Sessions\Storageless\Service\StoragelessManager $storageless */
$storageless = include __DIR__ . '/storageless.php';

return $storageless->get($serverRequest);
