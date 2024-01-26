<?php

declare(strict_types=1);

use PSR7Sessions\Storageless\Http\ClientFingerprint\SameOriginRequest;
use Lcobucci\JWT\Signer\Key\InMemory;
use PSR7Sessions\Storageless\Http\Configuration as StoragelessConfig;
use Lcobucci\JWT\Configuration as JwtConfig;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use PSR7Sessions\Storageless\Session\DefaultSessionData;
use PSR7Sessions\Storageless\Session\LazySession;
use PSR7Sessions\Storageless\Session\SessionInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$request = include __DIR__ . '/../services/serverRequest.php';

$storagelessConfig = new StoragelessConfig(
  JwtConfig::forSymmetricSigner(
      new Signer\Hmac\Sha256(),
      InMemory::base64Encoded('OpcMuKmoxkhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q='),
  )
);

$sameOriginRequest = new SameOriginRequest($storagelessConfig->getClientFingerprintConfiguration(), $request);

/** @var array<string, string> $cookies */
$cookies    = $request->getCookieParams();
$cookieName = $storagelessConfig->getCookie()->getName();

if (! isset($cookies[$cookieName])) {
    return null;
}

$cookie = $cookies[$cookieName];
if ($cookie === '') {
    return null;
}

$jwtConfiguration = $storagelessConfig->getJwtConfiguration();
try {
    $token = $jwtConfiguration->parser()->parse($cookie);
} catch (InvalidArgumentException) {
    return null;
}

if (! $token instanceof UnencryptedToken) {
    return null;
}

$constraints = [
    new StrictValidAt($storagelessConfig->getClock()),
    new SignedWith($jwtConfiguration->signer(), $jwtConfiguration->verificationKey()),
    $sameOriginRequest,
];

if (! $jwtConfiguration->validator()->validate($token, ...$constraints)) {
    return null;
}

$sessionContainer = LazySession::fromContainerBuildingCallback(
  function () use ($token): SessionInterface {
    if (! $token) {
      return DefaultSessionData::newEmptySession();
    }

    try {
        return DefaultSessionData::fromDecodedTokenData(
            (object) $token->claims()->get('session-data', new stdClass()),
        );
    } catch (BadMethodCallException) {
        return DefaultSessionData::newEmptySession();
    }
  }
);

return $sessionContainer;
