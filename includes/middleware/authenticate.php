<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../services/session.php';

use EcPhp\CasLib\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/../services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/../services/cas.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/../services/logger.php';

$logger->info('CAS authentication middleware enabled...');

try {
  // We cannot use the Response here.
  // TODO: Make it a proper middleware interface one day.
  $_SESSION['user'] = $casClient->authenticate($serverRequest);
} catch (Throwable $exception) {
  return;
}

// Redirect the user to the same page without ticket parameter.
$redirect = (string) Uri::removeParams(
    $serverRequest->getUri(),
    'ticket'
);

$logger->info('CAS authentication successful, redirecting to url without ticket parameter...');

// We cannot use the Response here.
// TODO: Make it a proper middleware interface one day.
send(new Response(302, ['Location' => $redirect]));
?>
