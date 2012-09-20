<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\TwigEngine;

require_once __DIR__ . '/../cnfg.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/api.php';
require_once __DIR__ . '/../controllers/facebook.php';
require_once __DIR__ . '/../controllers/vk.php';
require_once __DIR__ . '/../controllers/facebook.php';
require_once __DIR__ . '/../models/boxPost.php';
require_once __DIR__ . '/../models/vkProvider.php';
require_once __DIR__ . '/../models/facebookProvider.php';
require_once __DIR__ . '/../vendor/php-activerecord/php-activerecord/ActiveRecord.php';

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/post.php';


$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\SessionServiceProvider());

\ActiveRecord\Config::initialize(function($cfg)
{

     $cfg->set_model_directory(__DIR__.'/../models');
     $cfg->set_connections(array(
     'prod' => 'mysql://'.$_SERVER["DB1_USER"].':'.$_SERVER["DB1_PASS"].'@'.$_SERVER["DB1_HOST"].'/'.$_SERVER["DB1_NAME"],
	 'dev' => 'mysql://root@localhost/mybox'
     ));
     $cfg->set_default_connection('dev');

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

    if( ! $app['session']->has('user') ){

        return new RedirectResponse( BASE_URL . '/connect' );
     
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

/** update posts for all users **/

$app->get('/api/update', function () use ($app) {

    $posts = array();

    $c = new box\api();
	
    $update_time_preiod = 0;
	
	$update_time = time() - $update_time_preiod;
	
	$userList = \box\user::all( array( 'conditions' => array( 'lastupdate < ? ', $update_time ) ) );
	
	$updated = array(); 
	
	if( count( $userList ) > 0 ){
	
		foreach($userList as $k=>$user ){
			
			$c->setPosts( $user );
			
			$user->lastupdate = time();
			
			$user->save();
			
			$updated[] = $user->id;
		
		}
		
	}
	
    return json_encode( $updated );    

});

/** fb get token **/

$app->get('/login_fb', function () use ($app) {

    $c = new auth\facebook();
    
    $token = $c->getToken();
	
	if( $token == null ){
	
		return new RedirectResponse( BASE_URL.'/connect' );
	
	}
	
    if( $app['user'] == null ){

		$user = \box\user::find_by_fbid($c->user['uid']);

		if($user == null ){
	
			$user = new \box\user();
			$user->fbid = $c->user['uid'];
			$user->fbtoken = $token;

        }
		
		$user->fbtoken = $token;
		$user->save();
		$app['session']->set('user', $user->id);

    }else{

		$user = $app['user'];
		$user->fbid = $c->user['uid'];
		$user->fbtoken = $token;  
		$user->save(); 

    }

    $app['session']->set( 'user', $user->id );

    return new RedirectResponse( BASE_URL );

});
/** vk get token **/

$app->get('/login_vk', function () use ($app) {

    $c = new auth\vk();

    $token = $c->getToken();
	
	if( $token == null ){
	
		return new RedirectResponse( BASE_URL.'/connect' );
	
	}
	
    if( $app['user'] == null ){

		$user = \box\user::find_by_vkid( $c->user['uid'] );

        if($user == null ){
	
			$user = new \box\user();
			$user->vkid = $c->user['uid'];
			$user->vktoken = $token;


        }
		$user->vktoken = $token;
		$user->save();
		$app['session']->set('user', $user->id);

    }else{

		$user = $app['user'];
		$user->vkid = $c->user['uid'];
		$user->vktoken = $token;  
		$user->save(); 

    }

    $app['session']->set( 'user', $user->id );

	
    return new RedirectResponse( BASE_URL );

});



$app->run();
