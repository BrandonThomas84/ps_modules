<?php
require_once ('functions.php');

//check for secure session 
sec_session_start();
if(login_check($mysqli) == true) {

	function removeExclusion(){
		$sql = "DELETE FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `id`='" . $_POST["id"] . "' and`exclusion`='" . $_POST["merchantID"] . "';";
		mysql_query($sql) or die("Could not connect to MySQLi: " . mysql_error());
		header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng");
	}

	function addExclusion(){

		//check if valid product
		$productSQL = "SELECT DISTINCT `id_product` FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product` WHERE `id_product` = '" . $_POST["id_product"] . "';";

		if(mysql_num_rows(mysql_query($productSQL)) == 1){

			//check if exclusion exisits
			$checkSql = "SELECT `id` FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `id_product`='" . $_POST["id_product"] . "' and`exclusion`='" . $_POST["merchantID"] . "';";
			
			if(mysql_num_rows(mysql_query($checkSql)) == 0){
				//Insert new product exclusion
				$sql = "INSERT INTO `" . $GLOBALS["schema"] . "`.`merchant_exclusion` (`id_product`, `exclusion`) VALUES ('" . $_POST["id_product"] . "', '" . $_POST["merchantID"] . "');";
				mysql_query($sql) or die("Could not connect to MySQLi: " . mysql_error());
				header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng&page=" . $_POST["pageNumber"] . "&perpage=" . $_POST["perPage"] . "&msg=sc0004");
			} else {
				header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng&page=" . $_POST["pageNumber"] . "&perpage=" . $_POST["perPage"] . "&msg=er0004");
			}
		} else {
				header("Location: ../index.php?f=" . $_POST["merch"] ."&p=exmng&page=" . $_POST["pageNumber"] . "&perpage=" . $_POST["perPage"] . "&msg=er0005");
		}	
	}

	//determining qhich function to run
	if(isset($_POST["submitRemove"])){
		removeExclusion();
	} elseif(isset($_POST["submitAdd"])){
		addExclusion();
	}
	
} else {
	echo "You have attempted to access a secure page and must be logged in";}

?>