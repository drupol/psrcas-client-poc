<?php

declare(strict_types=1);

require_once './vendor/autoload.php';

use function Http\Response\send;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include 'casloader.php';

session_destroy();

send($casClient->logout());
