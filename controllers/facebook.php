<?php
namespace auth;

class facebook {

  public function getToken() {


	$app_id = '425240654200438';
        $app_secret = '1b3f4f01476be5624981a9f8ed6be17c';
        $my_url = 'http://mybox.pagodabox.com/login_fb';
  
        if(isset($_REQUEST['error_reason'])){
			die('Facebook auth error!');
            return null;
        }
        
        $code = $_REQUEST["code"];
        
        $token_url = "https://graph.facebook.com/oauth/access_token?"
        . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
        . "&client_secret=" . $app_secret . "&code=" . $code;

        $response = $this->cURL($token_url);

        return $response;
  
  }

}
