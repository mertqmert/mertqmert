<?php

require_once("mc_db.php");


$litterID = strip_tags($_POST['litterID']);
$littertype = strip_tags($_POST['littertype']);
$description = strip_tags($_POST['description']);
$latitude = strip_tags($_POST['latitude']);
$longitude = strip_tags($_POST['longitude']);
$discordname = strip_tags($_POST['discordname']);



connectToDB::addLitter($litterID, $littertype, $description, $latitude, $longitude, $discordname);
echo("<h3>Your litter $litterID was added. Thank you $discordname !</h3>");

?>