<?php

use Router;

$map = Mapper::get_instance();


// Connect all custom routes.

$map->connect("user/:action/:id", array('controller' => 'index'));