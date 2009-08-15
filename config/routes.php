<?php

$map = RouteMapper::get_instance();


// Connect all custom routes.

$map->connect("user/something/:id", array('controller' => 'index', 'action' => 'index'));