<?php
require($GLOBALS["root"] . "/merchant_manager/functions/functions.php");
require($GLOBALS["root"] . "/merchant_manager/feed_config/config_functions.php");

/*
table_name
database_field_name
enabled

*/

$sql = "SELECT `table_name`,`database_field_name`,`enabled` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` WHERE `id` = " . $_GET["fd"];
$query = mysql_query($sql);
$row = mysql_fetch_array($query);

echo "
	<div class=\"accuracy\">
		<span class=\"accuracyValue\" style=\"background: " . functionalWidth((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())) . "; width:" . number_format(((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())*100),2) . "%;\">" . number_format(((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())*100),2) . "%</span>
	</div>" . functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"]) . " / " . totalProducts() . " <br/>";

?>