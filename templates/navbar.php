<?php

require_once __DIR__ . '/../vendor/autoload.php';

$serverRequest = include __DIR__ . '/../includes/services/serverRequest.php';

$name = $_SESSION['user']['serviceResponse']['authenticationSuccess']['user'] ?? 'anonymous';
?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <a class="navbar-brand" href="#">ecphp/cas-lib</a>
  <div class="container-fluid">
    <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
      <ul class="navbar-nav me-auto">
        <li class="nav-item active">
          <a class="nav-link" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="simple.php">Simple</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="restricted.php">Restricted</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="proxy.php">Proxy</a>
        </li>
      </ul>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
      <ul class="navbar-nav ms-auto">
        <?php if (true === array_key_exists('user', $_SESSION ?? [])): ?>
          <li class="nav-item">
            <a class="nav-link" href="includes/login.php?service=<?= $serverRequest->getUri(); ?>">Renew login <?= $name; ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="includes/logout.php?service=<?= $serverRequest->getUri(); ?>">Logout <?= $name; ?></a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="includes/login.php?service=<?= $serverRequest->getUri(); ?>">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
