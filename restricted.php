<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

include 'authentication.php';

$serverRequest = include 'serverRequest.php';

// Default user name.
$name = 'Anonymous';

if (null === $_SESSION['user'] ?? null) {
    // If the user hasn't been found... Launch the login process.
    $psr7Response = $casClient->login(['service' => (string) $serverRequest->getUri()]);

    // If the login procedure is valid...
    if (null !== $psr7Response) {
        send($psr7Response);
    }
}

if (null !== $user = $_SESSION['user'] ?? null) {
    $name = $user['serviceResponse']['authenticationSuccess']['user'];
    $pgt = $user['serviceResponse']['authenticationSuccess']['proxyGrantingTicket'] ?? null;

    if (isset($_POST['pgt'], $_POST['targetService'])) {
        $targetService = $_POST['targetService'];
        $pgt = $_POST['pgt'];

        if (null !== $response = $casClient->requestProxyTicket($_POST)) {
            $body = json_decode((string) $response->getBody(), true);
            $proxyTicket = $body['serviceResponse']['proxySuccess']['proxyTicket'] ?? null;
            $linkToTargetService = htmlentities(sprintf('%s?ticket=%s', $targetService, $proxyTicket));
            $curlCommandLine = htmlentities(sprintf('curl -v -L -k "%s"', $linkToTargetService));
        }
    }
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

    <h1>Restricted access</h1>
    <h2>
        Hi <?php echo $name; ?> !
    </h2>

    <p>
        This page is only accessible to authenticated users.
    </p>
</div>
</body>
</html>

