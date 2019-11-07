<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use drupol\psrcas\Cas;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use drupol\psrcas\Configuration\Properties;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Create a configuration object.
$properties = new Properties(json_decode(
    file_get_contents(__DIR__ . '/psrcas-config.json'),
    true
));

// The PSR-17 HTTP factories factory.
$psr17Factory = new Psr17Factory();

// The PSR-18 HTTP client.
$psr18Client = new Psr18Client(
    new NativeHttpClient(
      [
          'verify_host' => false, // We disable SSL host verification.
          'verify_peer' => false, // We disable SSL peer verification.
      ]
  ),
    $psr17Factory,
    $psr17Factory
);

// The PSR3 logger. Make sure the file has the proper permissions.
$psr3Logger = new Logger('psrcas', [new StreamHandler(__DIR__ . '/psrcas.log')]);

// The PSR-6 cache.
$psr6Cache = new FilesystemAdapter('', 0, sys_get_temp_dir());

// Instantiate ANY PSR-17 factory implementations.

$creator = new ServerRequestCreator(
  $psr17Factory, // ServerRequestFactory
  $psr17Factory, // UriFactory
  $psr17Factory, // UploadedFileFactory
  $psr17Factory  // StreamFactory
);

session_start();

// The CAS protocol.
return new Cas(
    $creator->fromGlobals(),
    $properties,
    $psr18Client,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr6Cache,
    $psr3Logger
);
