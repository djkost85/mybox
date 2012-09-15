<?php
use Symfony\Component\HttpFoundation\Request;


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/api.php';
require_once __DIR__ . '/vendor/php-activerecord/php-activerecord/ActiveRecord.php';

\ActiveRecord\Config::initialize(function($cfg)
{
     $cfg->set_model_directory(__DIR__.'/models');
     $cfg->set_connections(array(
         'development' => 'mysql://'.$_SERVER["DB1_USER"].':'.$_SERVER["DB1_PASS"].'@'.$_SERVER["DB1_HOST"].'/'.$_SERVER["DB1_NAME"]));
});

$app = new Silex\Application();

$app['debug'] = true;

$app->post('/api/set_token', function (Request $request) {
    $token = $request->get('token');
    $service = $request->get('service');

    //@TODO Сохранение здесь!


    return json_encode(array('result'=>'ok'));
});

$app->run();