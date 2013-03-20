<?php
//include live_connect.inc.php file
include_once("inc/live_connect.inc.php");

//initiate class
$cnt_live = new MocrosoftLiveCnt(array(
	'client_id' 	=> 'Client id',
	'client_secret' => 'Client secret',
	'client_scope' 	=> 'wl.basic wl.emails wl.birthday',
	'redirect_url' 	=> 'Callback URL'
));


//get user info
$user_info = $cnt_live->getUser();

//get and set access token from microsoft
if(!$user_info && isset($_GET["code"])){
	$access_token 	= $cnt_live->getAccessToken($_GET["code"]);
	$cnt_live->setAccessToken($access_token);
	header('Location: '.$cnt_live->GetRedirectUrl());
}

//if user wants to log out, we simply distroy the current session
if(isset($_GET["logout"])){
	$cnt_live->distroySession();
	header('Location: '.$cnt_live->GetRedirectUrl());
}


if($user_info){ //we have the user info, let's do something with it.

     echo 'Hi '.$user_info->name.'. <a href="?logout=1">Log Out</a>';		
	//Display user details
	echo '<pre>';
	print_r($user_info);
	echo '</pre>';
	
}else{
	//show login button
	$loginUrl = $cnt_live->GetLoginUrl();
	echo '<a href="'.$loginUrl.'"><img src="microsoft-login-button.png" /></a>';
}

?>
