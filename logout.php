<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use EcPhp\CasLib\Utils\Uri;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

use function Http\Response\send;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/includes/services/cas.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/includes/services/serverRequest.php';

/** @var \PSR7Sessions\Storageless\Service\StoragelessManager $storageless */
$storageless = include __DIR__ . '/includes/services/storageless.php';

/** @var \PSR7Sessions\Storageless\Service\SessionStorage $session */
$session = include __DIR__ . '/includes/services/session.php';

$response = $casClient
  ->logout(
    $serverRequest,
    Uri::getParams($serverRequest->getUri()
  )
);

$logger->info('Refreshing the session...');

$handler = fn (ResponseInterface $response): RequestHandlerInterface => new class($response) implements RequestHandlerInterface {
  public function __construct(
    private readonly ResponseInterface $response
  ) {}

  public function handle(ServerRequestInterface $request): ResponseInterface {
    /** @var \PSR7Sessions\Storageless\Session\SessionInterface $session */
    $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
    $session->clear();

    return $this->response;
  }
};

$response = $storageless->process($serverRequest, $handler($response));

send($response);
