<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use function Http\Response\send;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include __DIR__ . '/cas.php';

$service = $_GET['service'];

send($casClient->login(['service' => $service, 'renew' => true]));
