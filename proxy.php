<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

include __DIR__ . '/includes/forceAuthentication.php';

// If the user hasn't been found... Launch the login process.
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
<?php include __DIR__ . '/templates/header.php'; ?>

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

<?php include __DIR__ . '/templates/footer.php'; ?>
