<?php

if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
	header("LOCATION: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	echo "Bitte zu HTTPS wechseln";
	exit();
}

session_start();
if(!isset($_SESSION["userid"]) || $_SESSION["userid"] == "") {
	if($_SERVER['REQUEST_URI'] == "/index.php" || $_SERVER['REQUEST_URI'] == "/" || $_SERVER['REQUEST_URI'] == "") {
		header("LOCATION: /login.php"); //default is index.php
	}else{
		header("LOCATION: /login.php?ref=".base64_encode($_SERVER["REQUEST_URI"]));
	}
	exit();
}
?>
