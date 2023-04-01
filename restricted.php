<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';

$user = $_SESSION['user'] ?? null;

if (null === $user) {
  require_once __DIR__ . '/includes/login.php';
}

$name = $user['serviceResponse']['authenticationSuccess']['user'];
$pgt = $user['serviceResponse']['authenticationSuccess']['proxyGrantingTicket'] ?? null;

if (isset($_POST['pgt'], $_POST['targetService'])) {
    $targetService = $_POST['targetService'];
    $pgt = $_POST['pgt'];

    if (null !== $response = $casClient->requestProxyTicket($_POST)) {
        $body = json_decode((string) $response->getBody(), true);
        $proxyTicket = $body['serviceResponse']['proxySuccess']['proxyTicket'] ?? null;
        $linkToTargetService = htmlentities(sprintf('%s?ticket=%s', $targetService, $proxyTicket));
        $curlCommandLine = htmlentities(sprintf('curl -b cookie.txt -c cookie.txt -v -L -k "%s"', $linkToTargetService));
    }
}
?>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container-fluid">
    <h1>Restricted access</h1>
    <h2>
        Hi <?php echo $name; ?> !
    </h2>

    <p>
        This page is only accessible to authenticated users.
    </p>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>

