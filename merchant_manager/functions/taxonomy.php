<?php
//instructs the merchant manager page to display the configure taxonomy button if there is a standard dataset present in the database
function taxonomyButton(){
	$sql = "SELECT DISTINCT `a2`.`merchant_id` FROM `testashop`.`mc_taxonomy` AS `a1` INNER JOIN `testashop`.`merchant_center_select_config` AS `a2` ON `a1`.`merchant_id` = `a2`.`merchant_id` WHERE `a1`.`merchant_id` = '" . $GLOBALS["merchantID"] . "';";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) > 0){
		return "<a class=\"button\" href=\"" . $_SERVER["PHP_SELF"] . "?f=" . $GLOBALS["merch"] . "&p=tax\" title=\"Product Taxonomy\">" . $GLOBALS["merchant"] ." Product Taxonomy</a>";
	}
}

//creates an option list containing all the categories that are currently configured in the prestashop database (up to 7 levels)
function distinctProductCategoryOptionList(){
	$sql = "SELECT DISTINCT " . productCategory("product_type") .  reportQueryFrom() . reportQueryWhere();
	$query = mysql_query($sql);
	
	echo "<select name=\"category\">";
	while($row = mysql_fetch_array($query)){
		echo "<option value=\"" . $row["product_type"] . "\">" . $row["product_type"] . "</option>";
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
		if(is_null($row["cattax_id"])){ 
			return 0;
		} else {
			return $row["cattax_id"];
		}
	}
}
//returns the row id for updating mc_cattax_mapping table
function mapID($category){
	$sql = "SELECT `id` FROM `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` WHERE `cattax_merchant_id` = '" . $GLOBALS["merchantID"] . "' AND `category_string` =  '" . $category . "';";
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	
	return $row["id"];
}
//creates the where for the query used to create the option list for the current taxonomy level
function levelWhere($level,$id){
	//constucting the 'where' statement for the initial query to identify the current taxonomy
	if($id == 0){$where = "";} else {$where = " WHERE `id` = '" . $id . "';";}

	$sql = "SELECT DISTINCT `level1` AS `1`,`level2` AS `2`,`level3` AS `3`,`level4` AS `4`,`level5` AS `5`,`level6` AS `6`,`level7` AS `7` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy`" . $where;
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);

	if(($level < 2)) {$l2 = "";} else {$l2 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level1`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["1"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if(($level < 3)) {$l3 = "";} else {$l3 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level2`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["2"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if(($level < 4)) {$l4 = "";} else {$l4 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level3`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["3"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if(($level < 5)) {$l5 = "";} else {$l5 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level4`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["4"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if(($level < 6)) {$l6 = "";} else {$l6 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level5`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["5"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}
	if(($level < 7)) {$l7 = "";} else {$l7 = " AND replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level6`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $row["6"] . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";}

	return $l2 . $l3 . $l4 . $l5 . $l6 . $l7;
}

//creates drop down menu for next taxonomy level
function displayTaxOptionList($level,$id){
	//constucting the 'where' statement for the initial query to identify the current taxonomy
	if($id == 0){$where = "";} else {$where = " WHERE `id` = '" . $id . "';";}

	$sql = "SELECT DISTINCT `level1` AS `1`,`level2` AS `2`,`level3` AS `3`,`level4` AS `4`,`level5` AS `5`,`level6` AS `6`,`level7` AS `7` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy`" . $where;
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	
	$levelSQL = "SELECT DISTINCT `level" . $level . "` as `values` FROM  `" . $GLOBALS["schema"] . "`.`mc_taxonomy` WHERE `merchant_id` = '" . $GLOBALS["merchantID"] . "' " . levelWhere($level,$id) . ";";
	$levelQuery = mysql_query($levelSQL);
	$levelNumRow = mysql_num_rows($levelQuery);
	
	if($level == 2 && $id == 0){} else {
		if($levelNumRow > 1){
			if(is_null($row[$level]) || ($id == 0 && $level == 1)){
				
				echo "<label style=\"width: 90px;display: inline-block;border-bottom: dotted 1px #ccc;\" for=\"level" . $level . "\"><strong>Level " . $level . "</strong></label>
				<select name=\"levelval\">";
					
				while($levelRow = mysql_fetch_array($levelQuery)){
					echo "<option " . feedConfigSelected($row[$level],$levelRow["values"]) ." value=\"" . $levelRow["values"] . "\">" . $levelRow["values"] . "</option>";
				}
					
				echo "
					</select>
					<input name=\"levelSubmit\"type=\"submit\" value=\"Add Level" . $level . " Value\"/>
					<br/>";	
			} else {
				echo "
				<label style=\"width: 90px;display: inline-block;border-bottom: dotted 1px #ccc;\" for=\"level" . $level . "\">Level " . $level . "</label>
				<input style=\"width: 250px;\" name=\"level " . $level . "\" type=\"text\" value=\"" . $row[$level] . "\" disabled=disabled>
				<input name=\"levelvaltext" . $level . "\" type=\"hidden\" value=\"" . $row[$level] . "\">
				<input name=\"levelRemove" . $level . "\"type=\"submit\" value=\"Delete";
				if($level ==1 ){echo " All";};
				echo "\"/><br/>";
			}
		}
	}
}

//return a single string value of all the taxonomy levels that are currently assigned to the category
function currentTaxonomyString($maxLevel){
	//start counter
	$i = 1;
	$a = array();
	
	while($i <= $maxLevel){
		//use SQL to remove all special characters that may appear for comparison
		$column = "replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`level" . $i . "`, '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '')";
		//check to see if there is already an assigned taxonomy value so that it can be used in the where
		if(categoryToTaxCheck($_POST["category"]) == 0){
			$where = "1=2";} 
			else {
				$where = " WHERE `id` = '" . categoryToTaxCheck($_POST["category"]) . "'";}
		//database select
		$sql = "SELECT " . $column . " AS `value` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy` " . $where . ";";
		$query = mysql_query($sql);
		@$row = mysql_fetch_array($query);
		array_push($a, $row["value"]);
		$i++;
	}

	return implode("",$a);
}
//returns currentTaxonomyString with the newest selection appended to the end
function assignNewTaxonomy($newValue,$mapID){
	$sql = "SELECT replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $newValue . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') AS `newValue`";
	$query = mysql_query($sql);
	$row = mysql_fetch_array($query);
	
	$string = currentTaxonomyString(7) . $row["newValue"];
	$stringSQL = "SELECT `id` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy` WHERE replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(CONCAT(COALESCE(`level1`, ''),COALESCE(`level2`, ''),COALESCE(`level3`, ''),COALESCE(`level4`, ''),COALESCE(`level5`, ''),COALESCE(`level6`, ''),COALESCE(`level7`, '')), '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = '" . $string . "';";
	$stringQUERY = mysql_query($stringSQL);
	

	//check for matching value to avoid taxonomy errors
	while($stringROW = mysql_fetch_array($stringQUERY)){
		if(mysql_num_rows($stringQUERY) == 1){
			$sql = "UPDATE `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` SET cattax_id = '" . $stringROW["id"] . "' WHERE `id` = '" . $mapID ."';";
			$query = mysql_query($sql);
			echo "<div class=\"sucMod\"><p>Success! You have added a new taxonomy value!</p></div><br>";
		} else {
			echo "<div class=\"errMod\"><p>Sorry, you selected an improper taxonomy value, please try again<p></div><br>" . $stringSQL;
		}
	}
}
//removes level (and all subsequent levels) of a taxonomy
function updateTaxonomy($removeValue,$mapID,$maxLevel){
	if($maxLevel == 1){
		$sql = "UPDATE `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` SET cattax_id = NULL WHERE `id` = '" . $mapID ."';";
		$query = mysql_query($sql);
	} else {
		$sql = "SELECT replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace('" . $removeValue . "', '-', ''), '#', ''), '$', ''), '%', ''), '" . chr(38) . "', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') AS `removeValue`";
		$query = mysql_query($sql);
		$row = mysql_fetch_array($query);

		$str = substr(currentTaxonomyString($maxLevel),0,strpos(currentTaxonomyString($maxLevel),$row["removeValue"]));
		$strCheck = substr(currentTaxonomyString($maxLevel),0,strlen(currentTaxonomyString($maxLevel))-strlen($row["removeValue"])); 
		if($str == $strCheck){$string = $str;} else {$string = $strCheck;};
		
		$stringSQL = "SELECT `id` FROM `" . $GLOBALS["schema"] . "`.`mc_taxonomy` WHERE replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(CONCAT(COALESCE(`level1`, ''),COALESCE(`level2`, ''),COALESCE(`level3`, ''),COALESCE(`level4`, ''),COALESCE(`level5`, ''),COALESCE(`level6`, ''),COALESCE(`level7`, '')), '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '+', ''), '<', ''), '=', ''), '>', ''), '\'', ''), '\"', ''), ' ', ''), '---', ''), '--', '') = '" . $string . "';";
		$stringQUERY = mysql_query($stringSQL);
		
		while($stringROW = mysql_fetch_array($stringQUERY)){
			//check for matching value to avoid taxonomy errors	
			if(mysql_num_rows($stringQUERY) == 1){
				$sql = "UPDATE `" . $GLOBALS["schema"] . "`.`mc_cattax_mapping` SET cattax_id = '" . $stringROW["id"] . "' WHERE `id` = '" . $mapID ."';";
				$query = mysql_query($sql);
			} 
		}
	}
}
?>