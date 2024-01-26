<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use EcPhp\CasLib\Utils\Uri;
use Nyholm\Psr7\Response;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Http\Response\send;

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/../services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/../services/cas.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/../services/logger.php';

/** @var \PSR7Sessions\Storageless\Http\SessionMiddleware $storageless */
$storageless = include __DIR__ . '/../services/storageless.php';

$logger->info('CAS authentication middleware enabled...');

try {
  $credentials = $casClient->authenticate($serverRequest);
} catch (Throwable $exception) {
  return;
}

$logger->info('CAS authentication successful, redirecting to url without ticket parameter...');

$logger->info('Saving the session with credentials...');

$handler = fn (array $userData): RequestHandlerInterface => new class($userData) implements RequestHandlerInterface {
  public function __construct(
    private readonly array $userData
  ) {}

  public function handle(ServerRequestInterface $request): ResponseInterface {
    /** @var \PSR7Sessions\Storageless\Session\SessionInterface $session */
    $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
    $session->set('user', $this->userData);

    $response = new Response(
      302,
      [
        'Location' => (string) Uri::removeParams(
          $request->getUri(),
          'ticket'
        )
      ]
    );

    return $response;
  }
};

$response = $storageless->process($serverRequest, $handler($credentials));

send($response);
exit;
