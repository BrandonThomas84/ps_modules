<!doctype html>
<html><head>
<meta charset="utf-8">
<title>Merchant Center Manager</title>
<meta name="description" content="Merchant Manager - Easily create feeds for the webs top shopping engines" />
<meta name="author" content="Brandon Thomas">
<meta name="robots" content="noindex,nofollow" />
<!--STYLESHEETS-->
<link href="style/style.css" rel="stylesheet" type="text/css" media="screen">
<?php if(isset($_GET["p"])){?>
	<!-- Conditionally linking the style sheet for the configuration pages -->
	<link href="feed_config/style/config_style.css" rel="stylesheet" type="text/css" media="screen">
<?php ;} ?>
<!--START Google web fonts -->
<link href='http://fonts.googleapis.com/css?family=Armata' rel='stylesheet' type='text/css'>
<!--END Google web fonts -->
<!--START JQuery -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
	$(function() {
	$( "#accordion" ).accordion();
	});
</script>
<!--END JQuery JS -->
</head>
<?php require('functions/functions.php'); ?>

<body>
<div id="primary">
  <div id="nav">
    <a href="./" title="home"><p>Merchant Manager</p></a>
    <p style="margin: 3px 10px 5px 10px;font-size: 13px;">Select from your installed modules</p>
<?php 
	navGeneration(
	); 
?>
  </div>
  <div id="main">
<?php 
	if(isset($config)){
		require ('feed_config/config.php');
	} elseif($module == "exmng"){
		require('merchant_exclusions.php');
		} else {
			if(!isset($GLOBALS["merch"]) || $GLOBALS["merch"] == '' || $GLOBALS["merch"] == 'home'){
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
</div>
</body>
</html>
