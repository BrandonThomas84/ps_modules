<?php sec_session_start(); if(login_check($mysqli) == true) { ?>
<?php

function excludedProductsQuery(){
	return "
	SELECT DISTINCT 
		`a2`.`id`,
		`a1`.`id_product`,
		concat('http://" . $_SERVER["SERVER_NAME"] . "/', `a4`.`link_rewrite`, '/', `a1`.`id_product`, '-', replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`a3`.`name`, '-', ''), '#', ''), '$', ''), '%', ''), '&', ''), '(', ''), ')', ''), '*', ''), ',', ''), '.', ''), '/', ''), ':', ''), ';', ''), '?', ''), '@', ''), '[', ''), ']', ''), '_', ''), '`', ''), '{', ''), '|', ''), '}', ''), '~', ''), '‘', ''), '‹', ''), '›', ''), '‾', ''), '+', ''), '<', ''), '=', ''), '>', ''), '↑', ''), '†', ''), '‡', ''), '‰', ''), '™', ''), '" . chr(92) .  "'', ''), '\"', ''), ' ', '-'), '---', '-'), '--', '-'), '.html') AS `link` 
	
	FROM `" . $GLOBALS["schema"] . "`.`ps_product` AS `a1`
	
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
	
	LEFT JOIN `" . $GLOBALS["schema"] . "`.`merchant_exclusion` AS `a2`
	ON `a1`.`id_product` = `a2`.`id_product`
		AND `a2`.`exclusion` = '" . $GLOBALS["merchantID"] . "'
	WHERE `a2`.`id` IS NOT NULL";
}

function displayExcludedProducts(){
	if(isset($_GET["page"])){
		$pageNumber = $_GET["page"];
	} else {
		$pageNumber = 0;
	}

	if(isset($_GET["perpage"])){
		$perPage = $_GET["perpage"];
	} else {
		$perPage = 10;
	}

	$limitStart = $pageNumber*$perPage;
	$limitRun = $perPage;

	$sql = excludedProductsQuery() . " ORDER BY `id_product` LIMIT " . $limitStart . "," . $limitRun . ";";

	$query = mysql_query($sql);
	
	while($row = mysql_fetch_array($query)){
		echo "
		<tr>
		  <td><p>" . $row["id_product"] . "</p></td>
		  <td><a target=\"_blank\" href=\"" . $row["link"] . "\" title=\"View Product\">Click Here</a></td>
		  <td>
		  	<form action=\"functions/exclusion_manage.php\" name=\"removeProductExclusion\" method=\"POST\" enctype=\"application/x-www-form-urlencoded\" title=\"Remove [Product] from [Merchant] Exclusions\">
		  		<input type=\"hidden\" name=\"id\" id=\"removeID\" value=\"" . $row["id"] . "\" >
		  		<input type=\"hidden\" name=\"merchantID\" id=\"merchantID\" value=\"" . $GLOBALS["merchantID"] . "\" >
		  		<input type=\"hidden\" name=\"merch\" id=\"merch\" value=\"" . $GLOBALS["merch"] . "\" >
		  		<input type=\"submit\" value=\"Remove\" name=\"submitRemove\">
		  	</form>
		  </td>
		</tr>";
	}
}

function pageNavigation(){
	if(isset($_GET["page"])){$pageNumber = $_GET["page"];} else {$pageNumber = 0;}
	if(isset($_GET["perpage"])){$perPage = $_GET["perpage"];} else {$perPage = 10;}

	$nextPage = $pageNumber+1;
	
	//previous page display only if it is not the first page
	if($pageNumber == 0){
		$previousPage = $pageNumber;
		$previousLinkDisplay = "";
	} else {
		$previousPage = $pageNumber-1;
		$previousLinkDisplay = "<a href=\"" . $_SERVER["PHP_SELF"] . "?f=" .  $_GET["f"] . "&p=" . $_GET["p"] . "&page=" . $previousPage . "&perpage=" . $perPage . "\" title=\"previous page\">Previous</a>";
	}

	if((mysql_num_rows(mysql_query(excludedProductsQuery())) - ($pageNumber+1*$perPage)) <= 0) {
		$nextLinkDisplay = "";}
		else {
			$nextLinkDisplay = "<a href=\"" . $_SERVER["PHP_SELF"] . "?f=" .  $_GET["f"] . "&p=" . $_GET["p"] . "&page=" . $nextPage . "&perpage=" . $perPage . "\" title=\"next page\">Next</a>";
		}
		
	
	echo "
		<tr><td colspan=\"3\">Navigation - Current Page: " . ($pageNumber+1) . "</td></tr>
		<tr>
			<td class=\"pageControl\">" . $previousLinkDisplay . "</td>
			<td class=\"pageControl\">
				<form action=\"" . $_SERVER["PHP_SELF"] . "\" name=\"removeProductExclusion\" method=\"GET\" enctype=\"application/x-www-form-urlencoded\" title=\"Remove [Product] from [Merchant] Exclusions\">
			  		<input type=\"hidden\" name=\"p\" value=\"exmng\" >
			  		<input type=\"hidden\" name=\"f\"  value=\"" . $GLOBALS["merch"] . "\" >
			  		<input type=\"hidden\" name=\"page\" value=\"" . $pageNumber . "\" >
			  		<select name=\"perpage\">
	        			<option value=\"10\" " . feedConfigSelected(10,$perPage) . ">10</option>
	        			<option value=\"20\" " . feedConfigSelected(20,$perPage) . ">20</option>
	        			<option value=\"30\" " . feedConfigSelected(30,$perPage) . ">30</option>
	        			<option value=\"40\" " . feedConfigSelected(40,$perPage) . ">40</option>
	        			<option value=\"50\" " . feedConfigSelected(50,$perPage) . ">50</option>
	        			<option value=\"100\" " . feedConfigSelected(100,$perPage) . ">100</option>
	        		</select>
			  		<input type=\"submit\" value=\"Change\" name=\"perPageSubmit\">
			  	</form>
			</td>
			<td class=\"pageControl\">" . $nextLinkDisplay . "</td>
		</tr>";
}

?>

<h1><?php echo $GLOBALS["merchant"]; ?></h1>
<table border="1">
	<thead style="background: #999; color: #fff;">
	  <td colspan="3">
		<strong>Add a new product to the exclusions list</strong>
	  </td>
	</thead>
	<tbody>
	  <tr>
		<td colspan="2">
		  <form action="<?php echo "functions/exclusion_manage.php" ;?>" name="addProductExclusion" method="POST" enctype="application/x-www-form-urlencoded" title="Add [Product] to [Merchant] Exclusions">
		    <label for="addID">Enter Product Number</label>
		    <input type="hidden" name="merchantID" id="merchantID" value="<?php echo $GLOBALS["merchantID"] ;?>">
		  	<input type="hidden" name="merch" id="merch" value="<?php echo $GLOBALS["merch"] ;?>" >
		  	<input type="text" size="30" maxlength="150" name="id_product" id="addID">
		</td>
		<td>
		    <input type="submit" value="Add" name="submitAdd">
		  </form>
		</td>
	  </tr>
</table>
<div class="clear"></div>
<table border="1">
	<thead style="background: #999; color: #fff;">
		<td><strong>Product ID</strong></td>
		<td><strong>Link<strong></td>
		<td><strong>Action</strong></td>
	</thead>
	<tbody>
		<?php displayExcludedProducts(); ?>
		<?php pageNavigation(); ?>
	</tbody>
</table>
<?php ;} ?>