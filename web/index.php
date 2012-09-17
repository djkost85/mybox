<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\TwigEngine;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/api.php';
require_once __DIR__ . '/../controllers/facebook.php';
require_once __DIR__ . '/../controllers/vk.php';
require_once __DIR__ . '/../controllers/facebook.php';
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/vkProvider.php';
require_once __DIR__ . '/../models/facebookProvider.php';
require_once __DIR__ . '/../vendor/php-activerecord/php-activerecord/ActiveRecord.php';
require_once __DIR__ . '/../cnfg.php';


$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\SessionServiceProvider());

\ActiveRecord\Config::initialize(function($cfg)
{
     $cfg->set_model_directory(__DIR__.'/../models');
     $cfg->set_connections(array(
       /*  'production' => 'mysql://'.$_SERVER["DB1_USER"].':'.$_SERVER["DB1_PASS"].'@'.$_SERVER["DB1_HOST"].'/'.$_SERVER["DB1_NAME"], */
         'development' => 'mysql://root:18445610@localhost/db1'
     ));

});

$app->register(new Silex\Provider\TwigServiceProvider(), array(

    'twig.path' => __DIR__.'/../views',

));

/** index **/
$app->get("/", function () use ($app) {

    return $app['twig']->render('index.html.twig');

});

/** boxes list **/
$app->get('/boxes', function () use ($app) {

    $posts = array();

    $c = new box\api();

    $posts = $c->getPosts( $app );

    return json_encode( $posts );    

});

/** fb get token **/

$app->get('/login_fb', function () use ($app) {

    $c = new auth\facebook();
    
    $token = $c->getToken();

    $app['session']->set( 'facebook', array( 'token' => $token, 'user' => $c->user ) );

    return new RedirectResponse('http://mybox.pagodabox.com/boxes');

});
/** vk get token **/

$app->get('/login_vk', function () use ($app) {

    $c = new auth\vk();

    $token = $c->getToken();

    $app['session']->set( 'vk', array( 'token' => $token, 'user' => $c->user ) );

    return new RedirectResponse('http://mybox.pagodabox.com/boxes');

});



$app->run();
