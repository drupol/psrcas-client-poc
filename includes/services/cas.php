<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use EcPhp\CasLib\Cas;
use EcPhp\CasLib\Configuration\Properties;
use EcPhp\CasLib\Response\CasResponseBuilder;
use loophp\psr17\Psr17;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Webclient\Extension\Log\Client;

/** @var Properties $properties */
$properties = include __DIR__ . '/properties.php';

/** @var \Psr\Log\LoggerInterface $logger */
$logger = include __DIR__ . '/logger.php';

// The PSR-17 HTTP factory.
$psr17Factory = new Psr17Factory();

// The PSR-18 HTTP client.
$httpClient = new Client(
  new Psr18Client(
    HttpClient::create(
      [
        'verify_host' => false, // We disable SSL host verification.
        'verify_peer' => false, // We disable SSL peer verification.
      ]
    ),
    $psr17Factory,
    $psr17Factory
  ),
  $logger
);

// The PSR-6 cache.
$psr6Cache = new FilesystemAdapter('', 0, sys_get_temp_dir());

// The CAS protocol.
return new Cas(
  $properties,
  $httpClient,
  new Psr17($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory),
  $psr6Cache,
  new CasResponseBuilder()
);
