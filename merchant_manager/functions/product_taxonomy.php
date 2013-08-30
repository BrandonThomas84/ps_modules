<h1><?php echo $GLOBALS["merchant"]; ?> Taxonomy Configuration</h1>
<?php

if(isset($_POST["categories"])){ 
	$category = $_POST["categories"];
	categoryToTaxCheck($category);?>
<table>
	<thead>
		<tr>
			<td>
				<h2>Taxonomy Correlation</h2>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo categoryToTaxCheck($category); ?>
				<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?f=<?php echo $_GET["f"]; ?>&p=<?php echo $_GET["p"]; ?>" method="POST" name="categorySelect"> 
					<input type="text" value="<?php echo $_POST["categories"]; ?>" disabled=disabled><br/>
					<?php echo displayTaxOptionList(1,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(2,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(3,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(4,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(5,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(6,categoryToTaxCheck($category)); ?>
					<?php echo displayTaxOptionList(7,categoryToTaxCheck($category)); ?>
					<input type="submit" value="Update"/>
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $_GET["f"] . "&p=" . $_GET["p"]; ?>" title="go back">Back</a>
			</td>
		</tr>
	</tbody>
</table>
<?php } else { ?>
<table>
	<thead>
		<tr>
			<td>
				<h2>Select Category to Map</h2>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?f=<?php echo $_GET["f"]; ?>&p=<?php echo $_GET["p"]; ?>" method="POST" name="categorySelect"> 
					<?php echo distinctProductCategoryOptionList(); ?>
					<input type="submit" value="Select"/>
				</form>
			</td>
		</tr>
	</tbody>
</table>
<?php } ?>