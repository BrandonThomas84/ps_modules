<?php

function merchantOptionList(){
	$sql = "SELECT DISTINCT `merchant_id` FROM `" . $GLOBALS["schema"] . "`.`merchant_center_select_config` ORDER BY `merchant_id`";
	$query = mysql_query($sql);
	echo "<option value=\"ap\">View All Products</option>";
	
	while($row = mysql_fetch_array($query)){
		echo "<option value=\"" . $row["merchant_id"] . "\">" . strtoupper(substr($row["merchant_id"],0,1)) . substr($row["merchant_id"],1,(strlen($row["merchant_id"])-1))  . "</option>";
		}
}

if(isset($_GET["mthd"])){
	$method = $_GET["mthd"];
		
	if($method == "ap"){
		$methodName = "Product";
		$methodSub = "";
		} else {
			$methodName = " Specific";
			$methodSub = strtoupper(substr($method,0,1)) . substr($method,1,(strlen($method)-1));
		}
		
	echo "You have selected the " . $methodSub . " " . $methodName . " method.";
} else {
	
?>

<h1>Please Select a Viewing Method</h1>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="GET" enctype="application/x-www-form-urlencoded" title="Merchant Selection" name="merchant">
	<input type="hidden" name="f" value="exmng">
    <select name="mthd">
    	<?php merchantOptionList(); ?>
	</select>
    <input type="submit" value="Select Merchant Method">
</form>

<?php }; ?>