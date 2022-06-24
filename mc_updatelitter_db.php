<?php

 require_once("mc_db.php");

$id = intval($_POST['litterID']);
//$litterID = strip_tags($_POST['litterID']);
$littertype = strip_tags($_POST['littertype']);
$description = strip_tags($_POST['description']);
$latitude = strip_tags($_POST['latitude']);
$longitude = strip_tags($_POST['longitude']);
$discordname = strip_tags($_POST['discordname']);

 connectToDB::updateLitterID( $id, $littertype, $description, $latitude, $longitude, $discordname);
 $name = connectToDB::getNameById($id);
 echo("LitterID $name was updated - Thank you $discordname!");
?>