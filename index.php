<?php

require_once __DIR__ . '/vendor/autoload.php';

use drupol\psrcas\Utils\Uri;
use Nyholm\Psr7\Response;
use drupol\psrcas\Cas;

use function Http\Response\send;

include __DIR__ . '/includes/authentication.php';

$name = 'stranger';
if (null !== $_SESSION['user'] ?? null) {
    $name = $user['serviceResponse']['authenticationSuccess']['user'] ?? $name;
}
?>
<?php include __DIR__ . '/templates/header.php'; ?>

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

<?php include __DIR__ . '/templates/footer.php'; ?>
