<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use function Http\Response\send;
use EcPhp\CasLib\Utils\Uri;

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/services/cas.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/services/serverRequest.php';

send(
  $casClient
    ->login(
      $serverRequest,
      ['renew' => true] + Uri::getParams($serverRequest->getUri())
    )
);
