<?php
namespace auth;

class vk {

  public $user = null;

  public function getToken() {


	$app_id = '3128485';
        $app_secret = 'tlORW6DTAvq8MbaLjXre';
        $my_url = 'http://mybox.pagodabox.com/login_vk';
  
        if(isset($_REQUEST['error_reason'])){
			die('Facebook auth error!');
            return null;
        }
        
        $code = $_REQUEST["code"];
        
        $token_url = 'https://oauth.vk.com/access_token?client_id=' . $app_id 
               . '&client_secret=' . $app_secret . '&code=' . $code 
               . '&redirect_uri=' . urlencode($my_url);


        $response = $this->cURL($token_url);

        $params = json_decode( $response );
        
        $query = 'users.get?uids=' . $params->user_id . '&fields=uid,first_name,last_name';
        
        $url = 'https://api.vk.com/method/' . $query
        
             .'&access_token=' . $params->access_token;

        $response = $this->cURL( $url );

        $response = json_decode( $response, true );

        $this->user = $response['response'][0];

        return $params->access_token;
  
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
