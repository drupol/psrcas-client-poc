<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/middleware/authenticate.php';
require_once __DIR__ . '/includes/middleware/proxy.php';

/** @var \EcPhp\CasLib\Contract\Configuration\PropertiesInterface $properties */
$properties = include __DIR__ . '/includes/services/properties.php';

$name = $_SESSION['user']['serviceResponse']['authenticationSuccess']['user'] ?? 'anonymous';
?>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container-fluid">
    <h1>Simple page</h1>

    <p>
        Hi there <?php print $name; ?> !
    </p>

    <p>
        This page is accessible by anonymous and authenticated users.
    </p>

    <h2>CAS configuration dump</h2>
    <?php dump($properties); ?>

    <h2>PHP Session dump</h2>
    <?php dump($_SESSION); ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
