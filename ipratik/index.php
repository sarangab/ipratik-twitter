<?php
// require twitterOAuth lib
require_once('twitteroauth/twitterOAuth.php');

/* Sessions are used to keep track of tokens while user authenticates with twitter */
session_start();
/* Consumer key from twitter */
$consumer_key = 'MKBDrDVkP4bLsvhjbHC0FA';
/* Consumer Secret from twitter */
$consumer_secret = 'OMJemmrf5zgpqfUZx2UrurymBfPBRdILHDUlvP3kwY';
/* Set up placeholder */
$content = NULL;
/* Set state if previous session */
$state = $_SESSION['oauth_state'];
/* Checks if oauth_token is set from returning from twitter */
$session_token = $_SESSION['oauth_request_token'];
/* Checks if oauth_token is set from returning from twitter */
$oauth_token = $_REQUEST['oauth_token'];
/* Set section var */
$section = $_REQUEST['section'];

/* Clear PHP sessions */
if ($_REQUEST['test'] === 'clear') {/*{{{*/
  session_destroy();
  session_start();
}/*}}}*/

/* If oauth_token is missing get it */
if ($_REQUEST['oauth_token'] != NULL && $_SESSION['oauth_state'] === 'start') {/*{{{*/
  $_SESSION['oauth_state'] = $state = 'returned';
}/*}}}*/

/*
 * Switch based on where in the process you are
 *
 * 'default': Get a request token from twitter for new user
 * 'returned': The user has authorize the app on twitter
 */
switch ($state) {/*{{{*/
  default:
    /* Create TwitterOAuth object with app key/secret */
    $to = new TwitterOAuth($consumer_key, $consumer_secret);
    /* Request tokens from twitter */
    $tok = $to->getRequestToken();

    /* Save tokens for later */
    $_SESSION['oauth_request_token'] = $token = $tok['oauth_token'];
    $_SESSION['oauth_request_token_secret'] = $tok['oauth_token_secret'];
    $_SESSION['oauth_state'] = "start";

    /* Build the authorization URL */
    $request_link = $to->getAuthorizeURL($token);

    /* Build link that gets user to twitter to authorize the app */
    $content = 'Click on the link to go to twitter to authorize your account.';
    $content .= '<br /><a href="'.$request_link.'">'.$request_link.'</a>';
    break;
  case 'returned':
    /* If the access tokens are already set skip to the API call */
    if ($_SESSION['oauth_access_token'] === NULL && $_SESSION['oauth_access_token_secret'] === NULL) {
      /* Create TwitterOAuth object with app key/secret and token key/secret from default phase */
      $to = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
      /* Request access tokens from twitter */
      $tok = $to->getAccessToken();

      /* Save the access tokens. Normally these would be saved in a database for future use. */
      $_SESSION['oauth_access_token'] = $tok['oauth_token'];
      $_SESSION['oauth_access_token_secret'] = $tok['oauth_token_secret'];
    }
    /* Random copy */
    $content = 'your account should now be registered with twitter. Check here:<br />';
    $content .= '<a href="https://twitter.com/account/connections">https://twitter.com/account/connections</a>';

    /* Create TwitterOAuth with app key/secret and user access key/secret */
    $to = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
    /* Run request on twitter API as user. */
    //$content = $to->OAuthRequest('https://twitter.com/account/verify_credentials.xml', array(), 'GET');
    //$content2 = $to->OAuthRequest('https://twitter.com/statuses/update.xml', array('status' => 'Test OAuth update. #testoauth'), 'POST');
    $content1 = $to->OAuthRequest('https://twitter.com/statuses/replies.xml', array(), 'GET');
        $content3=$to->OAuthRequest('http://twitter.com/statuses/user_timeline/pratikone.xml?count=1',array(),'GET');
    break;
}/*}}}*/

function updates($status){ 
	// Twitter login information 
	//$username = "pratikone"; 
	//$password = "preetu";
	
	$url = 'http://twitter.com/statuses/update.xml'; 
	// Arguments we are posting to Twitter 
	$postargs = 'status='.urlencode($status); 
	// Will store the response we get from Twitter 
	$responseInfo=array(); 
	// Initialize CURL 
	$ch = curl_init();
	
		// Tell CURL we are doing a POST 
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt ($ch, CURLOPT_POST, true); 
	// Give CURL the arguments in the POST 
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
	
		// Set the username and password in the CURL call 
	curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password); 
	// Set some cur flags (not too important) 
	curl_setopt($ch, CURLOPT_VERBOSE, 1); 
	curl_setopt($ch, CURLOPT_NOBODY, 0); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	// execute the CURL call 
	$response = curl_exec($ch); 
	// Get information about the response 
	$responseInfo=curl_getinfo($ch); 
	// Close the CURL connection 
	curl_close($ch);
}

  
?>

<html>
  <head>
    <title>Twitter OAuth in PHP</title>
  </head>
  <body>
    <h2>Welcome to a Twitter OAuth PHP example.</h2>
    <p>This site is a basic showcase of Twitters new OAuth authentication method. Everything is saved in sessions. If you want to start over <a href='<?php echo $_SERVER['PHP_SELF']; ?>?test=clear'>clear sessions</a>.</p>

    <p>
      Get the code powering this at <a href='http://github.com/abraham/twitteroauth'>http://github.com/abraham/twitteroauth</a>
      <br />
      Read the documentation at <a href='https://docs.google.com/View?docID=dcf2dzzs_2339fzbfsf4'>https://docs.google.com/View?docID=dcf2dzzs_2339fzbfsf4</a> 
    </p>

    <p><pre><?php 
$msg="trials and trials ..go on";
print_r($content3);
//print_r ($content1);
print($content);
//print_r(array_keys($content1));
//$msg=$_GET['txt1'];
//updates($msg);
  ?></pre></p>

  </body>
</html>

