<?php

declare(strict_types=1);

use PSR7Sessions\Storageless\Service\StoragelessManager;
use Lcobucci\JWT\Signer\Key\InMemory;

require_once __DIR__ . '/../../vendor/autoload.php';

return StoragelessManager::fromSymmetricKeyDefaults(
  InMemory::plainText('mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw='),
  120,
  60
);
