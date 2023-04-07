<?php

declare(strict_types=1);

use function Http\Response\send;
use EcPhp\CasLib\Utils\Uri;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';
// require_once __DIR__ . '/includes/middleware/proxy.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/includes/services/cas.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/includes/services/serverRequest.php';

send(
  $casClient
    ->login(
      $serverRequest,
      ['renew' => true] + Uri::getParams($serverRequest->getUri())
    )
);
exit;
