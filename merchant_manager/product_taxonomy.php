<?php 
//check for secure session 
sec_session_start(); 
if(login_check($mysqli) == true) { 
?>
	<h1><?php echo $GLOBALS["merchant"]; ?> Taxonomy Configuration</h1>
	<?php

		if(isset($_POST["category"])){ 
		$category = $_POST["category"];

			if(isset($_POST["apptax"])){
				setTaxonomyValue(getCattaxID($_POST["taxShortcut"]),mapID($category));
			}
			if(isset($_POST["levelSubmit"])){
				assignNewTaxonomy($_POST["levelval"],mapID($category));
			}
			for ($i = 1; $i <= 7; $i++)
			if(isset($_POST["levelRemove" . $i])){
				updateTaxonomy($_POST["levelvaltext" . $i],mapID($category),$i);
			}
			
	?>
	<table>
		<thead>
			<tr>
				<td colspan="2">
					<h2><?php echo $_POST["category"]; ?> Taxonomy Correlation (id: [<?php echo mapID($category); ?>])</h2>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					<form action="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $_GET["f"] . "&p=" . $_GET["p"]; ?>" method="POST" name="categorySelect"> 
						<input type="hidden" name="category" value="<?php echo $_POST["category"]; ?>"><br/>
						<?php echo displayTaxOptionList(1,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(2,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(3,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(4,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(5,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(6,categoryToTaxCheck($category)); ?>
						<?php echo displayTaxOptionList(7,categoryToTaxCheck($category)); ?>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $_GET["f"] . "&p=" . $_GET["p"]; ?>" title="go back">Back to Category Select</a>
				</td>
				<td>
					<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $_GET["f"]; ?>" title="go back">Back to <?php echo $GLOBALS["merchant"]; ?> Control Panel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?f=<?php echo $_GET["f"]; ?>&p=<?php echo $_GET["p"]; ?>" method="POST" name="taxonomySearch"> 
		<label for="search">Quick Apply - Search for Taxonomy Values</label>
		<input type="text" width="25" name="search">
		<input type="hidden" name="category" value="<?php echo $category; ?>">
		<input type="submit" value="Search"/>
	</form>
	<?php if(isset($_POST["search"])){echo taxonomySearch($_POST["search"],$category);}?>
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
			<tr>
				<td>
					<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $_GET["f"]; ?>" title="go back">Back to <?php echo $GLOBALS["merchant"]; ?> Control Panel</a>
				</td>
			</tr>
		</tbody>
	</table>
	<hr>
	<table style="width: 95%;">
		<thead>
			<td colspan="2">Categories to Taxonomy Status</td>
		</thead>
		<tbody>
			<tr><td><?php echo categoriesMissingTaxonomy(); ?></td></tr>
		</tbody>
	</table>
<?php 
	} 
}  //end secure session check 
?>