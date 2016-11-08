<?php
session_start();
define('APPROOT', __DIR__);
define('APP', true);

include APPROOT.'/library/App.php';

$app = new App();
$app->setBaseUrl('http://localhost/skeleton/')
    ->setAuthSessionVar('logged_in')
    ->setRoute([
        'default' => 'page/home',
        'sign-in' => 'page/sign-in',
        'userarea' => 'page/user-area',
    ])
    ->setProtectedRoute([
        'userarea',
    ])
    ->setLoginPage('sign-in')
    ->run();
