<?php
namespace auth;

class facebook {

  public $user = null;  
  

  public function getToken() {


	$app_id = FACEBOOK_APP_ID;
        $app_secret = FACEBOOK_APP_SECRET;

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

        parse_str( $response, $params );

        $query = 'SELECT uid, first_name, last_name FROM user WHERE uid = me()';

        $url = 'https://graph.facebook.com/fql?q=' .  urlencode($query)
        . '&access_token=' . $params['access_token'];

        $response = json_decode($this->cURL( $url ), true );

        $this->user =  $response['data'][0];

		
        return $params['access_token'];
  
  }

  private function cURL($url, $header=NULL, $cookie=NULL, $p=NULL){
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_HEADER, $header);
        \curl_setopt($ch, CURLOPT_NOBODY, $header);
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        \curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        \curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if ($p) {
            \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            \curl_setopt($ch, CURLOPT_POST, 1);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
        }

        $result = \curl_exec($ch);
        \curl_close($ch);
        
        if ($result) {

            return $result;

        } else {

            return \curl_error($ch);

        }
        
  }

}
