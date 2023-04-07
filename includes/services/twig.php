<?php

declare(strict_types=1);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

require_once __DIR__ . '/../../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../../templates');
$twig = new Environment(
  $loader,
  [
    'cache' => false,
    'debug' => true,
  ]
);
$twig->addExtension(new DebugExtension());


return $twig;
