<?php

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/section3/' => 'controllers/index.php',
    '/section3/about' => 'controllers/about.php',
    '/section3/notes' => 'controllers/notes.php',
    '/section3/note' => 'controllers/note.php',
    '/section3/contact' => 'controllers/contact.php',
];

function routeToController($uri, $routes) {
    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function abort($code = 404) {
    http_response_code($code);

    require "views/{$code}.php";

    die();
}

routeToController($uri, $routes);