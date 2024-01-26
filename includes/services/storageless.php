<?php

declare(strict_types=1);

use Lcobucci\JWT\Signer\Key\InMemory;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Http\Configuration as StoragelessConfig;
use Lcobucci\JWT\Configuration as JwtConfig;
use Lcobucci\JWT\Signer;

require_once __DIR__ . '/../../vendor/autoload.php';

return new SessionMiddleware(
  new StoragelessConfig(
      JwtConfig::forSymmetricSigner(
          new Signer\Hmac\Sha256(),
          InMemory::base64Encoded('OpcMuKmoxkhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q='),
      )
  )
);
