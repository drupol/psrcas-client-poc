<?php

$serverRequest = include __DIR__ . '/../includes/serverRequest.php';

$name = '';
if (true === isset($_SESSION['user'])) {
    $name = ' ' . $_SESSION['user']['serviceResponse']['authenticationSuccess']['user'];
}

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <span class="navbar-brand mb-0 h1">PSR CAS</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

    <div class="collapse navbar-collapse">
        <div class="navbar-nav mr-auto">
            <a class="nav-item nav-link" href="index.php">Home</a>
            <a class="nav-item nav-link" href="simple.php">Simple</a>
            <a class="nav-item nav-link" href="restricted.php">Restricted</a>
            <a class="nav-item nav-link" href="proxy.php">Proxy</a>
        </div>

        <div class="my-2 my-lg-0">
            <div class="navbar-nav mr-auto">

            <?php if (false === isset($_SESSION['user'])): ?>
                <a class="nav-item nav-link" href="includes/login.php?service=<?php print $serverRequest->getUri(); ?>">Login</a>
            <?php else: ?>
                <a class="nav-item nav-link" href="includes/logout.php?service=<?php print $serverRequest->getUri(); ?>">Logout<?php print $name; ?></a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
