<?php

declare(strict_types=1);

require_once './vendor/autoload.php';

use function Http\Response\send;
use Nyholm\Psr7\Factory\Psr17Factory;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include 'cas.php';

// We need this to build the body.
$streamFactory = new Psr17Factory();

$body = '<?xml version="1.0" encoding="utf-8"?><proxySuccess xmlns="http://www.yale.edu/tp/casClient" />';
$body = $streamFactory->createStream($body);

send($casClient->requestProxyCallback()->withBody($body));
