<?php

require_once '../vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;
use drupol\psrcas\Cas;

use function Http\Response\send;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include 'casloader.php';

if (null === $user = $_SESSION['user'] ?? null) {
    // Try to do an authentication.
    $user = $casClient->authenticate();

    // If the user has been found.
    if (null !== $user) {
        $_SESSION['user'] = $user;

        // Redirect the user to the same page without ticket parameter.
        $redirect = (string) Uri::removeParams(
            $casClient->getServerRequest()->getUri(),
            'ticket'
        );

        $response = new Response(302, ['Location' => $redirect]);

        send($response);
    }
}

if (null !== $user) {
    $name = $user['serviceResponse']['authenticationSuccess']['user'] ?? 'stranger';
}

$_welcome_message = 'Hi there <em>' . $name . '</em> !';

$class = new ReflectionClass(Cas::class);
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PSR CAS Client">
    <meta name="author" content="DIGIT.B.4">

    <title>ECAS - PSR CAS Client</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php if (null !== $user): ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 text-right">
                <button type="button" class="btn btn-warning"
                        onclick="location.href='restricted.php?renew=true';">Renew <strong><?php print $name; ?></strong>
                </button>
            </div>
            <div class="col-xs-2 text-right">
                <button type="button" class="btn btn-danger"
                        onclick="location.href='logout.php';">Logout <strong><?php print $name; ?></strong>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid">
    <h1>Unrestricted access<br/>
        <small>
          <?php print $_welcome_message; ?>
        </small>
    </h1>
    <p>
        This page is not restricted by the configuration of the PSR CAS Client.
        <a href="/restricted.php">Restricted access</a>
    </p>

    <h2>CAS configuration</h2>
  <?php dump($casClient->getProperties()); ?>

    <h2>Configuring the PSR CAS Client</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Comment</th>
            <th>Method</th>
            <th>Parameters</th>
            <th>Return type</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $props = $class->getProperties();

        foreach ($class->getMethods() as $method) {
          if (false === $method->isPublic()) {
            continue;
          }

          $parameters = array_map(function($parameter) {
            return '$' . $parameter;
          }, array_column($method->getParameters(), 'name'));

          printf(
            '<tr>
                        <td><code>%s</code></td>
                        <td class="text-nowrap">$cas-&gt;<strong>%s()</strong>:</td>
                        <td><code>%s</code></td><td></td></td>
                        <td><code>%s</code></td>
                        </tr>',
            trim(nl2br(htmlentities(trim($method->getDocComment())))),
            $method->getName(),
            implode(', ', $parameters),
            $method->getReturnType()
          );
        }
        ?>
        </tbody>
    </table>

    <h2>Server config</h2>
  <?php dump($_SERVER); ?>
</div>
</body>
</html>
