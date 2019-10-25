<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;

use function Http\Response\send;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

/** @var \drupol\psrcas\Cas $casClient */
$casClient = include 'casloader.php';

// Default user name.
$name = 'Anonymous';

// Get the current request.
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$serverRequest = $creator->fromGlobals();


if (null === $_SESSION['user'] ?? null) {
    // Try to do an authentication.
    $user = $casClient->authenticate();

    // If the user has been found.
    if (null !== $user) {
        $_SESSION['user'] = $user;

        // Redirect the user to the same page without ticket parameter.
        $redirect = (string) Uri::removeParams(
            $serverRequest->getUri(),
            'ticket'
        );

        $response = new Response(302, ['Location' => $redirect]);

        send($response);
    } else {
        // If the user hasn't been found... Launch the login process.
        $psr7Response = $casClient->login(['service' => (string) $serverRequest->getUri(), 'renew' => true]);

        // If the login procedure is valid...
        if (null !== $psr7Response) {
            send($psr7Response);
        }
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ECAS PHP Client">
    <meta name="author" content="DIGIT.B.01/IAM">

    <title>ECAS PHP Client</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-4">
            <button type="button" class="btn btn-primary"
                    onclick="location.href='index.php';">&lt; Back</strong></button>

        </div>
        <div class="col-xs-4 text-right">
            <button type="button" class="btn btn-warning"
                    onclick="location.href='restricted.php';">Renew <strong><?php echo $name; ?></strong>
            </button>
        </div>
        <div class="col-xs-4 text-right">
            <button type="button" class="btn btn-danger"
                    onclick="location.href='logout.php';">Logout <strong><?php echo $name; ?></strong>
            </button>
        </div>
    </div>

    <h1>Restricted access</h1>
    <h2>
        Hi <?php echo $name; ?> !
    </h2>
    <?php if (null !== $user) { ?>

        <fieldset>
            <legend>User debug</legend>

            <?php dump($user); ?>
        </fieldset>

        <fieldset>
            <legend>Proxy Ticket Granting</legend>
            <form method="POST">
                <dl>
                    <dt>
                        <label>
                            Proxy Granting Ticket
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="pgt" value="<?php echo $pgt; ?>" size="150"/>
                    </dd>
                    <dt>
                        <label>
                            Target service
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="targetService" value="<?php echo $targetService; ?>" size="150"/>
                    </dd>

                    <?php if ($proxyTicket) { ?>
                        <dt>
                            <label>
                                Proxy ticket (lifetime = 10 seconds)
                            </label>
                        </dt>
                        <dd>
                            <input type="text" value="<?php echo $proxyTicket; ?>" size="150" readonly="readonly"/>
                        </dd>

                        <dt>
                            <label>
                                URL
                            </label>
                        </dt>
                        <dd>
                            <input type="text" value="<?php echo $linkToTargetService; ?>" size="150" readonly="readonly"/>
                        </dd>

                        <dt>
                            <label>
                                CURL command (or <a href="<?php echo $linkToTargetService; ?>" target="_blank">link to the page</a>)
                            </label>
                        </dt>
                        <dd>
                            <input type="text" value="<?php echo $curlCommandLine; ?>" size="150" readonly="readonly"/>
                        </dd>
                    <?php } ?>
                </dl>

                <input type="submit" value="Get a new proxy ticket!"/>
            </form>
        </fieldset>
    <?php } ?>
</div>
</body>
</html>

