<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\TwigEngine;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/api.php';
require_once __DIR__ . '/../controllers/facebook.php';
require_once __DIR__ . '/../vendor/php-activerecord/php-activerecord/ActiveRecord.php';
require_once __DIR__ . '/../vendor/ukko/vk/src/VK.php';

$app = new Silex\Application();

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
/** fb get token **/

$app->get('/login_fb', function () use ($app) {
    $c = new auth\facebook();
    echo $c->getToken();
});

$app['debug'] = true;
/*
$app['vk'] = new VK("3128485", "tlORW6DTAvq8MbaLjXre");



$app->get("/login_vk", function () use ($app) {
    return new RedirectResponse($app['vk']->getAuthorizeURL('2', 'http://mybox.pagodabox.com/login_vk_callback'));

});

$app->get("/login_vk_callback", function (Request $request) use ($app) {
    print "Access token:" . $app['vk']->getAccessToken($request->get('code'));
});

*/
$app->post('/api/set_token', function (Request $request) {
    $token = $request->get('token');
    $service = $request->get('service');

    //@TODO Сохранение здесь!


    return json_encode(array('result'=>'ok'));
});



$app->run();
