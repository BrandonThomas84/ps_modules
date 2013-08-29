<?php require("config_functions.php");?>

<h1> <?php echo $GLOBALS["merchant"]; ?> Feed Settings</h1>
<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $GLOBALS["merch"]; ?>" target="_self" title="Back to feed manager">
	Back to <?php echo $GLOBALS["merchant"]; ?> Feed Manager
</a>
<?php echo messageReporting();?>
<div id="accordion">
	<?php displayConfigFields(); ?>
</div> 
<a href="<?php echo $_SERVER["PHP_SELF"] . "?f=" . $GLOBALS["merch"]; ?>" target="_self" title="Back to feed manager">
	Back to <?php echo $GLOBALS["merchant"]; ?> Feed Manager
</a>