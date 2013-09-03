<?php
require("db_connect.php");
require("login/login_functions.php");
include("taxonomy.php");
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
//applies 'selected' to optionlists where value matches
function feedConfigSelected($needle,$haystack){
	if($needle === "NULL" || $needle === "N/A" || $needle == ""){return "";
	} elseif($needle == $haystack){return " selected ";
		}
}
//strips all messages from the URL provided and returns new clean URL
function removeMessages($url){
    //removing all but the core URL
    $a = array(strtok($url,"?"));

    //checking if submitted URL contains variable information
    if(substr($url,strpos($url,"?")) == $url){
        $vars = "";
        $p = "";
    } else {
        //strips leading '?' and page location from url
        $vars = str_replace("?","",substr($url,strpos($url,"?")));
        $p = explode("&",$vars);
    }

    //search and extract p & f values
    for($i=0;$i<=substr_count($vars,"&");$i++){
        
        //finds f GET and returns it as fVal
        if(strstr($p[$i],"f=")){
            $fVal = "?" . $p[$i];
        } else {$fVal = "";}
        array_push($a,$fVal);

        //finds p GET and returns it as pVal
        if(strstr($p[$i],"p=")){
            if(isset($fVal)){$pVal = "&" . $p[$i];} else {$pVal = "?" . $p[$i];}
        } else {$pVal = "";}
        array_push($a,$pVal);
    }

    //returns URL without anything but the f value and p value if set
    return implode($a,""); 
}
//creates stripped return URL value, sending to home page if the referer value is not set
function returnURL(){
    if(isset($_SERVER["HTTP_REFERER"])){
        $returnURL = removeMessages($_SERVER["HTTP_REFERER"]);
    } else {
        $returnURL = $GLOBALS["root"];
    }
    return $returnURL;
}
//creates navigation menu
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
	<a href=\"./index.php?p=usrreg\" title=\"Create New User\">
    	<li " . navActiveClass(array("usrreg",@$_GET["p"])) . ">Create New User</li>
	</a>
	<a href=\"./functions/login/logout.php\" title=\"Logout\">
    	<li>Logout</li>
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
						} elseif($msg == "er0005"){
							$d = "errMod";
							$m = "<p>You have entered an invalid product ID (id_product)</p>";
							} elseif($msg == "sc0001"){
								$d = "sucMod";
								$m = "<p>Success! You have updated your feed settings!</p>";
								} elseif($msg == "sc0002"){
									$d = "sucMod";
									$m = "<p>New File created successfully</p>";
									} elseif($msg == "sc0003"){
										$d = "sucMod";
										$m = "<p>Successfully Created new User</p>";
										} elseif($msg == "sc0004"){
											$d = "sucMod";
											$m = "<p>You have successfully added a product to your exclusions!</p>";
											} elseif($msg == "sc0005"){
												$d = "sucMod";
												$m = "<p>Error successfully reported.</p><p>Thank you for your participation.</p>";
												} elseif($msg == "war0001"){
                                                    $d = "warMod";
                                                    $m = "<p>File has been emptied.</p>";
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
				array_push($statement,customFunction($row["custom_function"],$row["report_field_name"]));
				} else {
					array_push($statement, "`" . $row["table_name"] . "`.`" . $row["database_field_name"] . "` AS `" . $row["report_field_name"] . "`");
					}
	}

	return implode(", ",$statement);	
}

//create query portion - "from"
function reportQueryFrom(){
	return " 
	FROM
    `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product` AS `a1`
        LEFT JOIN
    `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "manufacturer` AS `a2` ON `a1`.`id_manufacturer` = `a2`.`id_manufacturer`
        LEFT JOIN
    `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "product_lang` AS `a3` ON `a3`.`id_product` = `a1`.`id_product`
        LEFT JOIN
    (SELECT 
        `url1`.`id_product` AS `id_product`,
            `url2`.`link_rewrite` AS `link_rewrite`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_product` `url1`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `url2` ON `url1`.`id_category` = `url2`.`id_category`
    WHERE
        `url2`.`id_category` NOT IN (1 , 2)
    GROUP BY `url1`.`id_product`
    ORDER BY `url1`.`id_product`) AS `a4` ON `a4`.`id_product` = `a1`.`id_product`
        INNER JOIN
    (SELECT DISTINCT
        `prd`.`id_product` AS `id_product`,
            (CASE
                WHEN
                    CONCAT((CASE
                        WHEN `tax`.`level1` IS NULL THEN ''
                        ELSE `tax`.`level1`
                    END), (CASE
                        WHEN `tax`.`level2` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level2`)
                    END), (CASE
                        WHEN `tax`.`level3` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level3`)
                    END), (CASE
                        WHEN `tax`.`level4` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level4`)
                    END), (CASE
                        WHEN `tax`.`level5` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level5`)
                    END), (CASE
                        WHEN `tax`.`level6` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level6`)
                    END), (CASE
                        WHEN `tax`.`level7` IS NULL THEN ''
                        ELSE CONCAT(' > ', `tax`.`level7`)
                    END)) = ''
                THEN
                    CONCAT((CASE
                		WHEN `category_string`.`catName1` IS NULL THEN ''
                		ELSE `category_string`.`catName1`
		            END),
		            (CASE
		                WHEN `category_string`.`catName2` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName2`)
		            END),
		            (CASE
		                WHEN `category_string`.`catName3` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName3`)
		            END),
		            (CASE
		                WHEN `category_string`.`catName4` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName4`)
		            END),
		            (CASE
		                WHEN `category_string`.`catName5` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName5`)
		            END),
		            (CASE
		                WHEN `category_string`.`catName6` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName6`)
		            END),
		            (CASE
		                WHEN `category_string`.`catName7` IS NULL THEN ''
		                ELSE CONCAT(' > ' + `category_string`.`catName7`)
		            END))
                ELSE CONCAT((CASE
                    WHEN `tax`.`level1` IS NULL THEN ''
                    ELSE `tax`.`level1`
                END), (CASE
                    WHEN `tax`.`level2` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level2`)
                END), (CASE
                    WHEN `tax`.`level3` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level3`)
                END), (CASE
                    WHEN `tax`.`level4` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level4`)
                END), (CASE
                    WHEN `tax`.`level5` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level5`)
                END), (CASE
                    WHEN `tax`.`level6` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level6`)
                END), (CASE
                    WHEN `tax`.`level7` IS NULL THEN ''
                    ELSE CONCAT(' > ', `tax`.`level7`)
                END))
            END) AS `final_category`,
			CONCAT((CASE
                WHEN `category_string`.`catName1` IS NULL THEN ''
                ELSE `category_string`.`catName1`
            END),
            (CASE
                WHEN `category_string`.`catName2` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName2`)
            END),
            (CASE
                WHEN `category_string`.`catName3` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName3`)
            END),
            (CASE
                WHEN `category_string`.`catName4` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName4`)
            END),
            (CASE
                WHEN `category_string`.`catName5` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName5`)
            END),
            (CASE
                WHEN `category_string`.`catName6` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName6`)
            END),
            (CASE
                WHEN `category_string`.`catName7` IS NULL THEN ''
                ELSE CONCAT(' > ' + `category_string`.`catName7`)
            END)) AS `ps_category_string`
    FROM
        (SELECT 
        `crmb1`.`id_category` AS `prd_category1`,
            `crmb2`.`id_category` AS `prd_category2`,
            `crmb3`.`id_category` AS `prd_category3`,
            `crmb4`.`id_category` AS `prd_category4`,
            `crmb5`.`id_category` AS `prd_category5`,
            `crmb6`.`id_category` AS `prd_category6`,
            `crmb7`.`id_category` AS `prd_category7`,
            `crmb1`.`name` AS `catName1`,
            `crmb2`.`name` AS `catName2`,
            `crmb3`.`name` AS `catName3`,
            `crmb4`.`name` AS `catName4`,
            `crmb5`.`name` AS `catName5`,
            `crmb6`.`name` AS `catName6`,
            `crmb7`.`name` AS `catName7`
    FROM
        (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb1`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb2` ON `crmb2`.`id_parent` = `crmb1`.`id_category`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb3` ON `crmb3`.`id_parent` = `crmb2`.`id_category`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb4` ON `crmb4`.`id_parent` = `crmb3`.`id_category`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb5` ON `crmb5`.`id_parent` = `crmb4`.`id_category`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb6` ON `crmb6`.`id_parent` = `crmb5`.`id_category`
    LEFT JOIN (SELECT 
        `nam`.`name` AS `name`,
            `num`.`id_category` AS `id_category`,
            `num`.`id_parent` AS `id_parent`
    FROM
        `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category` `num`
    INNER JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_lang` `nam` ON `num`.`id_category` = `nam`.`id_category`
    WHERE
        `num`.`active` = 1) `crmb7` ON `crmb7`.`id_parent` = `crmb6`.`id_category`
    WHERE
        `crmb1`.`id_category` NOT IN (1 , 2)) `category_string`
    LEFT JOIN `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` AS `taxmap` ON `taxmap`.`cattax_merchant_id` = '" . $GLOBALS["merchantID"] . "' AND CONCAT((CASE
        WHEN `category_string`.`catName1` IS NULL THEN ''
        ELSE `category_string`.`catName1`
    END), (CASE
        WHEN `category_string`.`catName2` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName2`)
    END), (CASE
        WHEN `category_string`.`catName3` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName3`)
    END), (CASE
        WHEN `category_string`.`catName4` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName4`)
    END), (CASE
        WHEN `category_string`.`catName5` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName5`)
    END), (CASE
        WHEN `category_string`.`catName6` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName6`)
    END), (CASE
        WHEN `category_string`.`catName7` IS NULL THEN ''
        ELSE CONCAT(' > ' + `category_string`.`catName7`)
    END)) = `taxmap`.`category_string`
    LEFT JOIN `" . $GLOBALS["schema"] . "`.`mc_taxonomy` AS `tax` ON `tax`.`id` = `taxmap`.`cattax_id`
    LEFT JOIN `" . $GLOBALS["schema"] . "`.`" . $GLOBALS["tableLead"] . "category_product` `prd` ON `prd`.`id_category` = coalesce(`category_string`.`prd_category7`, `category_string`.`prd_category6`, `category_string`.`prd_category5`, `category_string`.`prd_category4`, `category_string`.`prd_category3`, `category_string`.`prd_category2`, `category_string`.`prd_category1`) ORDER BY `final_category`) AS `a5` ON `a5`.`id_product` = `a1`.`id_product` ";
}

//create query portion - "where"
function reportQueryWhere(){
	return " WHERE (`a1`.`active` = 1) AND (`a1`.`available_for_order` = 1) AND (`a1`.`id_product` NOT IN (SELECT `id_product` FROM `" . $GLOBALS["schema"] . "`.`merchant_exclusion` WHERE `exclusion` = '" . $GLOBALS["merchantID"] . "'))";
}

////////////////////////////////////////////////////////////////////////
//File Creation Functions 
////////////////////////////////////////////////////////////////////////

//constructs the query from the three segements above
function queryBuilder($v){

	$query =  "SELECT DISTINCT " . reportQuerySelect() . reportQueryFrom() . reportQueryWhere();
	
	$headerlimit = " LIMIT 0,1 ";

	if($v == 'head'){return mysql_query($query . $headerlimit);} 
		else {return mysql_query($query);} 
};
//rpints the query being used for testing purposes
function printQueryBuilder($v){
    $query =  "SELECT DISTINCT " . reportQuerySelect() . reportQueryFrom() . reportQueryWhere();
    
    $headerlimit = " LIMIT 0,1 ";

    if($v == 'head'){return $query . $headerlimit;} 
        else {return $query;}
}
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
//checks for valid UPCs and those whos leading zeros may have been truncated up to two spaces
function upcFix($alias){
	return "(case
            when (length(`a1`.`upc`) = 12) then `a1`.`upc`
            when (length(`a1`.`upc`) = 11) then concat('0', `a1`.`upc`)
            when (length(`a1`.`upc`) = 10) then concat('00', `a1`.`upc`)
        end) AS `" . $alias . "`";
}
//creates a link to the primary image (full size)
function imageLink($alias){
	return "concat('http://" . $_SERVER["SERVER_NAME"] . "/', `a1`.`id_product`,'-large_default/',replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`a3`.`name`, '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '‾', ''), '+', ''), '<', ''), '=', ''), '>', ''), '↑', ''), '†', ''), '‡', ''), '‰', ''), '™', ''), '" . chr(92) .  "'', ''), '\"', ''), ' ', '-'), '---', '-'), '--', '-'), '.jpg') AS `" . $alias . "`";
}
//creates a link to the product page
function productLink($alias){
	return "concat('http://" . $_SERVER["SERVER_NAME"] . "/', `a4`.`link_rewrite`, '/', `a1`.`id_product`, '-', replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`a3`.`name`, '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '‾', ''), '+', ''), '<', ''), '=', ''), '>', ''), '↑', ''), '†', ''), '‡', ''), '‰', ''), '™', ''), '" . chr(92) .  "'', ''), '\"', ''), ' ', '-'), '---', '-'), '--', '-'), '.html') AS `" . $alias . "`";
}
//select product category or taxonomy if defined
function productCategory($alias){
	return " `a5`.`final_category` AS `" . $alias . "` ";
}
function productCategoryOrig($alias){
    return " `a5`.`ps_category_string` AS `" . $alias . "` ";
}
////////////////////////////////////////////////////////////////////////
//Bug Reporting Functions
////////////////////////////////////////////////////////////////////////

//checks to see if a bug has occured and displays a report module
function bugModal(){
	return "
	<div id=\"bugDiv\">
		<a href=\"#openModal\">Click Here to Report a Bug</a>
		<div id=\"openModal\" class=\"modalDialog\">
			<div>
				<a href=\"#close\" title=\"Close\" class=\"close\">X</a>
				<h2>Report a Bug</h2>
				<p>Form will be here that requests a description of the error and submits to <a target=\"_blank\" title=\"View Contact Function Page\" href=\"functions/contact.php\">contact function page</a></p>
			</div>
		</div>
	</div>";
}

?>