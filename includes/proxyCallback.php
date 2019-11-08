<?php
/**
 * @file
 * proxyCallback.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use function Http\Response\send;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include __DIR__ . '/cas.php';

send($casClient->requestProxyCallback());
