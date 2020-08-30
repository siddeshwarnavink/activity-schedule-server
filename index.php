<?php

include_once './config/bootstrap.php';

define('ROOT_DIR', '/activity-schedule-server/');

$path = explode("?", $_SERVER['REQUEST_URI'])[0];
$path = str_replace(ROOT_DIR, '', $path);
$path = "./api/" . $path . ".php";

if(file_exists($path)) {
    include_once $path;
} else {
    echo json_encode([
        'error' => 'API not found'
    ]);
    http_response_code(404);
}