<?php
require("../functions/db_connect.php");

////////////////////////////////////////////////////////////////////////
//Functions will write and construct the statements necessary to update 
////////////////////////////////////////////////////////////////////////

function updateWrite($field,$value){
	
	if($value == "" || $value == "NULL"){
		$value = "NULL";
		} else {
			if($field == "description"){
				$value = str_replace("'","''",$value);
				} 			
			if(in_array($field,array("table_name","database_field_name","report_field_name","description","static_value","custom_function"))){
				$value = "'" . $value . "'";
				} 
			} 
			
	return "`" . $field . "` = " . $value;
}

function updateCompile($table_name,$database_field_name,$report_field_name,$description,$static,$static_value,$custom_function,$enabled){

	$a = array();

	if($table_name != "%NO VALUE%"){
		array_push($a,updateWrite("table_name",$table_name));}
	if($database_field_name != "%NO VALUE%"){
		array_push($a,updateWrite("database_field_name",$database_field_name));}
	if($report_field_name != "%NO VALUE%"){
		array_push($a,updateWrite("report_field_name",$report_field_name));}
	if($description != "%NO VALUE%"){
		array_push($a,updateWrite("description",$description));}
	if($static != "%NO VALUE%"){
		array_push($a,updateWrite("static",$static));}
	if($static_value != "%NO VALUE%"){
		array_push($a,updateWrite("static_value",$static_value));}
	if($custom_function != "%NO VALUE%"){
		array_push($a,updateWrite("custom_function",$custom_function));}
	if($enabled != "%NO VALUE%"){
		array_push($a,updateWrite("enabled",$enabled));}

	return $a;
}

//error and success messages that will need to be removed from the return URL
	$msgs = array("&msg=er0001","&msg=sc0001");
	$returnURL = str_replace($msgs,"",$_SERVER["HTTP_REFERER"]);

//start update function
$editable = $_POST["editable"];
if($editable != "N"){

	$schema = $_POST["schema"];
	$id = $_POST["id"];
	$merchant_id = $_POST["merchant_id"];

	if(isset($_POST["enabled"])){
		$enabled = "1";
		} elseif(!isset($_POST["enabled"])) {
			$enabled = "0";
			} else {
				$enabled = "0";
				}
	if(isset($_POST["static"])){
		$static = 1;
		} else {
			$static = 0;}
	if($_POST["table_name"] != "N/A"){
		$table_name = $_POST["table_name"];
		} else {
			$table_name = "%NO VALUE%";
			}
	if($_POST["database_field_name"] != ""){
		$database_field_name = $_POST["database_field_name"];
		} else {
			$database_field_name = "%NO VALUE%";
			}
	if($_POST["report_field_name"] != ""){
		$report_field_name = $_POST["report_field_name"];
		} else {
			$report_field_name = "%NO VALUE%";
			}
	if($_POST["description"] != ""){
		$description = $_POST["description"];
		} else {
			$description = "%NO VALUE%";
			}
	if($_POST["static_value"] != ""){
		$static_value = $_POST["static_value"];
		} else {
			$static_value = "%NO VALUE%";
			}
	if($_POST["custom_function"] != "NULL"){
		$custom_function = $_POST["custom_function"];
		} else {
			$custom_function = "NULL";
			}

	$sql =  "UPDATE `" . $schema . "`.`merchant_center_select_config` SET " . implode(", ",updateCompile($table_name,$database_field_name,$report_field_name,$description,$static,$static_value,$custom_function,$enabled)) . " WHERE `id` = " . $id . ";";
	
	$query = mysql_query($sql);
		if (!$query){die(mysql_error());}
	
	header("Location: " . $returnURL  . "&msg=sc0001");
	} else { 
		header("Location: " . $returnURL  . "&msg=er0001");
		}

?>