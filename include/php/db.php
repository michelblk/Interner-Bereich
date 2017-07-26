<?php

function connect_db () {
	return new mysqli("domain", "user", "password", "database", 3306);
}

$mysqli = connect_db();
if($mysqli->connect_errno){http_response_code(503);echo "Fehler mit der Datenbankverbindung! Dienst steht vorübergehend nicht zur Verfügung.";exit();}
$mysqli->set_charset("utf8mb4");

function check_right ($rechtenr) {
	global $mysqli;
	return ($mysqli->query("SELECT `int__gruppen-rechte`.`rechtnr` FROM `int__gruppen-rechte`, `int__benutzer-gruppen` WHERE `int__benutzer-gruppen`.`gruppen_id` LIKE `int__gruppen-rechte`.`gruppen_id` AND `int__gruppen-rechte`.`rechtnr` LIKE '$rechtenr' AND `int__benutzer-gruppen`.`user_id` LIKE '".$_SESSION["userid"]."' LIMIT 1")->num_rows == 1);
}

?>
