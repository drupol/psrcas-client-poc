<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
//require_once __DIR__ . '/../services/session.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/../services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/../services/cas.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/../services/logger.php';

$logger->info('CAS Proxy callback middleware enabled...');

try {
  // We cannot use the Response here.
  // TODO: Make it a proper middleware interface one day.
  $casClient->handleProxyCallback($serverRequest);
} catch (Throwable $exception) {
  $logger->info('No proxy callback handling on this request...');
}
?>
