<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use EcPhp\CasLib\Configuration\Properties;

// Create a configuration object.
// Here you should pass an array containing the configuration.
// If you prefer, you can move the configuration in a JSON file and decode it here into an array.
return new Properties(
  json_decode(
    file_get_contents(__DIR__ . '/../../config/caslib-config.json'),
    true
  )
);
