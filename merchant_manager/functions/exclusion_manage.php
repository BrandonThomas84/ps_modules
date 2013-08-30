<?php
require ('functions.php');

sec_session_start();

if(login_check($mysqli) == true) {

	function removeExclusion(){
		$sql = "DELETE FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `id`='" . $_POST["id"] . "' and`exclusion`='" . $_POST["merchantID"] . "';";
		mysql_query($sql) or die("Could not connect to MySQLi: " . mysql_error());
		header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng");
	}

	function addExclusion(){
		//check to see if exclusion already exisits
		$checkSql = "SELECT `id` FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `id_product`='" . $_POST["id_product"] . "' and`exclusion`='" . $_POST["merchantID"] . "';";
		
		if(mysql_num_rows(mysql_query($checkSql)) == 0){
			//Insert new product exclusion
			$sql = "INSERT INTO `" . $GLOBALS["schema"] . "`.`merchant_exclusion` (`id_product`, `exclusion`) VALUES ('" . $_POST["id_product"] . "', '" . $_POST["merchantID"] . "');";
			mysql_query($sql) or die("Could not connect to MySQLi: " . mysql_error());
			header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng&msg=sc0004");
		} else {
			header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng&msg=er0004");
			}
		//
		
	}

	//checking to see if script will be removing an existing product exclusion
	if(isset($_POST["submitRemove"])){
		removeExclusion();
	} 

	//checking to see if script will be adding a new product exclusion
	if(isset($_POST["submitAdd"])){
		addExclusion();
	}
	
} else {
	echo "you do not have permissions to view this, please login.";}

?>