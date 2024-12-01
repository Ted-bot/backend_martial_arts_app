<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {

    header('Access-Control-Allow-Origin: *');
    // header("Access-Control-Allow-Headers: Content-Type, Accept, Origin, X-Authorization");
    header("Access-Control-Allow-Headers: Content-Type, Accept, Origin, X-Authorization, Authorization");
    header('Access-Control-Expose-Headers: *');
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE");
    // header("Allow: *");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        die();
    }
    
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
