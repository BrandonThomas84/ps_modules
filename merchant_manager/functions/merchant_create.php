<?php
require_once("functions.php");

//open file with write permission
$file = fopen($GLOBALS["root"] . "/merchant_manager/submissions/AHS_" . $GLOBALS["merchantID"] . "_product_submission.txt", "w+");
if(!$file){die("Cannot open " . $GLOBALS["merchant"] . " Merchant File!");} 


if(isset($_GET["del"])){
	unlink($file);
	} else {
		HeaderPrint($file,queryBuilder('head'));
		MerchPrint($file,queryBuilder(''));
		fclose($file);
	}

//close open file permissions
header("Location: " . $returnURL . "&msg=sc0002");
?> 
