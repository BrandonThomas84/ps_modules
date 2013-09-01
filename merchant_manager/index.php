<!doctype html>
<html><head>
<meta charset="utf-8">
<title>Merchant Center Manager</title>
<meta name="description" content="Merchant Manager - Easily create feeds for the webs top shopping engines" />
<meta name="author" content="Brandon Thomas">
<meta name="robots" content="noindex,nofollow" />
<!--STYLESHEETS-->
<link href="style/style.css" rel="stylesheet" type="text/css" media="screen">
<link href="feed_config/style/config_style.css" rel="stylesheet" type="text/css" media="screen">
<!--START Google web fonts -->
<link href='http://fonts.googleapis.com/css?family=Armata' rel='stylesheet' type='text/css'>
<!--END Google web fonts -->
<!--START JQuery -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<!--END JQuery JS -->
</head>
<body>
<div id="primary">

<?php 
require('functions/functions.php');
sec_session_start();
if(login_check($mysqli) == true) {

?>
<!--Login: http://www.wikihow.com/Create-a-Secure-Login-Script-in-PHP-and-MySQL-->

  <div id="nav">
    <a href="./" title="home"><p>Merchant Manager</p></a>
    <p style="margin: 3px 10px 5px 10px;font-size: 13px;">Select from your installed modules</p>
	<?php navGeneration();?>
  </div>
  <div id="main">
<?php 
	echo messageReporting();
	if(isset($config) && $config == "config"){
		require ('feed_config/config.php');
	} elseif (isset($config) && $config == "usrreg"){
		require ('functions/login/register.php');
		} elseif (isset($config) && $config == "exmng"){
			require ('merchant_exclusions.php');
			} elseif (isset($config) && $config == "tax"){
				require ('product_taxonomy.php');
				} else {
					if(
						!isset($GLOBALS["merch"]) || 
						$GLOBALS["merch"] == '' || 
						$GLOBALS["merch"] == 'home'
					  ){
?>
    
    <h1>Merchant Manager</h1><br/>
    <p>Please select one of your installed modules from the navigation panel on the left.</p>
    <ul>
    	<li><a href="#" title="Shop for more modules" target="_blank">Shop for more modules!</a></li>
        <li><a href="mailto:merchmanager_feedback@partychico.com" title="Email us" target="_blank">Submit Feedback</a></li>
    </ul>
<?php 
	;} else { 
			require('merchant_control.php');
		}
	}
?>
  </div>


<?php } else { ?>
<!--START js Login-->
<script type="text/javascript" src="functions/login/sha512.js"></script>
<script type="text/javascript" src="functions/login/forms.js"></script>
<!--END js Login-->
<?php require('functions/login/login.php');}?>

</div>
</body>
</html>