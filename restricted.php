<?php

declare(strict_types=1);

use function Http\Response\send;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';

/** @var \EcPhp\CasLib\Contract\Configuration\PropertiesInterface $properties */
$properties = include __DIR__ . '/includes/services/properties.php';

/** @var \loophp\psr17\Psr17Interface $psr17 */
$psr17 = include __DIR__ . '/includes/services/psr17.php';

/** @var \Twig\Environment $twig */
$twig = include __DIR__ . '/includes/services/twig.php';

/** @var \PSR7Sessions\Storageless\Service\SessionStorage $storageless */
$storageless = include __DIR__ . '/includes/services/storageless.php';

/** @var \PSR7Sessions\Storageless\Session\SessionInterface $session */
$session = include __DIR__ . '/includes/services/session.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/includes/services/logger.php';

if (false === $session->has('user')) {
  include __DIR__ . '/login.php';
  exit;
}

$user = $session->get('user', []);

$name = $user['serviceResponse']['authenticationSuccess']['user'];
$pgt = $user['serviceResponse']['authenticationSuccess']['proxyGrantingTicket'] ?? null;

if (isset($_POST['pgt'], $_POST['targetService'])) {
    $targetService = $_POST['targetService'];
    $pgt = $_POST['pgt'];

    if (null !== $response = $casClient->requestProxyTicket($_POST)) {
        $body = json_decode((string) $response->getBody(), true);
        $proxyTicket = $body['serviceResponse']['proxySuccess']['proxyTicket'] ?? null;
        $linkToTargetService = htmlentities(sprintf('%s?ticket=%s', $targetService, $proxyTicket));
        $curlCommandLine = htmlentities(sprintf('curl -b cookie.txt -c cookie.txt -v -L -k "%s"', $linkToTargetService));
    }
}

$restricted = $twig->render(
  'restricted.twig',
  [
    'name' => $session->get('user')['serviceResponse']['authenticationSuccess']['user'] ?? 'anonymous',
    'session' => (array) $session->jsonserialize(),
    'properties' => $properties,
    'service' => $serverRequest->getUri(),
  ]
);

$response = $psr17
  ->createResponse()
  ->withBody(
    $psr17->createStream($restricted)
  );

$logger->info('Refreshing the session...');

send(
  $storageless->withSession($response, $session)
);
