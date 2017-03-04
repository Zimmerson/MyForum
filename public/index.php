<?php

include '../vendor/autoload.php';

try {

    $dotEnv = new \Dotenv\Dotenv('../config/');
    $dotEnv->load();
    $dotEnv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

    session_start();

    $router = new App\Router($_SERVER['REQUEST_URI']);
    $controller = $router->dispatch();

    $data = $controller->getData();

    $pageFile = $router->getPageFile();

    if (file_exists($pageFile)) {
        // Load the layout file which will include our page file.
        require "../layouts/master.php";
    }

} catch (\Exception $e) {
//    print "Application exception<br/>";
//    print $e->getMessage();
    throw $e;
}
