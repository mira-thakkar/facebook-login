
<?php
session_start();
require_once 'autoload.php';

//import classes
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookHttpable;

// init app with app id and secret
FacebookSession::setDefaultApplication( 'app_id','app_secret');

//login helper with redirect_url
    $helper = new FacebookRedirectLoginHelper('http://localhost/facebook_login/connect.php');

try {
  $session = $helper->getSessionFromRedirect();
} 
catch( FacebookRequestException $ex ) {
  echo $ex->getMessage();	
} 
catch( Exception $ex ) {
  echo $ex->getMessage(); 
}
// see if session exists
if ( isset( $session ) ) 
{
  
  //create request for getting required information
   $request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,birthday,email');


  $response = $request->execute();
  // get response
  $accessToken = $session->getAccessToken();
  $graphObject = $response->getGraphObject();

  // To Get user information
  $userid = $graphObject->getProperty('id');
  $fullname = $graphObject->getProperty('name'); 
  $email = $graphObject->getProperty('email');  
  $birth = $graphObject->getProperty('birthday'); 
  $address=$graphObject->getProperty('address');
    
  //display facebook profile
  echo "<img src='https://graph.facebook.com/".$userid."/picture' style='height:100px;width:100px'>";
  echo  "<br>".$fullname;
  echo  "<br>".$email;
  echo  "<br>".$birth;
  echo  "<br>".$address;
    

  //upload photo on facebook
  $picture = __dir__.'/upload/image.png';
  $request = new FacebookRequest( $session, 'POST', '/me/photos',  array (
             'source' => new CURLFile($picture, 'image/png'),
             'message' => 'Nice Photo'
              ));

  $response = $request->execute();
  echo "photo uploaded";
	}	
 else
 {

//specify permission for the app.
  $permission=['email','user_birthday','publish_actions','read_custom_friendlists' ];
  $loginUrl = $helper->getLoginUrl($permission);
  header('Location: '.$loginUrl);
 }
?>



