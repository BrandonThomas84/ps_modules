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

function feedConfigSelected($needle,$haystack){
	if($needle === "NULL" || $needle === "N/A"){return "";
	} elseif($needle == $haystack){return " selected ";
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
	<a href=\"./index.php?p=usrreg\" title=\"Create New User\">
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
					} elseif($msg == "er0004"){
						$d = "errMod";
						$m = "<p>An exceiption for that product within this feed already exists</p>";
						} elseif($msg == "sc0001"){
							$d = "sucMod";
							$m = "<p>Success! You have updated your feed settings!</p>";
							} elseif($msg == "sc0002"){
								$d = "sucMod";
								$m = "<p>New File created successfully</p>";
								} elseif($msg == "sc0003"){
									$d = "sucMod";
									$m = "<p>Successfully Created new User</p>";
									}elseif($msg == "sc0004"){
										$d = "sucMod";
										$m = "<p>You have successfully added a product to your exlcuions!</p>";
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
			`prd_type`.`catName3` AS `catName3`,
			`prd_type`.`catName3` AS `catName4`,
			`prd_type`.`catName3` AS `catName5`,
			`prd_type`.`catName3` AS `catName6`,
			`prd_type`.`catName3` AS `catName7` 
		 FROM
			(
				SELECT `crmb1`.`id_category` AS `prd_category1`,
					`crmb2`.`id_category` AS `prd_category2`,
					`crmb3`.`id_category` AS `prd_category3`,
					`crmb4`.`id_category` AS `prd_category4`,
					`crmb5`.`id_category` AS `prd_category5`,
					`crmb6`.`id_category` AS `prd_category6`,
					`crmb1`.`name` AS `catName1`,
					`crmb2`.`name` AS `catName2`,
					`crmb3`.`name` AS `catName3`, 
					`crmb4`.`name` AS `catName4`,
					`crmb5`.`name` AS `catName5`,
					`crmb6`.`name` AS `catName6`
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
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb4` 
					ON `crmb4`.`id_parent` = `crmb3`.`id_category`
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb5` 
					ON `crmb5`.`id_parent` = `crmb4`.`id_category`
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb6` 
					ON `crmb6`.`id_parent` = `crmb5`.`id_category`
					LEFT JOIN 
						(SELECT `nam`.`name` AS `name`,
							`num`.`id_category` AS `id_category`,
							`num`.`id_parent` AS `id_parent` 
						 FROM `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num` 
						 INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` 
						 ON `num`.`id_category` = `nam`.`id_category`
						 WHERE `num`.`active` = 1
						) `crmb7` 
					ON `crmb7`.`id_parent` = `crmb6`.`id_category`
				WHERE `crmb1`.`id_category` NOT IN (1 , 2)
			) `prd_type` 
			LEFT JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_product` `prd` 
			ON `prd`.`id_category` = coalesce(`prd_type`.`prd_category6`,`prd_type`.`prd_category5`,`prd_type`.`prd_category4`,`prd_type`.`prd_category3`, `prd_type`.`prd_category2`, `prd_type`.`prd_category1`) 
			GROUP BY `prd`.`id_product` 
			ORDER BY `prd_type`.`prd_category1` 
		) `a5` 
		ON `a5`.`id_product` = `a1`.`id_product` ";
}

//create query portion - "where"
function reportQueryWhere(){
	return " WHERE (`a1`.`active` = 1) AND (`a1`.`available_for_order` = 1) AND (`a1`.`id_product` NOT IN (SELECT id_product FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `exclusion` = '" . $GLOBALS["merchantID"] . "'))";
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
	if($value == "productCategory"){return productCategory($alias);}
	
	//Google specific custom functions
	if($GLOBALS["merchantID"] == "google"){ 
		if($value == "availability"){return googleAvailability($alias);}
		elseif($value == "identifier_exists"){return googleIdentifier_exists($alias);}
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

function productCategory($alias){
	return "(case
		when 
			isnull(`a5`.`catName1`) 
		then NULL
		when 
			((`a5`.`catName1` is not null) 
				AND isnull(`a5`.`catName2`) 
				AND isnull(`a5`.`catName3`) 
				AND isnull(`a5`.`catName4`) 
				AND isnull(`a5`.`catName5`) 
				AND isnull(`a5`.`catName6`)
				AND isnull(`a5`.`catName7`))
		then `a5`.`catName1`
		when 
			((`a5`.`catName1` is not null AND `a5`.`catName2` is not null)
				AND isnull(`a5`.`catName3`) 
				AND isnull(`a5`.`catName4`) 
				AND isnull(`a5`.`catName5`) 
				AND isnull(`a5`.`catName6`)
				AND isnull(`a5`.`catName7`))
		then concat(`a5`.`catName1`, 
					' > ',
					`a5`.`catName2`)
		when
			((`a5`.`catName1` is not null AND `a5`.`catName2` is not null AND `a5`.`catName3` is not null)
				AND isnull(`a5`.`catName4`) 
				AND isnull(`a5`.`catName5`) 
				AND isnull(`a5`.`catName6`)
				AND isnull(`a5`.`catName7`))
		then concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`)
		when
			((`a5`.`catName1` is not null AND `a5`.`catName2` is not null AND `a5`.`catName3` is not null AND `a5`.`catName4` is not null) 
				AND isnull(`a5`.`catName5`) 
				AND isnull(`a5`.`catName6`)
				AND isnull(`a5`.`catName7`))
		then concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`,
					' > ',
					`a5`.`catName4`)
		when
			((`a5`.`catName1` is not null AND `a5`.`catName2` is not null AND `a5`.`catName3` is not null AND `a5`.`catName4` is not null AND `a5`.`catName5` is not null)  
				AND isnull(`a5`.`catName6`)
				AND isnull(`a5`.`catName7`))
		then concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`,
					' > ',
					`a5`.`catName4`,
					' > ',
					`a5`.`catName5`)
		when
			((`a5`.`catName1` is not null AND `a5`.`catName2` is not null AND `a5`.`catName3` is not null AND `a5`.`catName4` is not null AND `a5`.`catName5` is not null AND `a5`.`catName6` is not null)
				AND isnull(`a5`.`catName7`))
		then concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`,
					' > ',
					`a5`.`catName4`,
					' > ',
					`a5`.`catName5`,
					' > ',
					`a5`.`catName6`)
		when
			(`a5`.`catName1` is not null AND `a5`.`catName2` is not null AND `a5`.`catName3` is not null AND `a5`.`catName4` is not null AND `a5`.`catName5` is not null AND `a5`.`catName6` is not null AND `a5`.`catName7` is not null)
		then concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`,
					' > ',
					`a5`.`catName4`,
					' > ',
					`a5`.`catName5`,
					' > ',
					`a5`.`catName6`,
					' > ',
					`a5`.`catName7`)		
		else NULL
	end) AS `" . $alias . "`";
}

//creates an option list containing all the categories that are currently configured in the prestashop database (up to 7 levels)
function distinctProductCategoryOptionList(){
	$sql = "SELECT DISTINCT " . productCategory("product_type") .  reportQueryFrom() . reportQueryWhere();
	$query = mysql_query($sql);
	
	echo "<select name=\"categories\">";
	while($row = mysql_fetch_array($query)){
		echo "<option value=\"" . $row["product_type"] . "\">" . $row["product_type"] . "</option>";
	}
	echo "</select>";
}

//instructs the merchant manager page to display the configure taxonomy button if there is a standard dataset present in the database
function taxonomyButton(){
	$sql = "SELECT DISTINCT `a2`.`merchant_id` FROM `testashop`.`mc_taxonomy` AS `a1` INNER JOIN `testashop`.`merchant_center_select_config` AS `a2` ON `a1`.`merchant_id` = `a2`.`merchant_id` WHERE `a1`.`merchant_id` = '" . $GLOBALS["merchantID"] . "';";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) > 0){
		return "<a class=\"button\" href=\"" . $_SERVER["PHP_SELF"] . "?f=" . $GLOBALS["merch"] . "&p=tax\" title=\"Product Taxonomy\">" . $GLOBALS["merchant"] ." Product Taxonomy</a>";
	}
}

//creates and option list that will display a list of the acceptable taxonomy values for the current merchant
function taxonomyDisplay($level,$parent){
	$sql = "SELECT DISTINCT `level" . $level . "` AS `taxonomy`, `merchant_id`, `id` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy` WHERE `merchant_id` = '" . $GLOBALS["merchantID"] . "';";
	$query = mysql_query($sql);

	echo "<select name=\"level" . $level . "\">";
	while($row = mysql_fetch_array($query)){
		echo "<option value=\"" . $row["taxonomy"] . "\">" . $row["taxonomy"] . "</option>";
	}
	echo "</select>";
}

//check to see if there is already a mapped value for this category if not it will insert the value into the database table if there is it returns the taxonomy id that is being used
function categoryToTaxCheck($category){
	$sql = "SELECT * FROM `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` WHERE `cattax_merchant_id` = '" . $GLOBALS["merchantID"] . "' AND `category_string` =  '" . $category . "';";
	$query = mysql_query($sql);
	$numRows = mysql_num_rows($query);
	$row = mysql_fetch_array($query);
	
	if($numRows == 0){
		$insert = "INSERT INTO `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` (`category_string`,`cattax_merchant_id`) VALUES ('$category','" . $GLOBALS["merchantID"] . "');";
		$query2 = mysql_query($insert);

		return 0;
	} else {
		return $row["cattax_id"];
	}
}

function displayTaxOptionList($level,$id){
	
	if($id == 0){$where = "";} else {$where = " WHERE `id` = '" . $id . "';";}

	$sql = "SELECT DISTINCT `level1`,`level2`,`level3`,`level4`,`level5`,`level6`,`level7` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy`" . $where;
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	$c = 0;

	if((is_null($row["level1"])) || ($id == 0)) {$l1 = ""; } else {$l1 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level1`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level1"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";} 
	if((is_null($row["level2"])) || ($id == 0) || ($level < 2)) {$l2 = "";} else {$l2 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level2`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level2"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if((is_null($row["level3"])) || ($id == 0) || ($level < 3)) {$l3 = "";} else {$l3 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level3`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level3"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if((is_null($row["level4"])) || ($id == 0) || ($level < 4)) {$l4 = "";} else {$l4 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level4`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level4"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if((is_null($row["level5"])) || ($id == 0) || ($level < 5)) {$l5 = "";} else {$l5 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level5`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level5"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if((is_null($row["level6"])) || ($id == 0) || ($level < 6)) {$l6 = "";} else {$l6 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level6`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level6"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if((is_null($row["level7"])) || ($id == 0) || ($level < 7)) {$l7 = "";} else {$l7 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level7`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["level7"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}

	echo "<label for=\"level" . $level . "\">Level " . $level . " Taxonomy</label><select name=\"level" . $level . "\"><option value=\"NULL\"";
	if($id == 0){echo " selected ";}
	echo "></option>";	

	$levelSQL = "SELECT DISTINCT `level" . $level . "` as `values` FROM  `" . $GLOBALS["schema"] . "`.`mc_taxonomy` WHERE `merchant_id` = '" . $GLOBALS["merchantID"] . "' " . $l1 . $l2 . $l3 . $l4 . $l5 . $l6 . $l7 . ";";
	$levelQuery = mysql_query($levelSQL);
	echo $levelSQL;
	while($levelRow = mysql_fetch_array($levelQuery)){
		echo "<option value=\"" . $levelRow["values"] . "\">" . $levelRow["values"] . "</option>";
	}	

	echo "</select><br/>";
}


?>