<?php

require_once '../vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;
use drupol\psrcas\Cas;

use function Http\Response\send;

include 'authentication.php';

$name = 'stranger';
if (null !== $_SESSION['user'] ?? null) {
    $name = $user['serviceResponse']['authenticationSuccess']['user'] ?? $name;
}
?>
<!doctype html>

<html lang="en">
<head>
    <title>PSR CAS demo site</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>
</head>
<body>
<nav class="nav nav-pills nav-justified">
        <a class="nav-item nav-link" href="index.php">Index</a>
        <a class="nav-item nav-link" href="restricted.php">Restricted</a>
        <a class="nav-item nav-link" href="proxy.php">Proxy</a>
        <a class="nav-item nav-link" href="login.php?service=<?php print $serverRequest->getUri(); ?>">Login</a>
        <a class="nav-item nav-link" href="logout.php?service=<?php print $serverRequest->getUri(); ?>">Logout</a>
</nav>

<div class="container-fluid">
    <h1>Simple page<br/>
        <small>
            Hi there <?php print $name; ?> !
        </small>
    </h1>
    <p>
        This page is accessible by anonymous and authenticated users.
    </p>

    <h2>CAS configuration dump</h2>
    <?php dump($casClient->getProperties()); ?>

    <h2>PHP Session dump</h2>
    <?php dump($_SESSION); ?>
</div>
</body>
</html>
