<?php declare(strict_types=1);
use function Http\Response\send;
use PSR7Sessions\Storageless\Session\SessionInterface;

require_once __DIR__  . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';

/** @var \EcPhp\CasLib\Contract\Configuration\PropertiesInterface $properties */
$properties = include __DIR__ . '/includes/services/properties.php';

/** @var \loophp\psr17\Psr17Interface $psr17 */
$psr17 = include __DIR__ . '/includes/services/psr17.php';

/** @var \Twig\Environment $twig */
$twig = include __DIR__ . '/includes/services/twig.php';

/** @var \PSR7Sessions\Storageless\Service\StoragelessManager $storageless */
$storageless = include __DIR__ . '/includes/services/storageless.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/includes/services/logger.php';

/** @var SessionInterface $session */
$session = include __DIR__ . '/includes/services/session.php';

$simple = $twig->render(
  'simple.twig',
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
    $psr17->createStream($simple)
  );

send(
  $storageless
  ->handle(
    $serverRequest,
    $response,
    static function (SessionInterface $session) use ($logger): SessionInterface {
      $logger->info('Refreshing the session...');
      return $session;
    }
  )
);
