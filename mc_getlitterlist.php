<?php
 require_once("mc_db.php");

 $arr = connectToDB::getJsonEncodedLitterList();

 echo($arr);
?>