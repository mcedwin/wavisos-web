<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('simular', 'Simulador::index');
$routes->get('simular/enviar/(:segment)', 'Simulador::enviar/$1');
$routes->get('enviar', 'WhatsApp::enviar');

$routes->post('webhook2', 'WOtro::webhook');

$routes->get('api/regiones/(:num)', 'Api\Ubicacion::regiones/$1');
$routes->get('api/ciudades/(:num)', 'Api\Ubicacion::ciudades/$1');
$routes->get('cambiar/(:num)/(:num)/(:num)', 'Home::cambiar/$1/$2/$3');
$routes->get('(:segment)/(:segment)/(:segment)', 'Home::nav/$1/$2/$3');



//$routes->get('WOtro/sendMessage', 'WOtro::sendMessage');
// $routes->get('whatsapp/enviar', 'WOtro::enviar');
// $routes->get('whatsapp/prueba', 'WOtro::prueba');

// 

// $routes->get('bot', 'Bot::index');
// $routes->get('bot/index-(:num)', 'Bot::index/$1');

// $routes->get("/(:alpha)", "Home::nav/$1");
// $routes->get("/(:alpha)/(:alpha)", "Home::nav/$1/$2");
$routes->get("/item-(:num)", "Home::ver/$1");
// $routes->post('publicare', 'Home::publicar');


 $routes->setAutoRoute(true); // o 'improved'



