<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

$router->add('/confirm/{code}/{email}', [
    'controller' => 'user_control',
    'action' => 'confirmEmail'
]);

$router->add('/reset-password/{code}/{email}', [
    'controller' => 'user_control',
    'action' => 'resetPassword'
]);

$router->add('/retirar/', [
	'controller' => 'atm',
	'action' => 'retirar'
]);


$router->add('/getbalance/', [
	'controller' => 'atm',
	'action' => 'getbalance'
]);


$router->add('/transferir/', [
	'controller' => 'atm',
	'action' => 'transferir'
]);

return $router;
