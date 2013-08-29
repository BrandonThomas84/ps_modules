<?php
///////////////////////////////////////////////////////////////////////
//Required Setup Variables
////////////////////////////////////////////////////////////////////////

require($_SERVER["DOCUMENT_ROOT"] . "/ps_modules/config/settings.inc.php");
$host = _DB_SERVER_;
$user = _DB_USER_;
$pass = _DB_PASSWD_;
$tableLead = _DB_PREFIX_;
$schema = _DB_NAME_;
$link = mysql_connect($host,$user,$pass); 
	if (!$link){die("Could not connect to MySQL: " . mysql_error());}
	
//error and success messages that will need to be removed from the return URL
$msgs = array("&msg=er0001","&msg=sc0001","&msg=sc0002");
$returnURL = str_replace($msgs,"",$_SERVER["HTTP_REFERER"]);
$root = $_SERVER["DOCUMENT_ROOT"] . "/ps_modules";
?>