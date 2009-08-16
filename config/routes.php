<?php

$map = Router\Mapper::instance();


// Connect all custom routes.

$map->connect("user/:action/:id", array('controller' => 'index'));