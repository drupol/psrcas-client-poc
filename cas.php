<?php
/**
 * @file
 * cas.php
 */
declare(strict_types=1);

require_once 'vendor/autoload.php';

use drupol\psrcas\Cas;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use drupol\psrcas\Configuration\Properties;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Create a configuration object.
// Here you should pass an array containing the configuration.
// If you prefer, you can move the configuration in a JSON file and decode it here into an array.
$properties = new Properties(json_decode(
  file_get_contents(__DIR__ . '/psrcas-config.json'),
  true
));

// The PSR-17 HTTP factory.
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

// The PSR-3 logger.
// Make sure the file has "psrcas.log" the proper permissions.
$psr3Logger = new Logger('psrcas', [new StreamHandler(__DIR__ . '/psrcas.log')]);

// The PSR-6 cache.
$psr6Cache = new FilesystemAdapter('', 0, sys_get_temp_dir());

// The server request.
$serverRequest = include 'serverRequest.php';

// Optionally start the session.
session_start();

// The CAS protocol.
$cas = new Cas(
  $serverRequest,
  $properties,
  $psr18Client,
  $psr17Factory,
  $psr17Factory,
  $psr17Factory,
  $psr17Factory,
  $psr6Cache,
  $psr3Logger
);

return $cas;