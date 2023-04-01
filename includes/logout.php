<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/middleware/authenticate.php';

use EcPhp\CasLib\Utils\Uri;
use function Http\Response\send;

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/services/cas.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/services/serverRequest.php';

session_destroy();

send(
  $casClient
    ->logout(
      $serverRequest,
      Uri::getParams($serverRequest->getUri()
    )
  )
);
