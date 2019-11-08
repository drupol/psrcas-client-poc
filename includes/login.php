<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use function Http\Response\send;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include __DIR__ . '/cas.php';

$serverRequest = include __DIR__ . '/serverRequest.php';

send($casClient->login(['service' => (string) $serverRequest->getUri(), 'renew' => true]));
