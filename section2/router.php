<?php
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$routes = [
    '//laracast/section2/' => 'controllers/index.php',
    '/laracast/section2/about' => 'controllers/about.php',
    '/laracast/section2/contact' => 'controllers/contact.php',
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