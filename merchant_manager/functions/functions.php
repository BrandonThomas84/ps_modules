<?php
require($_SERVER["DOCUMENT_ROOT"] . "/ps_modules/merchant_manager/functions/db_connect.php");
require($_SERVER["DOCUMENT_ROOT"] . "/ps_modules/merchant_manager/functions/login/login_functions.php");
////////////////////////////////////////////////////////////////////////
// General Functions
////////////////////////////////////////////////////////////////////////	

//instructs the index page which module / control panel to display based on the the URL
if(isset($_GET["p"])){$config = $_GET["p"];}
if(!isset($_GET["f"])){$module = "home";} else {$module = $_GET["f"];}

//merchant friendly name generator
function merchantID($f){
	if($f == "exmng"){$v = "exmng";}
	if($f == "apf"){$v = "amazon";}
	if($f == "epf"){$v =  "ebay";}
	if($f == "gpf"){$v =  "google";}
	if($f == "pgpf"){$v =  "pricegrabber";}
			
	return $v;
}
//merchant friendly name generator
function merchantFriendly($m){
	if($m == "apf"){return "Amazon";}
		elseif($m == "epf"){return "eBay";}
		elseif($m == "gpf"){return "Google";}
		elseif($m == "pgpf"){return "PriceGrabber";}
}

//defining merchant variables after function is created
if(isset($_GET["f"])){
	$merch = $_GET["f"];
	$merchantID = merchantID($merch);
	$merchant = merchantFriendly($merch);
} else {$merch = '';}

//Checking for the presence of and inlcuding current merchants custom functions file
if(isset($_GET["f"])){
	if(file_exists($GLOBALS["root"] . "/merchant_manager/merchants/" . $GLOBALS["merchantID"] . "/" . $GLOBALS["merchantID"] . "_functions.php")){
		require($GLOBALS["root"] . "/merchant_manager/merchants/" . $GLOBALS["merchantID"] . "/" . $GLOBALS["merchantID"] . "_functions.php");
	}
}
function navGeneration(){
	$sql = "SELECT DISTINCT `merchant_id` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` ORDER BY `merchant_id`";
	
	$query = mysql_query($sql) or die("Could not connect to MySQL: " . mysql_error());

	while($row = mysql_fetch_array($query)){
		if($row["merchant_id"] == "amazon"){$m="apf";}
		if($row["merchant_id"] == "ebay"){$m="epf";}
		if($row["merchant_id"] == "google"){$m="gpf";}
		if($row["merchant_id"] == "pricegrabber"){$m="pgpf";}
		
		echo  "<a href=\"./index.php?f=" . $m . "\" title=\"" . $row["merchant_id"] . " Product Feed\">
    	<li " . navActiveClass(array($m,$GLOBALS["merch"])) . ">" . strtoupper(substr($row["merchant_id"],0,1)) . substr($row["merchant_id"],1,(strlen($row["merchant_id"])-1))  . "</li>
    </a>";}
	
	echo "
	<hr><br>
	<a href=\"./index.php?f=exmng\" title=\"Manage Product Exclusions\">
    	<li " . navActiveClass(array("1",@$_GET["ex"])) . ">Manage Product Exclusions</li>
	</a>
	<hr><br>
	<a href=\"./functions/login/register.php\" title=\"Create New User\">
    	<li " . navActiveClass(array("1",@$_GET["ex"])) . ">Create New User</li>
	</a>
	<a href=\"./functions/login/logout.php\" title=\"Logout\">
    	<li " . navActiveClass(array("1",@$_GET["ex"])) . ">Logout</li>
	</a>";
}

//adds an active class to the left hand navigation for selected module / control panel
function navActiveClass($v){
	if($v[0] == $v[1]){return " class=\"active\" ";}
}
//reconstructs URL queries -- CHECK TO SEE IF $_SERVER["QUERY_STRING"] will work for this
function linkMaintain(){
	$url = $_SERVER["PHP_SELF"];
	$vars = array();
	
	if(isset($_GET["f"])){$f = "f=" . $_GET["f"]; array_push($vars,$f);}
			
	if(isset($_GET["p"])){$p = "p=" . $_GET["p"]; array_push($vars,$p);}
	
	return $url . "?" . implode("&",$vars);
}

//function used for message reporting
function messageReporting(){
	if(isset($_GET["msg"])){
		$msg = $_GET["msg"];
		
		if($msg == "er0001"){ 
			$d = "errMod";
			$m = "<p>Sorry...The field you are trying to edit has been disabled.</p><p>Please try another field.</p>";
			} elseif($msg == "er0002"){
				$d = "errMod";
				$m = "<p>Incorrect Login Information</p>";
				} elseif($msg == "er0003"){
					$d = "errMod";
					$m = "<p>Passwords do not match</p>";
					} elseif($msg == "sc0001"){
						$d = "sucMod";
						$m = "<p>Success! You have updated your feed settings!</p>";
						} elseif($msg == "sc0002"){
							$d = "sucMod";
							$m = "<p>New File created successfully</p>";
							} elseif($msg == "sc0003"){
								$d = "sucMod";
								$m = "<p>Successfully Created new User</p>";
								}
		return "
		<div class=\"$d\">
			$m
		</div>
		";
	}
}
////////////////////////////////////////////////////////////////////////
//Report Wizard Query Functions
////////////////////////////////////////////////////////////////////////

//create query portion - "select" 
function reportQuerySelect(){
	$sql = "SELECT `table_name`, `database_field_name`, `report_field_name`, `static_value`, `custom_function`, `merchant_id` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` WHERE `merchant_id` = '" . $GLOBALS["merchantID"] . "' AND `enabled` = 1 ORDER BY `order` LIMIT 0,250";
	
	$query = mysql_query($sql);
	
	$statement = array();
	
	while($row = mysql_fetch_array($query)){
		if(isset($row["static_value"]) && $row["static_value"] != ""){
			array_push($statement,"'" . $row["static_value"] . "' AS `" . $row["report_field_name"] . "`");
			} elseif (isset($row["custom_function"])){
				array_push($statement,customFunction($row["custom_function"],$row["merchant_id"],$row["report_field_name"]));
				} else {
					array_push($statement, "`" . $row["table_name"] . "`.`" . $row["database_field_name"] . "` AS `" . $row["report_field_name"] . "`");
					}
	}

	return implode(", ",$statement);	
}

//create query portion - "from"
function reportQueryFrom(){
	return " FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product` `a1` 
	INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "manufacturer` `a2` 
	ON `a1`.`id_manufacturer` = `a2`.`id_manufacturer`
	INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product_lang` `a3` 
	ON `a3`.`id_product` = `a1`.`id_product`
	
	INNER JOIN 
		(SELECT 
			`url1`.`id_product` AS `id_product`,
			`url2`.`link_rewrite` AS `link_rewrite` 
		 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_product` `url1` 
			INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `url2` 
			ON `url1`.`id_category` = `url2`.`id_category`
		 WHERE `url2`.`id_category` NOT IN (1 , 2)
		 GROUP BY `url1`.`id_product` 
		 ORDER BY `url1`.`id_product` 
		) `a4` 
	ON `a4`.`id_product` = `a1`.`id_product`
	LEFT JOIN 
		(
		 SELECT `prd`.`id_product` AS `id_product`,
			`prd_type`.`catName1` AS `catName1`,
			`prd_type`.`catName2` AS `catName2`,
			`prd_type`.`catName3` AS `catName3` 
		 FROM 
			(
				SELECT `crmb1`.`id_category` AS `prd_category1`,
					`crmb2`.`id_category` AS `prd_category2`,
					`crmb3`.`id_category` AS `prd_category3`,
					`crmb1`.`name` AS `catName1`,
					`crmb2`.`name` AS `catName2`,
					`crmb3`.`name` AS `catName3` 
					 FROM 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent`
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb1` 
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`, 
							`num`.`id_category` AS `id_category`, 
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1 
						) `crmb2` 
					ON `crmb2`.`id_parent` = `crmb1`.`id_category`
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb3` 
					ON `crmb3`.`id_parent` = `crmb2`.`id_category`
				WHERE `crmb1`.`id_category` NOT IN (1 , 2)
			) `prd_type` 
			LEFT JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_product` `prd` 
			ON `prd`.`id_category` = coalesce(`prd_type`.`prd_category3`, `prd_type`.`prd_category2`, `prd_type`.`prd_category1`) 
			GROUP BY `prd`.`id_product` 
			ORDER BY `prd_type`.`prd_category1` 
		) `a5` 
		ON `a5`.`id_product` = `a1`.`id_product` ";
}

//create query portion - "where"
function reportQueryWhere(){
	return " WHERE (`a1`.`active` = 1) AND (`a1`.`available_for_order` = 1) AND (`a1`.`id_product` NOT IN (SELECT id_product FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `" . $GLOBALS["merchantID"] . "_exclude` = 1))";
}
//returns the total number of products in the database that are to be included in the feed
function totalProducts(){ 
	$sql = "SELECT COUNT(DISTINCT `a1`.`id_product`) AS 'total' FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "_product` AS `a1` LEFT JOIN `" . $GLOBALS["schema"] . "`.`merchant_exclusion` AS `a2` ON `a1`.id_product = `a2`.id_product AND `a2`.`" . $GLOBALS["merchantID"] . "_exclude` IS NOT NULL WHERE `a2`.`id` IS NULL;";
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	
	return $row["total"];
}
////////////////////////////////////////////////////////////////////////
//File Creation Functions 
////////////////////////////////////////////////////////////////////////

//constructs the query from the three segements above
function queryBuilder($v){

	$query =  "SELECT " . reportQuerySelect() . reportQueryFrom() . reportQueryWhere();
	
	$headerlimit = " LIMIT 0,1 ";

	if($v == 'head'){return mysql_query($query . $headerlimit);} 
		else {return mysql_query($query);} 
};

//function used to construct the header of the output file
function HeaderPrint($file,$sql){
	
	//error checking
	if (!$sql) {die('Invalid query: ' . mysql_error());}
	while($row = mysql_fetch_assoc($sql)){
		$headers = array_keys($row);		
		fputcsv($file, $headers, chr(9));		
	}
}; 

//function used to construct the contents of the output file
function MerchPrint($file,$sql){
	
	//error checking
	if (!$sql) {die('Invalid query: ' . mysql_error());}
	while($row = mysql_fetch_assoc($sql)){
		$i = 0;
		fputcsv($file, $row, chr(9), chr(0));	
		++$i;
	}
};

//REMOVE THIS BEFORE DEPLOYMENT
function querytester(){

	$query =  "SELECT " . reportQuerySelect() . reportQueryFrom() . reportQueryWhere();
	
	return $query;
};

////////////////////////////////////////////////////////////////////////
//Merchant Specific Functions  - Custom Functions
////////////////////////////////////////////////////////////////////////

//used to return the correct sub function based on merchant
function customFunction($value,$alias){
	//Top level functions regardless of merchant
	if($value == "upcFix"){return upcFix($alias);} 
	if($value == "imageLink"){return imageLink($alias);} 
	if($value == "productLink"){return productLink($alias);}
	
	//Google specific custom functions
	if($GLOBALS["merchantID"] == "google"){ 
		if($value == "availability"){return googleAvailability($alias);}
		elseif($value == "identifier_exists"){return googleIdentifier_exists($alias);}
		elseif($value == "product_type"){return googleProduct_type($alias);}
	}
	//PriceGrabber specific custom functions
	if($GLOBALS["merchantID"] == "pricegrabber"){ 
		if($value == "Categorization"){return priceGrabberCategorization($alias);}
		elseif($value == "Availability"){return priceGrabberAvailability($alias);}
		elseif($value == "ShippingCost"){return priceGrabberShippingCost($alias);}
	}
}

function upcFix($alias){
	return "(case
            when (length(`a1`.`upc`) = 12) then `a1`.`upc`
            when (length(`a1`.`upc`) = 11) then concat('0', `a1`.`upc`)
            when (length(`a1`.`upc`) = 10) then concat('00', `a1`.`upc`)
        end) AS `" . $alias . "`";
}
function imageLink($alias){
	return "concat('http://" . $_SERVER["SERVER_NAME"] . "/', `a1`.`id_product`,'-large_default/',replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`a3`.`name`, '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '‾', ''), '+', ''), '<', ''), '=', ''), '>', ''), '↑', ''), '†', ''), '‡', ''), '‰', ''), '™', ''), '" . chr(92) .  "'', ''), '\"', ''), ' ', '-'), '---', '-'), '--', '-'), '.jpg') AS `" . $alias . "`";
}
function productLink($alias){
	return "concat('http://" . $_SERVER["SERVER_NAME"] . "/', `a4`.`link_rewrite`, '/', `a1`.`id_product`, '-', replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`a3`.`name`, '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '‾', ''), '+', ''), '<', ''), '=', ''), '>', ''), '↑', ''), '†', ''), '‡', ''), '‰', ''), '™', ''), '" . chr(92) .  "'', ''), '\"', ''), ' ', '-'), '---', '-'), '--', '-'), '.html') AS `" . $alias . "`";
}

?>