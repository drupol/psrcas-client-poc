<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';

$user = $_SESSION['user'] ?? null;

if (null === $user) {
  return require_once __DIR__ . '/includes/login.php';
}

/** @var \Psr\Http\Message\ServerRequestInterface $serverRequest */
$serverRequest = include __DIR__ . '/includes/services/serverRequest.php';

/** @var \ecphp\CasLib\Cas $casClient */
$casClient = include __DIR__ . '/includes/services/cas.php';

$name = $user['serviceResponse']['authenticationSuccess']['user'];
$pgt = $user['serviceResponse']['authenticationSuccess']['proxyGrantingTicket'] ?? null;
$targetService = $proxyTicket ='';

if (isset($_POST['pgt'], $_POST['targetService'])) {
    $targetService = $_POST['targetService'];
    $pgt = $_POST['pgt'];

    try {
      $response = $casClient->requestProxyTicket($serverRequest, $_POST);
    } catch (Throwable $e) {
      return;
    }

    $body = json_decode((string) $response->getBody(), true);
    $proxyTicket = $body['serviceResponse']['proxySuccess']['proxyTicket'] ?? null;
    $linkToTargetService = htmlentities(sprintf('%s?ticket=%s', $targetService, $proxyTicket));
    $curlCommandLine = htmlentities(sprintf('curl -c cookie.txt -b cookie.txt -v -L -k "%s"', $linkToTargetService));
}
?>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container-fluid">
    <?php if (null !== $user) { ?>
        <fieldset>
            <legend>Proxy Ticket Granting</legend>

            <form method="POST">
                <div class="form-group">
                    <label for="pgt">Proxy Granting Ticket</label>
                    <input type="text" class="form-control" id="pgt" name="pgt" placeholder="Proxy granting ticket" value="<?= $pgt; ?>" readonly="readonly"/>
                </div>
                <div class="form-group">
                    <label for="targetService">Target service</label>
                    <input type="text" class="form-control" id="targetService" name="targetService" placeholder="https://any-url-here/" value="<?= $targetService; ?>"/>
                </div>

                <?php if ($pgt) { ?>
                <div class="form-group">
                    <input class="form-control btn btn-lg btn-block btn-primary" type="submit" name="newTicket" value="Get a new proxy ticket!" />
                </div>
                <?php } else { ?>
                  Unable to submit this form without PGT.
                <?php }?>

                <?php if ($proxyTicket) { ?>
                    <div class="form-group">
                        <label for="proxyTicket">Proxy ticket</label>
                        <input type="text" class="form-control" id="proxyTicket" name="proxyTicket" value="<?= $proxyTicket; ?>" readonly="readonly"/>
                    </div>
                    <div class="form-group">
                        <label for="linkToTargetService">URL</label>
                        <input type="text" class="form-control" id="linkToTargetService" name="linkToTargetService" value="<?= $linkToTargetService; ?>" readonly="readonly"/>
                    </div>
                    <div class="form-group">
                        <label for="curlCommandLine">Proxy ticket</label>
                        <input type="text" class="form-control" id="curlCommandLine" name="curlCommandLine" value="<?= $curlCommandLine; ?>" readonly="readonly"/>
                    </div>
                <?php } ?>
            </form>
        </fieldset>
    <?php } ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
