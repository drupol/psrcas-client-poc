<?php
/**
 * @file
 * forceAuthentication.php
 */
declare(strict_types=1);

require_once '../vendor/autoload.php';

use function Http\Response\send;

include __DIR__ . '/authentication.php';

$serverRequest = include __DIR__ . '/serverRequest.php';

// If the user hasn't been found... Launch the login process.
if (null === $user = $_SESSION['user'] ?? null) {
  $psr7Response = $casClient->login(['service' => (string) $serverRequest->getUri()]);

  // If the login procedure is valid, redirect the user to the login page.
  if (null !== $psr7Response) {
    send($psr7Response);
  }
}
?>
