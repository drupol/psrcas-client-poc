<?php

require_once __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/includes/authentication.php';

$name = 'stranger';
if (true === isset($_SESSION['user'])) {
    $name = $user['serviceResponse']['authenticationSuccess']['user'] ?? $name;
}
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
    <?php dump($casClient->getProperties()->all()); ?>

    <h2>PHP Session dump</h2>
    <?php dump($_SESSION); ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
