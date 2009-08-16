<?php

define('PROJECT_NAME', 'Valet');

// Require application.
require_once("../core/boot.php");

$dispatcher = Router\Dispatcher::instance();
$dispatcher->dispatch();


?>