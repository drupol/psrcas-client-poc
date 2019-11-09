<?php
/**
 * @file
 * authentication.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/serverRequest.php';

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include __DIR__ . '/cas.php';

if (true === isset($_GET['renew']) && $_GET['renew'] === '0') {
    //unset($_SESSION['user']);
}

// Check if a user is in the session.
if (false === isset($_SESSION['user'])) {
  // Try to do an authentication.
  $user = $casClient->authenticate();

  // If the user has been found.
  if (null !== $user) {
    // Save the user in the session.
    $_SESSION['user'] = $user;

    // Redirect the user to the same page without ticket parameter.
    $redirect = (string) Uri::removeParams(
      $serverRequest->getUri(),
      'ticket'
    );

    $response = new Response(302, ['Location' => $redirect]);

    // Emit the response to the client.
    send($response);
  }
}
?>
