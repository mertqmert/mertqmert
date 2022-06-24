<?php
	require_once("mc_db.php");
	$id = intval($_POST['litterID']);
	$name = connectToDB::getNameById($id);

	connectToDB::deleteLitterID($id);
	echo("Litter $name was deleted.");
?>
