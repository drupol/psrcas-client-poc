<?php

require_once __DIR__ . '/../vendor/autoload.php';

$serverRequest = include __DIR__ . '/../includes/serverRequest.php';
?>

<a class="btn btn-block btn-lg btn-primary" href="includes/login.php?service=<?php print $serverRequest->getUri(); ?>">Renew login</a>
