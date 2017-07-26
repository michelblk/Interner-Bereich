<?php
require("include/php/auth.php");
require("include/php/db.php");

if($_SERVER['REQUEST_METHOD'] == "GET"){
	if(isset($_GET['getUserData']) && isset($_GET["user"]) && $_GET["user"] != "") {
		session_write_close(); //!!! Schließt Session und gibt sie für andere Abfragen frei

		$userid = mysqli_real_escape_string($mysqli, $_GET["user"]);

		$output = array();

		$query = "SELECT `int__benutzer`.`user_id`, `int__benutzer`.`Nachname`, `int__benutzer`.`Vorname`, `int__benutzer`.`Telefon`, `int__benutzer`.`Mobil`, `int__benutzer`.`Mail`, `int__benutzer`.`Strasse`, `int__benutzer`.`Wohnort`, `int__benutzer`.`PLZ`, `int__gruppen`.`gruppen_id`, `int__gruppen`.`Beschreibung`, `int__gruppen`.`Prioritaet`, (SELECT MAX(`Prioritaet`) FROM `int__gruppen`,`int__benutzer-gruppen` WHERE `int__gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer`.`user_id` = `int__benutzer-gruppen`.`user_id`) AS 'maxPrioritaet' FROM `int__benutzer` LEFT JOIN `int__benutzer-gruppen` ON `int__benutzer`.`user_id` = `int__benutzer-gruppen`.`user_id` LEFT JOIN `int__gruppen` ON `int__benutzer-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id` HAVING `int__benutzer`.`user_id` = '".$userid."' ORDER BY `int__gruppen`.`Beschreibung` ASC";
		$query = $mysqli->query($query);
		while($result = $query->fetch_array(MYSQLI_ASSOC)) {
			if(!isset($output["userid"])) { //einmalig allg. Informationen schreiben
				$output["userid"] = $result["user_id"];
				$output["Nachname"] = $result["Nachname"];
				$output["Vorname"] = $result["Vorname"];
				$output["Telefon"] = $result["Telefon"];
				$output["Mobil"] = $result["Mobil"];
				$output["Mail"] = $result["Mail"];
				$output["Strasse"] = $result["Strasse"];
				$output["Wohnort"] = $result["Wohnort"];
				$output["PLZ"] = $result["PLZ"];
				$output["maxPrioritaet"] = $result["maxPrioritaet"];
				$output["gruppen"] = array();
			}
			if($result["gruppen_id"] != null){
				$gruppe = array();
				$gruppe["id"] = $result["gruppen_id"];
				$gruppe["beschreibung"] = $result["Beschreibung"];
				$gruppe["Prioritaet"] = $result["Prioritaet"];
				$output["gruppen"][] = $gruppe;
			}
		}
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output, JSON_UNESCAPED_UNICODE);

		exit;
	}else
	if(isset($_GET["getUserImage"]) && isset($_GET["user"]) && $_GET["user"] != "") {
		session_write_close(); //!!! Schließt Session und gibt sie für andere Abfragen frei -> mehrere Bilderabfragen gleichzeitig erlauben
		$user = $_GET["user"];

		$outputfile = "";

		if(isset($_GET["o"])) {
			header("Content-Type: image/jpeg");
			if(file_exists("include/php/userdata/images/".$user.".jpg")) {
				$outputfile = "include/php/userdata/images/".$user.".jpg";
			}else{
				$outputfile = "include/php/userdata/images/anonym.jpg";
			}
		}else
		{ //small image
			header("Content-Type: image/png");
			if(file_exists("include/php/userdata/images/".$user.".jpg")) {
				if(!file_exists("include/php/userdata/images/small/".$user.".png")){
					resizeImage("include/php/userdata/images/".$user.".jpg", "include/php/userdata/images/small/".$user.".png", 150);
				}
				$outputfile = "include/php/userdata/images/small/".$user.".png";
			}else{
				if(!file_exists("include/php/userdata/images/small/anonym.jpg"))
					resizeImage("include/php/userdata/images/anonym.jpg", "include/php/userdata/images/small/anonym.png", 150);
				$outputfile = "include/php/userdata/images/small/anonym.png";
			}
		}

		//cache control
		$headers = apache_request_headers();
		header("Cache-Control: public, max-age=" . 30 * 60); // 30 Minute cache
		header("Cache-Control: pre-check=" . 30 * 60, FALSE);
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 30 * 60) ." GMT");
		if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($outputfile))) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($outputfile)).' GMT', true, 304);
		} else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($outputfile)).' GMT', true, 200);
			header('Content-Length: '.filesize($outputfile));

			echo file_get_contents($outputfile);
		}
		exit;
	}
}
http_response_code(404);
exit;

function resizeImage ($path, $newpath, $size, $quality = 0) {
	list($width, $height) = getimagesize($path);
	if($width > $height) {
		$ratio = $size/$width;
		$newwidth = $size;
		$newheight = $height * $ratio;
		$off_x = 0;
		$off_y = ($size - $newheight)/2;
	}else{
		$ratio = $size/$height;
		$newheight = $size;
		$newwidth = $width* $ratio;
		$off_y = 0;
		$off_x = ($size - $newwidth)/2;
	}

	// Bild laden
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$source = imagecreatefromjpeg($path);

	// Skalieren
	imagecopyresampled($thumb, $source, $off_x, $off_y, 0, 0, $newwidth, $newheight, $width, $height);

	// Ausgabe
	imagepng($thumb, $newpath, $quality);
	imagedestroy($thumb); //free up memory
}
?>
