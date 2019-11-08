<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

include 'authentication.php';

$serverRequest = include 'serverRequest.php';

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
            $curlCommandLine = htmlentities(sprintf('curl -c cookie.txt -b cookie.txt -v -L -k "%s"', $linkToTargetService));
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

<body>
<div class="container-fluid">
    <?php if (null !== $user) { ?>
        <fieldset>
            <legend>Proxy Ticket Granting</legend>

            <form method="POST">
                <div class="form-group">
                    <label for="pgt">Proxy Granting Ticket</label>
                    <input type="text" class="form-control" id="pgt" name="pgt" placeholder="Example input" value="<?php echo $pgt; ?>" readonly="readonly"/>
                </div>
                <div class="form-group">
                    <label for="targetService">Target service</label>
                    <input type="text" class="form-control" id="targetService" name="targetService" placeholder="https://any-url-here/" value="<?php echo $targetService; ?>"/>
                </div>
                <div class="form-group">
                    <input class="form-control btn btn-lg btn-block btn-primary" type="submit" name="newTicket" value="Get a new proxy ticket!" />
                </div>

                <?php if ($proxyTicket) { ?>
                    <div class="form-group">
                        <label for="proxyTicket">Proxy ticket</label>
                        <input type="text" class="form-control" id="proxyTicket" name="proxyTicket" value="<?php echo $proxyTicket; ?>" readonly="readonly"/>
                    </div>
                    <div class="form-group">
                        <label for="linkToTargetService">URL</label>
                        <input type="text" class="form-control" id="linkToTargetService" name="linkToTargetService" value="<?php echo $linkToTargetService; ?>" readonly="readonly"/>
                    </div>
                    <div class="form-group">
                        <label for="curlCommandLine">Proxy ticket</label>
                        <input type="text" class="form-control" id="curlCommandLine" name="curlCommandLine" value="<?php echo $curlCommandLine; ?>" readonly="readonly"/>
                    </div>
                <?php } ?>
            </form>
        </fieldset>
    <?php } ?>
</div>
</body>
