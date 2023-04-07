<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use EcPhp\CasLib\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;
use PSR7Sessions\Storageless\Session\SessionInterface;

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/../services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/../services/cas.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/../services/logger.php';

/** @var \PSR7Sessions\Storageless\Service\StoragelessManager $storageless */
$storageless = include __DIR__ . '/../services/storageless.php';

$logger->info('CAS authentication middleware enabled...');

try {
  $credentials = $casClient->authenticate($serverRequest);
} catch (Throwable $exception) {
  return;
}

$logger->info('CAS authentication successful, redirecting to url without ticket parameter...');

$logger->info('Saving the session with credentials...');

$response = $storageless->handle(
  $serverRequest,
  new Response(
    302,
    [
      'Location' => (string) Uri::removeParams(
        $serverRequest->getUri(),
        'ticket'
      )
    ]
  ),
  static function (SessionInterface $session) use ($credentials): SessionInterface {
    $session->set('user', $credentials);

    return $session;
  }
);

send($response);
exit;
