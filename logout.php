<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use EcPhp\CasLib\Utils\Uri;
use function Http\Response\send;
use PSR7Sessions\Storageless\Session\SessionInterface;

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/includes/services/cas.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/includes/services/serverRequest.php';

/** @var \PSR7Sessions\Storageless\Service\StoragelessManager $storageless */
$storageless = include __DIR__ . '/includes/services/storageless.php';

$response = $casClient
  ->logout(
    $serverRequest,
    Uri::getParams($serverRequest->getUri()
  )
);

send(
  $storageless->handle(
    $serverRequest,
    $response,
    static function (SessionInterface $session): void {
      $session->clear();
    }
  )
);
exit;
