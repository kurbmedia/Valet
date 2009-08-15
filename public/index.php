<?php

print_r($_SERVER);

// Require application.
require_once("../core/boot.php");

$app = new Application();


/**
 * Load any required libraries.
 * Available Options:
 * [ auth ]  : Enable authentication
 * [ db ] 	 : Database connection
 * [ flash ] : Flash Messages
 *
*/

$app->enable('auth,db,flash');
$app->run();


?>