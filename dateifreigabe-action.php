<?php
require("include/php/auth.php");
require("include/php/db.php");
$basefolder = "include/php/userdata/filesharing/";

function checkfolder($folder) {
	global $basefolder;
	if(is_dir($basefolder.$folder) && basename($folder) == ".."){ //.. nicht erlaubt (am Ende)
		http_response_code(403); //forbidden
		echo "Nicht erlaubt";
		exit();
	}
	$f = explode("/", $folder); // .. als Ordnername nicht erlaubt
	foreach($f as $tmp) {
		if ($tmp == "..") {
			http_response_code(403); //forbidden
			echo "Nicht erlaubt";
			exit();
		}
	}
	return $folder;
}

function correctName($name) {
	// nur wörter, leerzeichen, zahlen und -_~,;[]().&+' erlauben
	$erg = preg_replace("([^\w\s\d\-_~,;\[\]\(\)&+'.äÄüÜöÖß])", '', $name);
	// Doppelte Punkte entfernen
	$erg = preg_replace("([\.]{2,})", '', $erg);
	return $erg;
}

function rmdirr($dir) { //loesche Ordner mit Inhalt
	// Wenn der Input ein Ordner ist, dann Überprüfung des Inhaltes beginnen
	if (is_dir($dir)) {
		// Ordnerinhalt auflisten und jedes Element nacheinander überprüfen
		$dircontent=scandir($dir);
		foreach ($dircontent as $c) {
			// Wenn es sich um einen Ordner handelt, die Funktion rmr(); aufrufen
			if ($c != '.' && $c != '..' && is_dir($dir.'/'.$c)) {
				rmdirr($dir.'/'.$c);
				// Wenn es eine Datei ist, diese löschen
			} else if ($c != '.' && $c != '..') {
				unlink($dir.'/'.$c);
			}
		}
		// Den nun leeren Ordner löschen
		rmdir($dir);
		// Wenn es sich um eine Datei handelt, diese löschen
	} else {
		unlink($dir);
	}
}

function formatSizeUnits($bytes)
{ // https://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_GET["neu"])){
		if(isset($_POST["type"]) && isset($_POST["id"])) {
			if($_POST["type"] == "ordner" && isset($_POST["name"])) { //neuer Ordner
				if($_POST["id"] == "" || $_POST["id"] == "/") { //Hauptordner
					if(check_right(5)){ //darf Hauptordner erstellen
						if(isset($_POST["gruppe"]) && is_array($_POST["gruppe"])) {
							$name = correctName($_POST["name"]);
							if(strlen($name) > 0 && count($_POST["gruppe"]) > 0) {
								$check = $mysqli->query("SELECT `ordner_id` FROM `int__dateifreigabe-ordner` WHERE `name` = '$name' LIMIT 1")->num_rows;
								if($check == 0){ //Ordner existiert

									$right6 = check_right(6);
									$erlaubteGruppen = array();
									if(!$right6) { // darf nicht für jede Gruppe einen Ordner erstellen
										$gruppen = $mysqli->query("SELECT `gruppen_id` FROM `int__benutzer-gruppen` WHERE `user_id` = '".$_SESSION["userid"]."'");
										while($gruppe = $gruppen->fetch_array(MYSQLI_ASSOC)) {
											$erlaubteGruppen[] = $gruppe["gruppen_id"];
										}
									}

									$query = $mysqli->query("INSERT INTO `int__dateifreigabe-ordner` (`name`) VALUES ('$name')");
									$ordnerid = $mysqli->insert_id;
									if($query && $ordnerid > 0){
										$sql = "INSERT INTO `int__dateifreigabe-ordner-gruppen`(`ordner_id`, `gruppen_id`, `schreiben`) VALUES ";
										$key = 0;
										foreach($_POST["gruppe"] as $groupid => $val) {
											$schreiben = ($val == "rw" ? "1":"0");

											//Recht überprüfen
											if(!$right6 && !in_array($groupid, $erlaubteGruppen)){
												http_response_code(401); //Unauthorized
												exit();
											}
											$sql.="('".$ordnerid."', '".$groupid."', '".$schreiben."')";
											if($key < count($_POST["gruppe"])-1){$sql.=",";}
											$key++;
										}

										if($mysqli->query($sql)) { //Erfolg
											// Ordner erstellen !
											mkdir($basefolder.$ordnerid, 0777, false); //Erstellen von Unterordnern nicht erlauben (darf eigentlich durch den Filter nicht passieren)

											$tmp = array();
											$tmp["id"] = $ordnerid;
											$tmp["name"] = $name;

											header('Content-Type: application/json; charset=utf-8');
											echo json_encode($tmp, JSON_UNESCAPED_UNICODE);
											exit();
										}else{
											// Fehler beim Hinzufügen der Gruppen
											http_response_code(500);
											exit();
										}
									}else{
										// Fehler beim Einfügen
										http_response_code(500);
										exit();
									}
								}else{
									//Ordnername existiert bereits
									http_response_code(409); //Conflict
									exit();
								}
							}else{
								// Ordnername leer, nicht verwendbar oder keine Gruppen gewählt
								http_response_code(406); //not acceptable
								exit();
							}
						}else{
							// Gruppen nicht definiert
							http_response_code(400); //bad request
							exit();
						}
					}else{
						http_response_code(401); //Unauthorized
						exit();
					}
				}else{ //Neuer Unterordner
					$name = correctName($_POST["name"]);

					// Ordner herausfinden
					$folder = trim(checkfolder($_POST["id"]), '/') . '/';
					$mainfolder = explode("/", $folder)[0];
					$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
					if($ordner->num_rows == 1) { //Hauptordner gefunden
						$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
						$rw = $ordner["schreiben"]; //boolean
						$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
						if($rw || check_right(5)) { //hat schreibrechte, oder darf Hauptordner erstellen
							//Ordner erstellen
							if(strlen($name) > 0) {
								if(!is_dir($basefolder.$path.$name)) {
									mkdir($basefolder.$path.$name, 0777, false);
									if(!is_dir($basefolder.$path.$name)){
										//etwas ist schiefgelaufen
										http_response_code(500);
										exit();
									}
									$tmp = array();
									$tmp["name"] = $name;
									header('Content-Type: application/json; charset=utf-8');
									echo json_encode($tmp, JSON_UNESCAPED_UNICODE);
									exit();
								}else{ //Ordner existiert bereits
									http_response_code(409); //Conflict
									exit();
								}
							}
						}else{
							http_response_code(401); //Unauthorized
							exit();
						}
					}
				}
			}
		}
	}else //neu Ende
	if(isset($_GET["loeschen"])) {
		if(isset($_POST["type"]) && isset($_POST["id"])) {
			if($_POST["type"] == "ordner" && isset($_POST["name"])) { //ordner löschen
				$name = $_POST["name"];
				// Ordner herausfinden
				$folder = trim(checkfolder($_POST["id"]), '/') . '/';
				if($folder == "/") { //wenn Hauptordner
					$mainfolder = $_POST["name"];
				}else{ //Unterordner
					$mainfolder = explode("/", $folder)[0];
				}
				$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
				if($ordner->num_rows == 1) { //Hauptordner gefunden
					// ! Da in der Dateifreigabe nur Ordner der eigenen Gruppen stehen, kann man hierüber auch nur Ordner löschen, der zu einer der eigenen Gruppen gehört
					$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
					$rw = $ordner["schreiben"]; //boolean
					$ordnerid = $ordner["ordner_id"];
					$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
					$right5 = check_right(5);
					if($rw || $right5){ // Wenn man einen Hauptordner erstellen darf, darf man ihn auch nur mit leserechten löschen
						if($folder == "/" || $folder == "") { //Hauptordner
							if($right5){ //Hauptordner erstellen
								if(is_dir($basefolder.$path)) {
									rmdirr($basefolder.$path);
									$query = $mysqli->query("DELETE FROM `int__dateifreigabe-ordner` WHERE `ordner_id` = '$ordnerid' LIMIT 1");
									$query = $mysqli->query("DELETE FROM `int__dateifreigabe-ordner-gruppen` WHERE `ordner_id` = '$ordnerid'");
									if(!is_dir($basefolder.$path)) { //Erfolg
										http_response_code(204); // kein Inhalt
										exit();
									}else{
										http_response_code(500);
										exit();
									}
								}else{
									http_response_code(400); //Bad request
									exit();
								}
							}else{
								http_response_code(401); //Unauthorized
								exit();
							}
						}else{ //Unterordner
							if(is_dir($basefolder.$path.$name)) {
								rmdirr($basefolder.$path.$name);
								if(is_dir($basefolder.$path.$name)) { //Ordner existiert noch, etwas ist schief gelaufen
									http_response_code(500); // Internal Server Error
									exit();
								}else{
									http_response_code(204); //ohne Inhalt
									exit();
								}
							}else{ //Ordner existiert nicht
								http_response_code(400); //Bad request
								exit();
							}
						} //Unterordner ende
					}else{ //wenn kein Schreibrecht
						http_response_code(401); //Unauthorized
						exit();
					}
				}else{ //Hauptordner nicht gefunden
					http_response_code(404);
					exit();
				}
			}else
			if($_POST["type"] == "file" && isset($_POST["name"])) {
				$name = $_POST["name"];
				// Ordner herausfinden
				$folder = trim(checkfolder($_POST["id"]), '/') . '/';
				if($folder == "/") { //wenn Hauptordner
					http_response_code(400); //bad request
					exit();
				}else{ //Unterordner
					$mainfolder = explode("/", $folder)[0];
				}

				$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");

				if($ordner->num_rows == 1) { //Hauptordner gefunden
					$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
					$rw = $ordner["schreiben"]; //boolean
					$ordnerid = $ordner["ordner_id"];
					$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
					$right5 = check_right(5);
					if($rw || $right5){
						if(is_file($basefolder.$path.$name)) {
							unlink($basefolder.$path.$name);
							if(is_file($basefolder.$path.$name)) { //Datei existiert noch, etwas ist schief gelaufen
								http_response_code(500); // Internal Server Error
								exit();
							}else{
								http_response_code(204); //ohne Inhalt
								exit();
							}
						}else{ //Ordner existiert nicht
							http_response_code(400); //Bad request
							exit();
						}
					}else{
						http_response_code(401); //Unauthorized
						exit();
					}
				}else{
					http_response_code(404);
					exit();
				}
			}
		}
	}else
	if(isset($_GET["umbenennen"])) {
		if(isset($_POST["type"]) && isset($_POST["id"]) && isset($_POST["alterName"]) && isset($_POST["neuerName"])) {
			if($_POST["type"] == "ordner") {
				if($_POST["id"] == "" || $_POST["id"] == "/") { //Hauptordner
					$alterName = mysqli_real_escape_string($mysqli, $_POST["alterName"]);
					$neuerName = mysqli_real_escape_string($mysqli, correctName($_POST["neuerName"]));
					$mysqli->query("UPDATE `int__dateifreigabe-ordner` SET `name`='$neuerName' WHERE `name`='$alterName' LIMIT 1");
					if($mysqli->affected_rows == 1){
						//Erfolg
						http_response_code(204); //Ohne Inhalt
						exit();
					}else{
						http_response_code(404);
						exit();
					}
				}else{ //Unterordner
					$folder = trim(checkfolder($_POST["id"]), '/') . '/';
					$mainfolder = explode("/", $folder)[0];
					$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
					if($ordner->num_rows == 1) { //Hauptordner gefunden
						// ! Da in der Dateifreigabe nur Ordner der eigenen Gruppen stehen, kann man hierüber auch nur Ordner umbenennen, der zu einer der eigenen Gruppen gehört
						$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
						$rw = $ordner["schreiben"]; //boolean
						$ordnerid = $ordner["ordner_id"];
						if($rw || check_right(5)){
							$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
							$alterName = $_POST["alterName"];
							$neuerName = correctName($_POST["neuerName"]);
							if(rename($basefolder.$path.$alterName, $basefolder.$path.$neuerName)) {
								//Erfolg
								http_response_code(204); //Ohne Inhalt
								exit();
							}else{
								http_response_code(404);
								exit();
							}
						}else{
							http_response_code(401); //Unauthorized
							exit();
						}
					}else{ //Hauptordner nicht gefunden
						http_response_code(404);
						exit();
					}
				}
			}else
			if($_POST["type"] == "datei") {
				$folder = trim(checkfolder($_POST["id"]), '/') . '/';
				$mainfolder = explode("/", $folder)[0];
				$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
				if($ordner->num_rows == 1) { //Hauptordner gefunden
					$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
					$rw = $ordner["schreiben"]; //boolean
					if($rw || check_right(5)){
						$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));

						$alterName = $_POST["alterName"];
						$neuerName = correctName($_POST["neuerName"]);
						if(rename($basefolder.$path.$alterName, $basefolder.$path.$neuerName)) {
							//Erfolg
							http_response_code(204); //Ohne Inhalt
							exit();
						}else{
							http_response_code(404);
							exit();
						}
					}else{
						http_response_code(401); //Unauthorized
						exit();
					}
				}else{ //Hauptordner nicht gefunden
					http_response_code(404);
					exit();
				}
			}
		}
	}else
	if(isset($_GET["upload"])) {
		if(isset($_POST["type"]) && isset($_POST["id"])) {
			if($_POST["type"] == "dateien" && isset($_FILES["dateien"])) {
				if($_POST["id"] != "" || $_POST["id"] == "/"){ //Muss ein Unterordner sein
					$folder = trim(checkfolder($_POST["id"]), '/') . '/';
					$mainfolder = explode("/", $folder)[0];
					$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
					session_write_close(); // !!!!!!!!!!!!!!
					if($ordner->num_rows == 1) { //Hauptordner gefunden
						$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
						$rw = $ordner["schreiben"]; //boolean
						$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
						if($rw || check_right(5)){
							$log = "";
							foreach($_FILES["dateien"]["name"] as $i => $datei) {
								if($_FILES["dateien"]["tmp_name"][$i]) { // muss existieren
									$dateiname = correctName($_FILES["dateien"]["name"][$i]);
									if(file_exists($basefolder.$path.$dateiname)) {
										$nr = 1;
										$fileinfo = pathinfo($dateiname);
										while(file_exists($basefolder.$path.$fileinfo["filename"]."_".$nr.".".$fileinfo["extension"]) && $nr < 100) { //max 99 versuche
											$nr++;
										}
										$dateiname = $fileinfo["filename"]."_".$nr.".".$fileinfo["extension"];
									}
									if (!move_uploaded_file($_FILES['dateien']['tmp_name'][$i], $basefolder.$path.$dateiname)) {
										$log .= $dateiname." konnte nicht hochgeladen werden\n";
									}
								}
							}
							if($log != "") {
								header('Content-Type: text/plain; charset=utf-8');
								echo $log;
								exit();
							}else{
								http_response_code(204); //Ohne Antwort
								exit();
							}
						}else{
							http_response_code(401); //Unauthorized
							exit();
						}
					}else{ //Hauptordner nicht gefunden
						http_response_code(404);
						exit();
					}
				}else{ //ist ein Hauptordner
					http_response_code(403); //forbidden
					exit();
				}
			}else{
				http_response_code(404);
				echo "Es können derzeit nur Dateien hochgeladen werden";
				exit();
			}
		}
	}
}else
if($_SERVER["REQUEST_METHOD"] == "GET") {
	if(isset($_GET["folder"]) && $_GET["folder"] != "" && isset($_GET["download"])) {
		require('include/php/zip.php');
		ignore_user_abort(true);
		$ordner = trim(checkfolder($_GET["folder"]), '/') . '/';
		$mainfolder = explode("/", $ordner)[0];
		$ordnerid = $mysqli->query("SELECT `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
		session_write_close();
		if($ordnerid->num_rows != 1) {
			http_response_code(404);
			exit();
		}
		$ordnerid = $ordnerid->fetch_array(MYSQLI_ASSOC);
		$ordner = $ordnerid["ordner_id"].substr($ordner, strlen($mainfolder), strlen($ordner));
		$zip_name = 'zip_' . time();
		$zip_directory = $basefolder."zip/";
		$zip = new zip( $zip_name, $zip_directory );
		chdir($basefolder.$ordner."../");
		$zip->add_directory(basename($ordner));
		chdir(dirname(__FILE__)); //zurücksetzen
		$zip->save();

		$path = $zip->get_zip_path();
		header('Content-type:  application/zip');
		header('Content-Length: ' . filesize($path));
		header('Content-Disposition: attachment; filename="'.basename($_GET["folder"]).'.zip"');
		header("Pragma: no-cache");
		header("Expires: 0");

		register_shutdown_function('unlink', $file);

		readfile($path);
		unlink($path);

		exit();
	}else
	if(isset($_GET["ordnerEigenschaften"]) && $_GET["ordnerEigenschaften"] != "") {
		$ordner = trim(checkfolder($_GET["ordnerEigenschaften"]), '/');

		$mainfolder = explode("/", $ordner)[0];
		$ordnerid = $mysqli->query("SELECT `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
		if($ordnerid->num_rows != 1) {
			http_response_code(404);
			exit();
		}
		$ordnerid = $ordnerid->fetch_array(MYSQLI_ASSOC);
		$ordnerid = $ordnerid["ordner_id"];

		$path = $ordnerid.substr($ordner, strlen($mainfolder), strlen($ordner));

		$output = array();
		$output["name"] = $ordner;
		$output["id"] = $ordnerid;

		// Inhalt bestimmen
		$dateien = 0;
		$ordner = 0;
		$groesse = 0;
		function scan ($ordnername){
			global $dateien;
			global $ordner;
			global $groesse;
			$y=scandir($ordnername);
			foreach($y as $z){
				if(is_dir($ordnername."/".$z) && basename($z) != "." && basename($z) != ".."){
					$ordner++;
					scan($ordnername."/".$z);
				}
				if(is_file($ordnername."/".$z)) {
					$dateien++;
					$groesse += filesize($ordnername."/".$z);
				}
			}
		}
		scan($basefolder.$path);

		$output["groesse"] = formatSizeUnits($groesse);
		$output["ordner"] = $ordner;
		$output["dateien"] = $dateien;

		$query = $mysqli->query("SELECT `int__gruppen`.`Beschreibung`, `int__dateifreigabe-ordner-gruppen`.`schreiben` FROM `int__dateifreigabe-ordner-gruppen`, `int__gruppen` WHERE `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id` AND `int__dateifreigabe-ordner-gruppen`.`ordner_id` = '$ordnerid' ORDER BY `int__gruppen`.`Beschreibung` ASC");
		$lesen = array();
		$schreiben = array();
		while($gruppe = $query->fetch_array(MYSQLI_ASSOC)) {
			if(!$gruppe["schreiben"]) {
				$lesen[] = $gruppe["Beschreibung"];
			}else{
				$schreiben[] = $gruppe["Beschreibung"];
			}
		}

		$output["lesen"] = $lesen;
		$output["schreiben"] = $schreiben;

		$output = json_encode($output, JSON_UNESCAPED_UNICODE);
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Length: '.strlen($output));
		echo $output;
		exit();
	}else
	if(isset($_GET["dateiEigenschaften"]) && $_GET["dateiEigenschaften"] != "") {
		$ordner = trim(checkfolder($_GET["dateiEigenschaften"]), '/');

		$mainfolder = explode("/", $ordner)[0];
		$ordnerid = $mysqli->query("SELECT `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".mysqli_real_escape_string($mysqli, $mainfolder)."' LIMIT 1");
		if($ordnerid->num_rows != 1) {
			http_response_code(404);
			exit();
		}
		$ordnerid = $ordnerid->fetch_array(MYSQLI_ASSOC);
		$ordnerid = $ordnerid["ordner_id"];

		$path = $ordnerid.substr($ordner, strlen($mainfolder), strlen($ordner));

		$output = array();
		$output["name"] = $ordner;
		$output["groesse"] = formatSizeUnits(filesize($basefolder.$path));
		$output["zeit"] = date("d.m.Y H:i:s", filemtime($basefolder.$path));

		$query = $mysqli->query("SELECT `int__gruppen`.`Beschreibung`, `int__dateifreigabe-ordner-gruppen`.`schreiben` FROM `int__dateifreigabe-ordner-gruppen`, `int__gruppen` WHERE `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id` AND `int__dateifreigabe-ordner-gruppen`.`ordner_id` = '$ordnerid' ORDER BY `int__gruppen`.`Beschreibung` ASC");
		$lesen = array();
		$schreiben = array();
		while($gruppe = $query->fetch_array(MYSQLI_ASSOC)) {
			if(!$gruppe["schreiben"]) {
				$lesen[] = $gruppe["Beschreibung"];
			}else{
				$schreiben[] = $gruppe["Beschreibung"];
			}
		}

		$output["lesen"] = $lesen;
		$output["schreiben"] = $schreiben;

		$output = json_encode($output, JSON_UNESCAPED_UNICODE);
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Length: '.strlen($output));
		echo $output;
		exit();
	}
}
http_response_code(404);
exit();
?>
