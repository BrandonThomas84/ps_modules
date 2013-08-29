<?php

$fieldsSQL = "SELECT `id`,`report_field_name`,`order`,CASE WHEN `required` = true THEN 'Y' WHEN `required` = false THEN 'N' END AS `required`, CASE WHEN `enabled` = true THEN 'Y' WHEN `enabled` = false THEN 'N' END AS `enabled`, CASE	WHEN `editable` = true THEN 'Y'	WHEN `editable` = false THEN 'N' END AS `editable` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` WHERE `merchant_id`='" . $GLOBALS["merchantID"] . "' ORDER BY `order` ASC";
$fieldsQUERY = mysql_query($fieldsSQL);

if(isset($_GET["fieldID"])){
	$fieldSQL = "SELECT `id`,`table_name`,`database_field_name`,`report_field_name`,`static_value`,`custom_function`,`merchant_id`,`description`,`order`,CASE WHEN `required` = true THEN 'Y' WHEN `required` = false THEN 'N' END AS `required`, CASE WHEN `enabled` = true THEN 'Y' WHEN `enabled` = false THEN 'N' END AS `enabled`, CASE	WHEN `editable` = true THEN 'Y'	WHEN `editable` = false THEN 'N' END AS `editable`, CASE WHEN `static` = true THEN 'Y' WHEN `static` = false THEN 'N' END AS `static` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` WHERE `id`='" . $_GET["fieldID"] . "'";
	$fieldQUERY = mysql_query($fieldSQL);
}


function feedConfigChecked($value){
	if($value === 'Y'){return " checked ";
	}
}
function feedConfigRequired($value){
	if($value === 'Y'){return " required";
	} else {return " optional ";
		}
}
function feedConfigRequiredCaption($value){
	if($value === 'Y'){return "<span class=\"required\">*REQUIRED</span>";
	}
}
function feedConfigEditable($value){
	if($value === 'N'){return " disabled ";
	}
}
function feedConfigEditableHeader($value){
	if($value === 'N'){return "noFieldEdit";
	}
}
function feedConfigStaticDisplay($value){
	if($value === 'N'){return "style=\"display: none;\"";
	}
}
function feedConfigSelected($needle,$haystack){
	if($needle === "NULL" || $needle === "N/A"){return "";
	} elseif($needle == $haystack){return " selected ";
		}
}
function feedConfigActive($value){
	if($value === 'N'){return "<span class=\"inactiveField\">Inactive</span>";
	} else {return "<span class=\"activeField\">Active</span>";
		}
}
function feedConfigAvailableFunctions($function){

						
	if($GLOBALS["merchantID"] == "google"){
		return "<option value=\"availability\" " . feedConfigSelected("availability",$function) . ">" . $GLOBALS["merchantID"] . "_availability</option>
		<option value=\"identifier_exists\" " . feedConfigSelected("identifier_exists",$function) . ">" . $GLOBALS["merchantID"] . "_identifier_exists</option>
		<option value=\"product_type\" " . feedConfigSelected("product_type",$function) . ">" . $GLOBALS["merchantID"] . "_product_type</option>";
	} elseif($GLOBALS["merchantID"] == "pricegrabber"){
		return "<option value=\"Categorization\" " . feedConfigSelected("Categorization",$function) . ">" . $GLOBALS["merchantID"] . "_Categorization</option>
		<option value=\"Availability\" " . feedConfigSelected("Availability",$function) . ">" . $GLOBALS["merchantID"] . "_Availability</option>
		<option value=\"ShippingCost\" " . feedConfigSelected("ShippingCost",$function) . ">" . $GLOBALS["merchantID"] . "_ShippingCost</option>";
		}
}
//returns the total number of products in the database that are to be included in the feed
function totalProducts(){ 
	$sql = "SELECT COUNT(DISTINCT `a1`.`id_product`) AS `total` FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product` AS `a1` LEFT JOIN `" . $GLOBALS["schema"] . "`.`merchant_exclusion` AS `a2` ON `a1`.`id_product` = `a2`.`id_product` AND `a2`.`" . $GLOBALS["merchantID"] . "_exclude` IS NOT NULL WHERE `a2`.`id` IS NULL;";
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	
	return $row["total"];
}
//returns the number of functional products
function functionalProducts($table,$field,$used){
	if (feedConfigEditable($used) == " disabled "){
		return "0";
	} else {
		if (($field != NULL || $field != '') && isset($field)){
	
			$select = "SELECT COUNT(DISTINCT `a1`.`id_product`) AS 'total' ";
			$from = reportQueryFrom($GLOBALS["schema"],$GLOBALS["tableLead"]);
			$where = reportQueryWhere($GLOBALS["schema"],$GLOBALS["merchant"]) . "AND (`" . $table . "`.`" . $field . "` IS NOT NULL AND `" . $table . "`.`" . $field . "` != '')";
			$query = mysql_query($select . $from . $where);
			$row = mysql_fetch_array($query);
			
			return $row["total"];
	
		} else { 
				return totalProducts($GLOBALS["schema"],$GLOBALS["tableLead"]);
			}
	}
}

function feedHealthDisplay(){
	$sql = "SELECT `table_name`,`database_field_name`,`enabled` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` WHERE `id` = " . $_GET["fieldID"];
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);

	echo "
		<div class=\"accuracy\">
			<span class=\"accuracyValue\" style=\"background: " . functionalWidth((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())) . "; width:" . number_format(((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())*100),2) . "%;\">" . number_format(((functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"])/totalProducts())*100),2) . "%</span>
		</div>" . functionalProducts($row["table_name"],$row["database_field_name"],$row["enabled"]) . " / " . totalProducts() . " <br/>";
}

function functionalWidth($v){
	if($v<0.03333333){return "#FF0000";}
	if($v<0.06666666){return "#FF1100";}
	if($v<0.09999999){return "#FF2200";}
	if($v<0.13333332){return "#FF3300";}
	if($v<0.16666665){return "#FF4400";}
	if($v<0.19999998){return "#FF5500";}
	if($v<0.23333331){return "#FF6600";}
	if($v<0.26666664){return "#FF7700";}
	if($v<0.29999997){return "#FF8800";}
	if($v<0.33333333){return "#FF9900";}
	if($v<0.36666663){return "#FFAA00";}
	if($v<0.39999996){return "#FFBB00";}
	if($v<0.43333329){return "#FFCC00";}
	if($v<0.46666662){return "#FFDD00";}
	if($v<0.49999995){return "#FFEE00";}
	if($v<0.53333328){return "#FFFF00";}
	if($v<0.56666661){return "#EEFF00";}
	if($v<0.59999994){return "#DDFF00";}
	if($v<0.63333327){return "#CCFF00";}
	if($v<0.66666666){return "#BBFF00";}
	if($v<0.69999993){return "#AAFF00";}
	if($v<0.73333326){return "#99FF00";}
	if($v<0.76666659){return "#88FF00";}
	if($v<0.79999992){return "#77FF00";}
	if($v<0.83333325){return "#66FF00";}
	if($v<0.86666658){return "#55FF00";}
	if($v<0.89999991){return "#44FF00";}
	if($v<0.93333324){return "#33FF00";}
	if($v<0.96666657){return "#22FF00";}
	if($v<0.99999999){return "#11FF00";}
	else {return "#00FF00";}
}

function displayAllFields(){
	echo "<div id=\"feedFields\"><h2>Select Field to Configure</h2>";

	while($row = mysql_fetch_array($GLOBALS["fieldsQUERY"])){
		echo "<a class=\"feedFieldTile " . feedConfigEditableHeader($row["editable"]) . " " . feedConfigRequired($row["required"]) . "\" href=\"" . $_SERVER["PHP_SELF"] . "?f=" . $GLOBALS["merch"] . "&fieldID=" . $row["id"] . "\" title=\"Edit Field Settings for " . $row["report_field_name"] . "\"><li><div><p>" . $row["report_field_name"] . "</p>" . feedConfigActive($row["enabled"]) . "</div></li></a>";
	}

	echo "</div><div class=\"clear\"></div>";
}

function displayFieldConfig(){
	$cRow = mysql_fetch_array($GLOBALS["fieldQUERY"]);		
	
	echo "
	<h3 id=\"" . $cRow["report_field_name"] . "\" class=\"" . feedConfigEditableHeader($cRow["editable"]) . "\" >[" . $cRow["report_field_name"] . "]" .  feedConfigActive($cRow["enabled"]) .feedConfigRequiredCaption($cRow["required"]) . "</h3>
	<div id=\"feedSettingsMod\">
	<form action=\"feed_config/config_u.php\" method=\"POST\" enctype=\"application/x-www-form-urlencoded\" title=\"" . $cRow["report_field_name"] ." Configuration\" name=\"" . $cRow["report_field_name"] . "\" >
	<table cellspacing=\"0\" cellpadding=\"0\" id=\"fieldSettings\">
	  <col width=\"200\" />
	  <col width=\"70\" span=\"4\" />
	  <col width=\"20\" />
	  <col width=\"130\" span=\"2\" />
	  <tr>
	    <td colspan=\"8\" height=\"20\"></td>
	  </tr>
	  <tr>
	    <td><p class=\"title br\">Active<a href=\"#\" title=\"This checkbox determines whether or not this field will be included in your feed. Required fields will have this option disabled.\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a></p></td>
	    <td><input type=\"checkbox\" name=\"enabled\" " . feedConfigChecked($cRow["enabled"]) . feedConfigEditable($cRow["editable"]) . "></td>
	    <td colspan=\"3\"></td>
	    <td></td>
	    <td colspan=\"2\"><p class=\"title\" style=\"text-align:left !important;\"><a href=\"#\" title=\"This area is used to provide the merchants description of the field in their feed. This can help you to insure that you have selected the correct column from your own database. You can even add your own notes to this area!\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a>Description</p></td>
	  </tr>
	  <tr>
	    <td colspan=\"5\" height=\"10\"></td>
	  </tr>
	  <tr>
	    <td><p class=\"title br\">Database<a href=\"#\" title=\"This is the table and field that the information for the feed is being retrieved from\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a></p></td>
	    <td colspan=\"2\">
		  <span>Table Name</span><br/>
		  <select name=\"table_name\" " . feedConfigEditable($cRow["editable"]) . ">
	        <option value=\"N/A\" " . feedConfigSelected("N/A",$cRow["table_name"]) . ">N/A</option>
	        <option value=\"a1\" " . feedConfigSelected("a1",$cRow["table_name"]) . ">" . $GLOBALS["tableLead"] . "product</option>
	        <option value=\"a2\" " . feedConfigSelected("a2",$cRow["table_name"]) . ">" . $GLOBALS["tableLead"] . "manufacturer</option>
	        <option value=\"a3\" " . feedConfigSelected("a3",$cRow["table_name"]) . ">" . $GLOBALS["tableLead"] . "product_lang</option>
	        <option value=\"a4\" " . feedConfigSelected("a4",$cRow["table_name"]) . ">URL Rewrite View</option>
	        <option value=\"a5\" " . feedConfigSelected("a5",$cRow["table_name"]) . ">Category Rewrite View</option>
	      </select></td>
	    <td colspan=\"2\">
	      <span>Field Name</span><br/>
		  <input type=\"text\" size=\"25\" maxlength=\"150\" name=\"database_field_name\" value=\"" . $cRow["database_field_name"] . "\" align=\"left\" " . feedConfigEditable($cRow["editable"]) . "></td>
	    <td></td>
	    <td class=\"fieldDesc\" width=\"275\"colspan=\"2\" rowspan=\"11\" valign=\"top\"><p>" . $cRow["description"] . "</p></td>
	  </tr>
	  <tr>
	    <td colspan=\"5\" height=\"10\"></td>
	  </tr>
	  <tr>
	    <td><p class=\"title br\">Static<a href=\"#\" title=\"This checkbox instructs the feed to insert a static value instead of querying the database. To assign a value check this box, submit your changes, and then return to this field to fill in the static value.\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a></p></td>
	    <td><input type=\"checkbox\" name=\"static\" " . feedConfigChecked($cRow["static"]) . feedConfigEditable($cRow["editable"]) . " ></td>
	    <td colspan=\"3\"><span " . feedConfigStaticDisplay($cRow["static"]) . ">Static Value<input type=\"text\" size=\"25\" maxlength=\"250\" name=\"static_value\" value=\"" . $cRow["static_value"] . "\"" . feedConfigEditable($cRow["editable"]) . " align=\"left\"></span></td>
	    <td></td>
	    <td></td>
	  </tr>
	  <tr>
	    <td colspan=\"6\" height=\"10\"></td>
	  </tr>
	  <tr>
	    <td><p class=\"title br\">Custom Function<a href=\"#\" title=\"Here you can assign a custom function to the field. Some common custom functions have been preleaded into the Merchant Center. If you do not see one that will work for you please contact the author of this module.\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a></p></td>
	    <td colspan=\"4\"><select name=\"custom_function\" " . feedConfigEditable($cRow["editable"]) . ">
	        <option value=\"NULL\" " . feedConfigSelected("NULL",$cRow["custom_function"]) . ">NONE SELECTED</option>
	        <option value=\"upcFix\" " . feedConfigSelected("upcFix",$cRow["custom_function"]) . ">upcFix</option>
	        <option value=\"imageLink\" " . feedConfigSelected("imageLink",$cRow["custom_function"]) . ">imageLink</option>
	        <option value=\"productLink\" " . feedConfigSelected("productLink",$cRow["custom_function"]) . ">productLink</option> " . feedConfigAvailableFunctions($cRow["merchant_id"],$cRow["custom_function"]) . "</select></td>
	    <td>
			<div class=\"hidden\">
				<input type=\"hidden\" name=\"report_field_name\" value=\"" . $cRow["report_field_name"] . "\" >
				<input type=\"hidden\" name=\"merchant_id\" value=\"" . $cRow["merchant_id"] . "\" >
				<input type=\"hidden\" name=\"id\" value=\"" . $cRow["id"] . "\" >
				<input type=\"hidden\" name=\"schema\" value=\"" . $GLOBALS["schema"] . "\" >
				<input type=\"hidden\" name=\"editable\" value=\"" . $cRow["editable"] . "\" >
			</div>
		</td>
	  </tr>
	  <tr>
	    <td colspan=\"6\" height=\"10\">
			<input type=\"submit\" value=\"Save Changes\">
		</td>
	  </tr>
	  <tr>
	    <td colspan=\"5\"><p class=\"title\" style=\"text-align:center !important;\">Field Information<a href=\"#\" title=\"This area is designed to display information that pertains to how your configured field functions against your database. The higher the included products and accepted value percentage the better your data quality.\" class=\"tooltip\"><span title=\"help\"><img src=\"images/help.png\"></span></a></p></td>
	    <td></td>
	  </tr>
	    </form>
	  
	  <tr>
	    <td colspan=\"5\" rowspan=\"4\" class=\"border\">
			<h2>Field Health</h2>";
			feedHealthDisplay();
echo "	</td>
	    <td></td>
	  </tr>
	  <tr>
	    <td></td>
	  </tr>
	</table>
	</div>
";

}
?>