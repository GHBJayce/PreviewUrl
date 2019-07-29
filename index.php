<?php

require './vendor/autoload.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$app = new \App\App();

call_user_func([
    $app,
    $action,
]);