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
require_once __DIR__ . '/../models/record.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/vkProvider.php';
require_once __DIR__ . '/../models/facebookProvider.php';
require_once __DIR__ . '/../vendor/php-activerecord/php-activerecord/ActiveRecord.php';
require_once __DIR__ . '/../cnfg.php';


$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\SessionServiceProvider());

\ActiveRecord\Config::initialize(function($cfg)
{
     $cfg->set_model_directory(__DIR__.'/../vendor/php-activerecord/php-activerecord/models');
     $cfg->set_connections(array(
       /*  'production' => 'mysql://'.$_SERVER["DB1_USER"].':'.$_SERVER["DB1_PASS"].'@'.$_SERVER["DB1_HOST"].'/'.$_SERVER["DB1_NAME"], */
         'development' => 'mysql://root:18445610@localhost/db1'
     ));

});

$app->register(new Silex\Provider\TwigServiceProvider(), array(

    'twig.path' => __DIR__.'/../views',

));

/** auth **/
$app['user'] = null;

if( $app['session']->has('user') ){
	
	$userId = $app['session']->get('user');
        $app['user'] = \box\user::find_by_id($userId);
	

}

/** index **/
$app->get("/", function () use ($app) {

    if( $app['session']->has('user') ){

        return new RedirectResponse('http://mybox.pagodabox.com/connect');
     
    } 

    return $app['twig']->render('index.html.twig');

});
/** connect **/
$app->get("/connect", function () use ($app) {

    return $app['twig']->render('connect.html.twig');

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

    if($app['user'] == null ){

	$user = \box\user::find_by_fbId($c->user['uid']);

	$user->fbToken = $token;

        if($user == null ){
	
		$user = new \box\user();
		$user->fbId = $c->user['uid'];
		$user->fbToken = $token;


        }

	$user->save;
	$app['session']->set('user', $user->id);

    }else{

	$user = $app['user'];
	$user->fbId = $c->user['uid'];
	$user->fbToken = $token;  
	$user->save(); 

    }

    //$app['session']->set( 'facebook', array( 'token' => $token, 'user' => $c->user ) );

    return new RedirectResponse('http://mybox.pagodabox.com');

});
/** vk get token **/

$app->get('/login_vk', function () use ($app) {

    $c = new auth\vk();

    $token = $c->getToken();

    if($app['user'] == null ){

	$user = \box\user::find_by_vkId($c->user['uid']);

	$user->vkToken = $token;

        if($user == null ){
	
		$user = new \box\user();
		$user->vkId = $c->user['uid'];
		$user->vkToken = $token;


        }

	$user->save;
	$app['session']->set('user', $user->id);

    }else{

	$user = $app['user'];
	$user->vkId = $c->user['uid'];
	$user->vkToken = $token;  
	$user->save(); 

    }

    //$app['session']->set( 'vk', array( 'token' => $token, 'user' => $c->user ) );

    return new RedirectResponse('http://mybox.pagodabox.com');

});



$app->run();
