<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use EcPhp\CasLib\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/../services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/../services/cas.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/../services/logger.php';

/** @var \PSR7Sessions\Storageless\Service\SessionStorage $storageless */
$storageless = include __DIR__ . '/../services/storageless.php';

/** @var \PSR7Sessions\Storageless\Session\SessionInterface $session */
$session = include __DIR__ . '/../services/session.php';

$logger->info('CAS authentication middleware enabled...');

try {
  $credentials = $casClient->authenticate($serverRequest);
} catch (Throwable $exception) {
  return;
}

$logger->info('CAS authentication successful, redirecting to url without ticket parameter...');

$logger->info('Saving the session with credentials...');
$session->set('user', $credentials);

$response = $storageless->withSession(
  new Response(
    302,
    [
      'Location' => (string) Uri::removeParams(
        $serverRequest->getUri(),
        'ticket'
      )
    ]
  ),
  $session
);

send($response);
exit;
