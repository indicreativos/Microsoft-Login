<?php
/**
 * By Saran Chamling
 * NOTE: This PHP file was created for Learning purpose, and is NOT affiliated with or endorsed by Microsoft inc.
 * Licensed under the Creative Commons Attribution-ShareAlike (CC BY-SA 3.0); 
 * You may obtain a copy of the License at
 *
 *     http://creativecommons.org/licenses/by-sa/3.0/
 */

//check curl and json
if (!function_exists('curl_init')) {
  exit('Microsoft Class Needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  exit('Microsoft Class Needs the JSON PHP extension.');
}

class MocrosoftLiveCnt
{
    private $clientId;
	private $clientSecret;
	private $scope;
	private $redirectUrl;
	
	//construct
    public function __construct($params) {
		$this->clientId 		= (string) $params['client_id'];
		$this->clientSecret  	= (string) $params['client_secret'];
		$this->scope 			= (string) $params['client_scope'];
		$this->redirectUrl  	= (string) $params['redirect_url'];
    }
	
	//return redirect url
	public function GetRedirectUrl()
	{
		return $this->redirectUrl;
	}
	
	//return microsoft login url
	public function GetLoginUrl()
	{
		$redirect_url	= urlencode($this->redirectUrl);
		$scope 			= urlencode($this->scope);
		$clientid 		= urlencode($this->clientId);
		$dialog_url 	= 'https://login.live.com/oauth20_authorize.srf?client_id='.$clientid.'&scope='.$scope.'&response_type=code&redirect_uri='.$redirect_url;
		return $dialog_url;
	}
	
	//get user details
	public function getUser()
	{
		$getAccessToken = $this->getAccessToken();
		$url = 'https://apis.live.net/v5.0/me?access_token='.$getAccessToken;
		$result = json_decode($this->HttpPost($url));
		
		if(!empty($result->error))
		{
			return false;
		}else{
			return $result;
		}
	}
	
	//get access token
	public function getAccessToken($code=null)
	{
			$token = $this->getSessionVar('ms_access_token');
			
			if($token && !$code)
			{
				return $token;
			}else{
				$url = 'https://login.live.com/oauth20_token.srf';
				$fields = array(
					'client_id' => urlencode($this->clientId),
					'client_secret' => urlencode($this->clientSecret),
					'redirect_uri' => urlencode($this->redirectUrl),
					'code' => urlencode($code),
					'grant_type' => urlencode('authorization_code')
					);
				$result = $this->HttpPost($url,1,$fields);
				if(!$result) {
					return false;
				}
				$authCode = json_decode($result);
				return $authCode->access_token;
			}
	}
	
	//set access token
	public function setAccessToken($token)
	{
		$this->setSessionVar('ms_access_token', $token);
	}
	
	//distroy all sessions	
	public function distroySession(){
		$this->initiateSession();
		session_destroy();
	}
	
	//httppost
	private function HttpPost($url=null,$post=0,$postargs=array())
	{		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		if($post)
		{
			foreach($postargs as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string); 
		}
		curl_setopt($ch, CURLOPT_POST, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	//set session variables
	private function setSessionVar($key, $value)
	{
		$this->initiateSession();
		$_SESSION[$key] = $value;
	}
	
	//return session variables
	private function getSessionVar($key)
	{
		$this->initiateSession();
		if(isset($_SESSION[$key])){
		return $_SESSION[$key];
		}
	}
	
	//session start
	private function initiateSession()
	{
		if (!session_id()) {
		  session_start();
		}	
	}

}