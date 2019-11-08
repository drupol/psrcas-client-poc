<?php declare(strict_types=1);

require_once __DIR__  . '/vendor/autoload.php';

include __DIR__ . '/includes/authentication.php';

$name = 'anonymous';
if (true === isset($_SESSION['user'])) {
    $name = $_SESSION['user']['serviceResponse']['authenticationSuccess']['user'];
}
?>

<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container-fluid">
    <h1>Simple page</h1>
    <h2>
        Hi <?php echo $name; ?> !
    </h2>

    <p>
        This page is only accessible to authenticated users.
    </p>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
