<?php
require("../include/php/auth.php");
require("../include/php/db.php");


if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_GET["s"]) && $_GET["s"] == "account") { // account settings
		if(isset($_GET["changePassword"])) { //Passwort ändern
			if(isset($_POST["oldpw"]) && isset($_POST["newpw"]) && isset($_POST["newpw2"])
			&& $_POST["oldpw"] != "" && $_POST["newpw"] != "" && $_POST["newpw2"] != ""
			&& $_POST["newpw"] == $_POST["newpw2"]) {
				$userid = $_SESSION["userid"];
				$oldpw = $_POST["oldpw"];
				$newpw = $_POST["newpw"];

				$dbPW = $mysqli->query("SELECT `Passwort` from `int__benutzer` WHERE `user_id` LIKE '".$_SESSION["userid"]."' LIMIT 1")->fetch_object()->Passwort;
				if(password_verify($oldpw, $dbPW)) {
					$passwordHash = password_hash($newpw, PASSWORD_DEFAULT);
					$query = $mysqli->query("UPDATE `int__benutzer` SET `Passwort`='$passwordHash' WHERE `user_id`='$userid' LIMIT 1");
					http_response_code(204); //enthält bewusst keine Daten
					exit;
				}else{ //falsches altes Passwort
					http_response_code(401); //auth failed
					exit;
				}
			}else{
				http_response_code(400); //bad input/request
				exit;
			}
		}else
		if(isset($_GET["changePersonalData"])) { //Persönliche Daten
			$sql= "UPDATE `int__benutzer` SET ";
			$update = array();
			if(isset($_POST["vorname"]) && $_POST["vorname"] != ""){
				$update[]="`Vorname`='".mysqli_real_escape_string($mysqli,$_POST["vorname"])."'";
				$_SESSION["vorname"] = mysqli_real_escape_string($mysqli,$_POST["vorname"]);
			}
			if(isset($_POST["nachname"]) && $_POST["nachname"] != ""){
				$update[]="`Nachname`='".mysqli_real_escape_string($mysqli,$_POST["nachname"])."'";
				$_SESSION["nachname"] = mysqli_real_escape_string($mysqli,$_POST["nachname"]);
			}
			if(isset($_POST["email"]) && $_POST["email"] != "")$update[]="`Mail`='".mysqli_real_escape_string($mysqli,$_POST["email"])."'";
			if(isset($_POST["passwort"]) && $_POST["passwort"] != "")$update[]="`Passwort`='".password_hash($_POST["passwort"], PASSWORD_DEFAULT)."'";
			if(isset($_POST["strasse"]))$update[]="`Strasse`='".mysqli_real_escape_string($mysqli,$_POST["strasse"])."'"; //nicht pflicht
			if(isset($_POST["wohnort"]))$update[]="`Wohnort`='".mysqli_real_escape_string($mysqli,$_POST["wohnort"])."'"; //nicht pflicht
			if(isset($_POST["PLZ"]))$update[]="`PLZ`='".mysqli_real_escape_string($mysqli,$_POST["PLZ"])."'"; //nicht pflicht
			if(isset($_POST["telefon"]))$update[]="`Telefon`='".mysqli_real_escape_string($mysqli,$_POST["telefon"])."'"; //nicht pflicht
			if(isset($_POST["mobil"]))$update[]="`Mobil`='".mysqli_real_escape_string($mysqli,$_POST["mobil"])."'"; //nicht pflicht
			$sql .= implode(", ",$update);
			$sql .= " WHERE `user_id` = '".$_SESSION["userid"]."'";

			$mysqli->query($sql);  //UPDATE Userdata
			http_response_code(204);
			exit;
		}else
		if (isset($_GET["changeEMail"])) { //E-Mail
			if(isset($_POST["alteMail"]) && $_POST["alteMail"] != ""
			&& isset($_POST["neueMail"]) && $_POST["neueMail"] != ""
			&& isset($_POST["neueMail2"]) && $_POST["neueMail2"] != ""
			&& $_POST["neueMail"] == $_POST["neueMail2"]) {
				$alteMail = mysqli_real_escape_string($mysqli, $_POST["alteMail"]);
				$neueMail = mysqli_real_escape_string($mysqli, $_POST["neueMail"]);

				$dbMail = $mysqli->query("SELECT `Mail` from `int__benutzer` WHERE `user_id` LIKE '".$_SESSION["userid"]."' LIMIT 1")->fetch_object()->Mail;
				$numMail = $mysqli->query("SELECT `Mail` as 'nummer' from `int__benutzer` WHERE `Mail` LIKE '".$neueMail."' LIMIT 1")->num_rows;
				if($num != 0) { //E-Mail existiert bereits
					http_response_code(409); //Conflict
					exit;
				}
				if($dbMail == $alteMail) { //Erfolg
					$query = $mysqli->query("UPDATE `int__benutzer` SET `Mail`='$neueMail' WHERE `user_id`='".$_SESSION["userid"]."' LIMIT 1");
					http_response_code(204); //enthält bewusst keine Daten
					exit;
				}else{
					http_response_code(401); //auth failed
					exit;
				}
			}else{
				http_response_code(400); //Bad request
				exit;
			}
			http_response_code(500); //Internal Server Error: Something went wrong
			exit;
		}
	}else
	if(isset($_GET["s"]) && $_GET["s"] == "admin") {

		if(isset($_GET["updateUser"]) && $_GET["updateUser"] != "")
		{ //Benutzer bearbeiten
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			$userid = mysqli_real_escape_string($mysqli,$_GET["updateUser"]);
			$sql= "UPDATE `int__benutzer` SET ";
			$update = array();
			if(isset($_POST["vorname"]) && $_POST["vorname"] != "")$update[]="`Vorname`='".mysqli_real_escape_string($mysqli,$_POST["vorname"])."'";
			if(isset($_POST["nachname"]) && $_POST["nachname"] != "")$update[]="`Nachname`='".mysqli_real_escape_string($mysqli,$_POST["nachname"])."'";
			if(isset($_POST["email"]) && $_POST["email"] != "")$update[]="`Mail`='".mysqli_real_escape_string($mysqli,$_POST["email"])."'";
			if(isset($_POST["passwort"]) && $_POST["passwort"] != "")$update[]="`Passwort`='".password_hash($_POST["passwort"], PASSWORD_DEFAULT)."'";
			if(isset($_POST["strasse"]))$update[]="`Strasse`='".mysqli_real_escape_string($mysqli,$_POST["strasse"])."'"; //nicht pflicht
			if(isset($_POST["wohnort"]))$update[]="`Wohnort`='".mysqli_real_escape_string($mysqli,$_POST["wohnort"])."'"; //nicht pflicht
			if(isset($_POST["PLZ"]))$update[]="`PLZ`='".mysqli_real_escape_string($mysqli,$_POST["PLZ"])."'"; //nicht pflicht
			if(isset($_POST["telefon"]))$update[]="`Telefon`='".mysqli_real_escape_string($mysqli,$_POST["telefon"])."'"; //nicht pflicht
			if(isset($_POST["mobil"]))$update[]="`Mobil`='".mysqli_real_escape_string($mysqli,$_POST["mobil"])."'"; //nicht pflicht
			if(isset($_POST["pwzurueckDeaktiv"])){$update[]="`pw_zuruecksetzen`='2'";}else{$update[]="`pw_zuruecksetzen`='0'";}//wenn nicht übertragen wird, ist der Harken nicht gesetzt worden -> nicht deaktivieren
			$sql .= implode(", ",$update);
			$sql .= " WHERE `user_id` = '".$userid."'";

			$mysqli->query($sql);  //UPDATE Userdata

			// Check if groups have been changed
			if(isset($_POST["gruppen"]) && is_array($_POST["gruppen"])){
				$alteGruppenQuery = $mysqli->query("SELECT `gruppen_id` FROM `int__benutzer-gruppen` WHERE `user_id` = '$userid'");
				$alteGruppen = array();
				while ($alteGruppe = $alteGruppenQuery->fetch_array(MYSQLI_NUM)[0]) {
					$alteGruppen[] = $alteGruppe;
				}

				$gruppen=$_POST["gruppen"];
				$tobeAdded = array();
				$tobeDeleted = array();
				foreach($gruppen as $gruppe) {
					if(!in_array($gruppe, $alteGruppen) && $gruppe > 0) { //Gruppe muss der DB hinzugefügt werden, und ist nicht das versteckte Element (sorgt dafür, dass man auch keine Gruppe haben kann)
						$tobeAdded[] = $gruppe;
					}
				}
				foreach($alteGruppen as $alteGruppe) {
					if(!in_array($alteGruppe, $gruppen)) { //Gruppe muss aus der DB gelöscht werden
						$tobeDeleted[] = $alteGruppe;
					}
				}

				if(count($tobeAdded) > 0){
					//Update Groups
					$sql = "INSERT INTO `int__benutzer-gruppen`(`user_id`, `gruppen_id`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$userid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}

					$mysqli->query($sql); //Add Groups to User
				}
				if(count($tobeDeleted) > 0) {
					$sql = "DELETE FROM `int__benutzer-gruppen` WHERE `user_id`='$userid' AND (";
					foreach($tobeDeleted as $key => $row) {
						$sql .= "`gruppen_id` = '$row'";
						if($key < count($tobeDeleted)-1){$sql.=" OR ";}
					}
					$sql .= ")";

					$mysqli->query($sql); //Deletes connection between group and user
				}
			} //Gruppen ende

			// Lehrgänge
			if(isset($_POST["lehrgaenge"]) && is_array($_POST["lehrgaenge"])){
				$alteLehrgaengeQuery = $mysqli->query("SELECT `lehrgang_id` FROM `int__benutzer-lehrgaenge` WHERE `user_id` = '$userid'");
				$alteLehrgaenge = array();
				while ($alterLehrgang = $alteLehrgaengeQuery->fetch_array(MYSQLI_NUM)[0]) {
					$alteLehrgaenge[] = $alterLehrgang;
				}

				$lehrgaenge=$_POST["lehrgaenge"];
				$tobeAdded = array();
				$tobeDeleted = array();
				foreach($lehrgaenge as $lehrgang) {
					if(!in_array($lehrgang, $alteLehrgaenge) && $lehrgang > 0) { //Gruppe muss der DB hinzugefügt werden, und ist nicht das versteckte Element (sorgt dafür, dass man auch keine Gruppe haben kann)
						$tobeAdded[] = $lehrgang;
					}
				}
				foreach($alteLehrgaenge as $alterLehrgang) {
					if(!in_array($alterLehrgang, $lehrgaenge)) { //Gruppe muss aus der DB gelöscht werden
						$tobeDeleted[] = $alterLehrgang;
					}
				}

				if(count($tobeAdded) > 0){
					//Update Groups
					$sql = "INSERT INTO `int__benutzer-lehrgaenge`(`user_id`, `lehrgang_id`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$userid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}

					$mysqli->query($sql); //Add Groups to User
				}
				if(count($tobeDeleted) > 0) {
					$sql = "DELETE FROM `int__benutzer-lehrgaenge` WHERE `user_id`='$userid' AND (";
					foreach($tobeDeleted as $key => $row) {
						$sql .= "`lehrgang_id` = '$row'";
						if($key < count($tobeDeleted)-1){$sql.=" OR ";}
					}
					$sql .= ")";

					$mysqli->query($sql); //Deletes connection between group and user
				}
			} //Lehrgänge Ende

			if(isset($_FILES["bild"]) && ($_FILES["bild"]["type"] == "image/jpg" || $_FILES["bild"]["type"] == "image/jpeg")) { //Falls neues Bild
				if(file_exists("../include/php/userdata/images/".$userid.".jpg")) {
					unlink("../include/php/userdata/images/".$userid.".jpg"); // Alte Datei löschen
					unlink("../include/php/userdata/images/small/".$userid.".png"); // Alte Datei löschen
				}
				move_uploaded_file($_FILES['bild']['tmp_name'], "../include/php/userdata/images/".$userid.".jpg");
			}

			http_response_code(204); //Leere Antwort -> Erfolg
			exit();
		}else
		if(isset($_GET['register'])) {
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			if(isset($_POST['vorname']) && $_POST['vorname'] != "" &&
			isset($_POST['nachname']) && $_POST['nachname'] != "" &&
			isset($_POST['strasse']) &&
			isset($_POST['plz']) &&
			isset($_POST['ort']) &&
			isset($_POST['email']) && $_POST['email'] != ""  &&
			isset($_POST['passwort']) && $_POST['passwort'] != "") {
				if($mysqli->connect_errno){header("LOCATION: register.php?err=0");exit();}
				mysqli_set_charset($mysqli, "utf8");

				$vorname = mysqli_real_escape_string($mysqli, $_POST['vorname']);
				$nachname = mysqli_real_escape_string($mysqli, $_POST['nachname']);
				$strasse = mysqli_real_escape_string($mysqli, $_POST['strasse']);
				$plz = (mysqli_real_escape_string($mysqli, $_POST['plz']));
				$ort = mysqli_real_escape_string($mysqli, $_POST['ort']);
				$pw = $_POST['passwort'];
				$email = (isset($_POST['email']) ? mysqli_real_escape_string($mysqli, $_POST['email']):"");
				$telefon = (isset($_POST['telefon']) ? mysqli_real_escape_string($mysqli, $_POST['telefon']):"");
				$mobil = (isset($_POST['mobil']) ? mysqli_real_escape_string($mysqli, $_POST['mobil']):"");

				$pw = password_hash($pw, PASSWORD_DEFAULT);

				if($mysqli->query("SELECT * FROM `int__benutzer` WHERE (`Vorname` LIKE '$vorname' AND `Nachname` LIKE '$nachname') OR `Mail` LIKE '$email' LIMIT 1")->num_rows == 0){
					$query = $mysqli->query("INSERT INTO `int__benutzer` (`Nachname`, `Vorname`, `Telefon`, `Mobil`, `Mail`, `Strasse`, `Wohnort`, `PLZ`, `Passwort`) VALUES ('$nachname','$vorname','$telefon','$mobil','$email','$strasse','$ort','$plz','$pw')");
					mysqli_close($mysqli);
					if($query){
						header("LOCATION: register.php?success");
					}else{
						header("LOCATION: register.php?err=X");
					}
					exit;
				}else{
					header("LOCATION: register.php?err=1");
				}
			}else
			{
				header("LOCATION: register.php?err=2");
				exit;
			}
		}else
		if(isset($_GET["deleteUser"]) && $_GET["deleteUser"] != "" && isset($_POST["userid"]) && $_POST["userid"] == $_GET["deleteUser"]) {
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			$userid = mysqli_real_escape_string($mysqli,$_POST["userid"]);
			$userdata = $mysqli->query("SELECT `Vorname`, `Nachname`, `Mail` FROM `int__benutzer` WHERE `user_id`='$userid' LIMIT 1");
			if($userdata->num_rows == 1){
				$userdata = $userdata->fetch_array(MYSQLI_ASSOC);
			}else{
				http_response_code(400); //Bad request
				exit;
			}

			// Benutzer aus Gruppen löschen
			$mysqli->query("DELETE FROM `int__benutzer-gruppen` WHERE `user_id` = '$userid'");

			// Benutzer aus Lehrgängen löschen
			$mysqli->query("DELETE FROM `int__benutzer-lehrgaenge` WHERE `user_id` = '$userid'");

			// Benutzer Anwesenheiten löschen
			$mysqli->query("DELETE FROM `int__benutzer-einsatz-anwesenheit` WHERE `user_id` = '$userid'");

			// Kommentare löschen
			$mysqli->query("DELETE FROM `int__dashboard-kommentare` WHERE `user_id` = '$userid'");

			// Benutzerbild löschen
			unlink("../include/php/userdata/images/".$userid.".jpg"); // Alte Datei löschen
			unlink("../include/php/userdata/images/small/".$userid.".png"); // Alte Datei löschen

			// Benutzer löschen: Ganz löschen oder Namen behalten? -> Probleme in zukünftigen Datenbanken (z.B. Einsätze)
			$mysqli->query("DELETE FROM `int__benutzer` WHERE `user_id` = '$userid'");

			// Sende E-Mail an Webmaster
			$betreff = "Benutzer geloescht";
			$headers   = array();
			$headers[] = 'From: Interner Bereich <no-reply@example.de>';
			$headers[] = 'Reply-To: webmaster@example.de';
			$headers[] = 'X-Mailer: PHP/' . phpversion();
			$headers[] = "Mime-Version: 1.0";
			$headers[] = "Content-Type: text/html; charset=utf-8";
			$headers[] = "Content-Transfer-Encoding: quoted-printable";
			$text = '<html>
			<head>
			<title>'.$betreff.'</title>
			<style>body{font-size: 14px;}</style>
			</head>
			<body>
			<p>- Automatisch generierte E-Mail -</p>
			<p>Soeben wurde der Benutzer "'.$userdata["Vorname"].' '.$userdata["Nachname"].'" (ID '.$userid.', E-Mail: '.$userdata["Mail"].') durch '.$_SESSION["vorname"].' '.$_SESSION["nachname"].' (ID '.$_SESSION["userid"].') gelöscht.</p>
			<p>Kommentare, Lehrgänge, Anwesenheiten sowie die Gruppenzuordnungen wurden entfernt und können nicht wiederhergestellt werden.</p>
			<p>Adminkonsole des Internen Bereichs - '.strftime("%D %T", time()).'</p>
			</body>
			</html>';
			$header = implode("\r\n",$headers);
			mail("\"Webmaster\" <webmaster@example.de>", $betreff, $text, $header);

			http_response_code(204);
			exit();
		}else
		if(isset($_GET["updateGroup"]) && $_GET["updateGroup"] != "" && isset($_GET["type"]) && $_GET["type"] = "update") { //Gruppe aktualisieren
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			$groupid = intval(mysqli_real_escape_string($mysqli,$_GET["updateGroup"]));

			// Gruppeninformationen
			$sql= "UPDATE `int__gruppen` SET ";
			$update = array();
			if(isset($_POST["name"]) && $_POST["name"] != "")$update[]="`Beschreibung`='".mysqli_real_escape_string($mysqli,$_POST["name"])."'";
			if(isset($_POST["prioritaet"]))$update[]="`Prioritaet`='".mysqli_real_escape_string($mysqli,$_POST["prioritaet"])."'"; //nicht pflicht
			$sql .= implode(", ",$update);
			$sql .= " WHERE `gruppen_id` = '".$groupid."'";
			$mysqli->query($sql);

			// Check if members have been changed
			if(isset($_POST["mitglieder"]) && is_array($_POST["mitglieder"])){
				$alteMitgliederQuery = $mysqli->query("SELECT `user_id` FROM `int__benutzer-gruppen` WHERE `gruppen_id` = '$groupid'");
				$alteMitglieder = array();
				while ($altesMitglied = $alteMitgliederQuery->fetch_array(MYSQLI_NUM)[0]) { //Mitglieder aus der DB abrufen und speichern
					$alteMitglieder[] = $altesMitglied;
				}

				// Mitglieder vergleichen
				$mitglieder=$_POST["mitglieder"];
				$tobeAdded = array();
				$tobeDeleted = array();
				foreach($mitglieder as $mitglied) {
					if(!in_array($mitglied, $alteMitglieder)) { //Gruppe muss der DB hinzugefügt werden
						$tobeAdded[] = $mitglied;
					}
				}
				foreach($alteMitglieder as $altesMitglied) {
					if(!in_array($altesMitglied, $mitglieder)) { //Gruppe muss aus der DB gelöscht werden
						$tobeDeleted[] = $altesMitglied;
					}
				}
				if(count($tobeAdded) > 0){
					//Update
					$sql = "INSERT INTO `int__benutzer-gruppen`(`gruppen_id`, `user_id`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$groupid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}
					$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
				}
				if(count($tobeDeleted) > 0) {
					$sql = "DELETE FROM `int__benutzer-gruppen` WHERE `gruppen_id`='$groupid' AND (";
					foreach($tobeDeleted as $key => $row) {
						$sql .= "`user_id` = '$row'";
						if($key < count($tobeDeleted)-1){$sql.=" OR ";}
					}
					$sql .= ")";
					$mysqli->query($sql); //Benutzer aus Gruppe löschen
				}
			} // Mitglieder ende

			// Überprüfen, ob Rechte geändert wurden
			if(isset($_POST["rechte"]) && is_array($_POST["rechte"])){
				$alteRechteQuery = $mysqli->query("SELECT `rechtnr` FROM `int__gruppen-rechte` WHERE `gruppen_id` = '$groupid'");
				$alteRechte = array();
				while ($altesRecht = $alteRechteQuery->fetch_array(MYSQLI_NUM)[0]) { //Rechte aus der DB abrufen und speichern
					$alteRechte[] = $altesRecht;
				}

				// Mitglieder vergleichen
				$rechte=$_POST["rechte"];
				$tobeAdded = array();
				$tobeDeleted = array();
				foreach($rechte as $recht) {
					if(!in_array($recht, $alteRechte) && $recht > 0) { //Recht muss der Gruppe in der DB hinzugefügt werden
						$tobeAdded[] = $recht;
					}
				}
				foreach($alteRechte as $altesRecht) {
					if(!in_array($altesRecht, $rechte)) { //Recht muss der Gruppe in der DB entzogen werden
						$tobeDeleted[] = $altesRecht;
					}
				}
				if(count($tobeAdded) > 0){
					//Update
					$sql = "INSERT INTO `int__gruppen-rechte`(`gruppen_id`, `rechtnr`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$groupid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}
					$mysqli->query($sql); // Recht der Gruppe hinzufügen
				}
				if(count($tobeDeleted) > 0) {
					$sql = "DELETE FROM `int__gruppen-rechte` WHERE `gruppen_id`='$groupid' AND (";
					foreach($tobeDeleted as $key => $row) {
						$sql .= "`rechtnr` = '$row'";
						if($key < count($tobeDeleted)-1){$sql.=" OR ";}
					}
					$sql .= ")";
					$mysqli->query($sql); // Recht der Gruppe entfernen;
				}
			} // Rechte ende

			http_response_code(204);
			exit();
		}else
		if(isset($_GET["updateGroup"]) && $_GET["updateGroup"] == "" && isset($_GET["type"]) && $_GET["type"] = "new") { //Neue Gruppe
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			$sql= "INSERT INTO `int__gruppen`(`Beschreibung`, `Prioritaet`) VALUES (";
			if (isset($_POST["name"])){$sql .= "'".mysqli_real_escape_string($mysqli,$_POST["name"])."',";}else{$sql .= "'Unbenannt',";}
			if(isset($_POST["prioritaet"])){$sql .= "'".intval(mysqli_real_escape_string($mysqli,$_POST["prioritaet"]))."'";}else{$sql .= "'0'";}
			$sql .= ")";
			$mysqli->query($sql); //Neue Gruppe eintragen
			$groupid = $mysqli->insert_id;

			if(isset($_POST["mitglieder"]) && is_array($_POST["mitglieder"])){
				$mitglieder = (is_array($_POST["mitglieder"]) ? $_POST["mitglieder"] : array());
				if(count($mitglieder) > 0){
					//Update
					$sql = "INSERT INTO `int__benutzer-gruppen`(`gruppen_id`, `user_id`) VALUES ";
					foreach($mitglieder as $key => $row) {
						$sql.="('".$groupid."', '".$row."')";
						if($key < count($mitglieder)-1){$sql.=",";}
					}
					$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
				}
			}

			if(isset($_POST["rechte"]) && is_array($_POST["rechte"])){
				$rechte = (is_array($_POST["rechte"]) ? $_POST["rechte"] : array());
				if(count($rechte) > 0){
					//Update
					$sql = "INSERT INTO `int__gruppen-rechte`(`gruppen_id`, `rechtnr`) VALUES ";
					foreach($rechte as $key => $row) {
						if($row > 0){
							$sql.="('".$groupid."', '".$row."')";
							if($key < count($rechte)-1){$sql.=",";}
						}
					}
					$mysqli->query($sql); // Recht der Gruppe hinzufügen
				}
			}

			header("Content-Type: application/json; charset=utf-8");
			$out = array();
			$out["gruppenid"] = $groupid;
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			exit();
		}else
		if(isset($_GET["deleteGroup"]) && $_GET["deleteGroup"] != "" && isset($_POST["groupid"]) && $_POST["groupid"] == $_GET["deleteGroup"]) {
			if(!check_right(2)) {
				http_response_code(401);
				exit();
			}
			$groupid = mysqli_real_escape_string($mysqli,$_POST["groupid"]);
			$groupcheck = $mysqli->query("SELECT `gruppen_id` FROM `int__gruppen` WHERE `gruppen_id`='$groupid' LIMIT 1");
			if($groupcheck->num_rows != 1){ //Gruppe existiert nicht
				http_response_code(400); //Bad request
				exit();
			}

			// Rechte von Gruppen löschen
			$mysqli->query("DELETE FROM `int__gruppen-rechte` WHERE `gruppen_id` = '$groupid'");

			// Mitglieder von Gruppen löschen
			$mysqli->query("DELETE FROM `int__benutzer-gruppen` WHERE `gruppen_id` = '$groupid'");

			// Gruppe löschen
			$mysqli->query("DELETE FROM `int__gruppen` WHERE `gruppen_id` = '$groupid' LIMIT 1");

			http_response_code(204);
			exit();
		}else
		if(isset($_GET["updateCourse"]) && $_GET["updateCourse"] != "" && isset($_GET["type"]) && $_GET["type"] == "update") { // update Lehrgang
			if(!check_right(9)) {
				http_response_code(401);
				exit();
			}
			$lehrgangid = intval(mysqli_real_escape_string($mysqli,$_GET["updateCourse"]));

			// Gruppeninformationen
			$sql= "UPDATE `int__lehrgaenge` SET ";
			$update = array();
			if(isset($_POST["name"]) && $_POST["name"] != "")$update[]="`name`='".mysqli_real_escape_string($mysqli,$_POST["name"])."'";
			if(isset($_POST["abkuerzung"]))$update[]="`abkuerzung`='".mysqli_real_escape_string($mysqli,$_POST["abkuerzung"])."'"; //nicht pflicht
			if(isset($_POST["icon"]))$update[]="`icon`='".mysqli_real_escape_string($mysqli,$_POST["icon"])."'"; //nicht pflicht
			if(isset($_POST["reihenfolge"]))$update[]="`reihenfolge`='".mysqli_real_escape_string($mysqli,$_POST["reihenfolge"])."'"; //nicht pflicht
			$sql .= implode(", ",$update);
			$sql .= " WHERE `lehrgang_id` = '".$lehrgangid."'";
			$mysqli->query($sql);

			// Check if members have been changed
			if(isset($_POST["mitglieder"]) && is_array($_POST["mitglieder"])){
				$alteMitgliederQuery = $mysqli->query("SELECT `user_id` FROM `int__benutzer-lehrgaenge` WHERE `lehrgang_id` = '$lehrgangid'");
				$alteMitglieder = array();
				while ($altesMitglied = $alteMitgliederQuery->fetch_array(MYSQLI_NUM)[0]) { //Mitglieder aus der DB abrufen und speichern
					$alteMitglieder[] = $altesMitglied;
				}
				// Mitglieder vergleichen
				$mitglieder=$_POST["mitglieder"];
				$tobeAdded = array();
				$tobeDeleted = array();
				foreach($mitglieder as $mitglied) {
					if(!in_array($mitglied, $alteMitglieder) && $mitglied >= 0) { //Gruppe muss der DB hinzugefügt werden, wenn es nicht das Prüfelement ist
						$tobeAdded[] = $mitglied;
					}
				}
				foreach($alteMitglieder as $altesMitglied) {
					if(!in_array($altesMitglied, $mitglieder)) { //Gruppe muss aus der DB gelöscht werden
						$tobeDeleted[] = $altesMitglied;
					}
				}
				if(count($tobeAdded) > 0){
					//Update
					$sql = "INSERT INTO `int__benutzer-lehrgaenge`(`lehrgang_id`, `user_id`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$lehrgangid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}
					$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
				}
				if(count($tobeDeleted) > 0) {
					$sql = "DELETE FROM `int__benutzer-lehrgaenge` WHERE `lehrgang_id`='$lehrgangid' AND (";
					foreach($tobeDeleted as $key => $row) {
						$sql .= "`user_id` = '$row'";
						if($key < count($tobeDeleted)-1){$sql.=" OR ";}
					}
					$sql .= ")";
					$mysqli->query($sql); //Benutzer aus Gruppe löschen
				}
			} // Mitglieder ende
			http_response_code(204);
			exit();
		}else
		if(isset($_GET["updateCourse"]) && $_GET["updateCourse"] == "-1" && isset($_GET["type"]) && $_GET["type"] == "new") {
			if(!check_right(9)) {
				http_response_code(401);
				exit();
			}
			$sql= "INSERT INTO `int__lehrgaenge`(`name`, `abkuerzung`, `icon`, `reihenfolge`) VALUES (";
			$update = array();
			if(isset($_POST["name"])){$update[]="'".mysqli_real_escape_string($mysqli, $_POST["name"])."'";}else{$update[]="''";}
			if(isset($_POST["abkuerzung"])){$update[]="'".mysqli_real_escape_string($mysqli, $_POST["abkuerzung"])."'";}else{$update[]="''";}
			if(isset($_POST["icon"])){$update[]="'".mysqli_real_escape_string($mysqli, $_POST["icon"])."'";}else{$update[]="''";}
			if(isset($_POST["reihenfolge"])){$update[]="'".intval($_POST["reihenfolge"])."'";}else{$update[]="''";}
			$sql .= implode(", ",$update);
			$sql .= ")";
			$mysqli->query($sql);

			$lehrgangid = $mysqli->insert_id;
			if($lehrgangid <= 0){http_response_code(500);exit();}

			// Mitglieder
			if(isset($_POST["mitglieder"]) && is_array($_POST["mitglieder"])) {
				// Mitglieder vergleichen
				$mitglieder=$_POST["mitglieder"];
				$tobeAdded = array();
				foreach($mitglieder as $mitglied) {
					if($mitglied >= 0) { //Mitglied muss dem Einsatz hinzugefügt werden, wenn dies nicht das Prüfelement ist
						$tobeAdded[] = $mitglied;
					}
				}
				// Daten ändern
				if(count($tobeAdded) > 0){
					//Update
					$sql = "INSERT INTO `int__benutzer-lehrgaenge`(`lehrgang_id`, `user_id`) VALUES ";
					foreach($tobeAdded as $key => $row) {
						$sql.="('".$lehrgangid."', '".$row."')";
						if($key < count($tobeAdded)-1){$sql.=",";}
					}
					$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
				}
			}

			header("Content-Type: application/json; charset=utf-8");
			echo "{\"id\":\"".$lehrgangid."\"}";
			exit();
		}else
		if(isset($_GET["deleteCourse"]) && $_GET["deleteCourse"] != "" && isset($_POST["id"]) && $_POST["id"] == $_GET["deleteCourse"]) {
			if(!check_right(9)) {
				http_response_code(401);
				exit();
			}
			$lehrgang = mysqli_real_escape_string($mysqli,$_POST["id"]);
			$check = $mysqli->query("SELECT `lehrgang_id` FROM `int__lehrgaenge` WHERE `lehrgang_id`='$lehrgang' LIMIT 1");
			if($check->num_rows != 1){ //Gruppe existiert nicht
				http_response_code(400); //Bad request
				exit();
			}


			// Mitglieder von Gruppen löschen
			$mysqli->query("DELETE FROM `int__benutzer-lehrgaenge` WHERE `lehrgang_id` = '$lehrgang'");

			// Gruppe löschen
			$mysqli->query("DELETE FROM `int__lehrgaenge` WHERE `lehrgang_id` = '$lehrgang' LIMIT 1");

			http_response_code(204);
			exit();
		}
	}else
	if(isset($_GET["s"]) && $_GET["s"] == "website") {
		// Verschiedene Rechte: Einzeln Überprüfen!
		if(isset($_GET["updateOperation"]) && $_GET["updateOperation"] != "") {
			if(!check_right(3)){http_response_code(401);exit();} //darf Einsätze bearbeiten
			if(isset($_GET["type"]) && $_GET["type"] == "update")
			{ //Update
				if(isset($_POST["id"]) && $_GET["updateOperation"] == $_POST["id"]) {
					$einsatzid = $_POST["id"];
					//Update simple information
					$sql= "UPDATE `einsaetze` SET ";
					$update = array();
					if(isset($_POST["titel"]) && $_POST["titel"] != "")$update[]="`title`='".mysqli_real_escape_string($mysqli,$_POST["titel"])."'";
					if(isset($_POST["art"]))$update[]="`einsatzArt`='".mysqli_real_escape_string($mysqli,$_POST["art"])."'"; //nicht pflicht
					if(isset($_POST["ort"]))$update[]="`einsatzOrt`='".mysqli_real_escape_string($mysqli,$_POST["ort"])."'";
					if(isset($_POST["num"]) && $_POST["num"] != ""){
						$update[]="`num`='".intval($_POST["num"])."'";
						// Bilder umbenennen
						$daten = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', `num`, `images` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1")->fetch_array(MYSQLI_ASSOC);
						$jahr = $daten["jahr"];
						$jahrkurz = substr($jahr, 2);
						$num = $daten["num"];
						$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/"; //Bilder
						if($num != intval($_POST["num"])){
							for($i = 1; $i <= $daten["images"]; $i++) {
								if(file_exists($path."e".$num."_".$jahrkurz."_".$i.".jpg"))
								rename($path."e".$num."_".$jahrkurz."_".$i.".jpg", $path."e".intval($_POST["num"])."_".$jahrkurz."_".$i.".jpg");
							}
						}
					}
					if(isset($_POST["assNum"]))$update[]="`assNum`='".mysqli_real_escape_string($mysqli,$_POST["assNum"])."'";
					if(isset($_POST["beginn"])){
						$zeit = new DateTime($_POST["beginn"]);
						$zeit = $zeit->format('Y-m-d H:i:s');
						$update[]="`startTime`='".$zeit."'";
					}
					if(isset($_POST["ende"])){
						$zeit = new DateTime($_POST["ende"]);
						$zeit = $zeit->format('Y-m-d H:i:s');
						$update[]="`endTime`='".$zeit."'";
					}
					if(isset($_POST["text"]))$update[]="`text`='".mysqli_real_escape_string($mysqli,$_POST["text"])."'";
					if(isset($_POST["lightboxText"]))$update[]="`lightboxText`='".mysqli_real_escape_string($mysqli,$_POST["lightboxText"])."'";
					if(isset($_POST["fahrzeuge"]) && is_array($_POST["fahrzeuge"])) {
						if($_POST["fahrzeuge"][0] != -1){ //verstecktes Element nicht gefunden
							http_response_code(400);
							echo "Fehler in der Fahrzeugübermittlung";
							exit();
						}
						$update[]="`fahrzeuge`='".mysqli_real_escape_string($mysqli, implode(", ",array_splice($_POST["fahrzeuge"], 1)))."'"; //verstecktes Element rauskürzen und mit Komma Elemente trennen
					}
					$sql .= implode(", ",$update);
					$sql .= " WHERE `id` = '".$einsatzid."'";
					$mysqli->query($sql);

					//Anwesende
					if(isset($_POST["anwesende"]) && is_array($_POST["anwesende"])) {
						$altQuery = $mysqli->query("SELECT `user_id` FROM `int__benutzer-einsatz-anwesenheit` WHERE `einsatz_id` LIKE '$einsatzid'");
						$alt = array();
						while ($altAnwesender = $altQuery->fetch_array(MYSQLI_NUM)[0]) { //Mitglieder aus der DB abrufen und speichern
							$alt[] = $altAnwesender;
						}

						// Mitglieder vergleichen
						$anwesende=$_POST["anwesende"];
						$tobeAdded = array();
						$tobeDeleted = array();
						foreach($anwesende as $anwesender) {
							if($anwesender >= 0 && !in_array($anwesender, $alt)) { //Mitglied muss dem Einsatz hinzugefügt werden, wenn dies nicht das Prüfelement ist
								$tobeAdded[] = $anwesender;
							}
						}
						foreach($alt as $altAnwesender) {
							if(!in_array($altAnwesender, $anwesende)) { //Mitlied muss vom Einsatz gelöscht werden
								$tobeDeleted[] = $altAnwesender;
							}
						}

						// Daten ändern
						if(count($tobeAdded) > 0){
							//Update
							$sql = "INSERT INTO `int__benutzer-einsatz-anwesenheit`(`einsatz_id`, `user_id`) VALUES ";
							foreach($tobeAdded as $key => $row) {
								$sql.="('".$einsatzid."', '".$row."')";
								if($key < count($tobeAdded)-1){$sql.=",";}
							}
							$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
						}
						if(count($tobeDeleted) > 0) {
							$sql = "DELETE FROM `int__benutzer-einsatz-anwesenheit` WHERE `einsatz_id`='$einsatzid' AND (";
							foreach($tobeDeleted as $key => $row) {
								$sql .= "`user_id` = '$row'";
								if($key < count($tobeDeleted)-1){$sql.=" OR ";}
							}
							$sql .= ")";
							$mysqli->query($sql); //Benutzer aus Gruppe löschen
						}
					}

					// alte Bilder
					if(isset($_POST["existierendeBilder"]) && is_array($_POST["existierendeBilder"])) {
						$anzAlteBilder = $mysqli->query("SELECT `images` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1")->fetch_object()->images; //Anzahl an alten Bildern
						$alteBilder = array();
						for($i = 1; $i <= $anzAlteBilder; $i++){
							$alteBilder[$i-1] = $i;
						}

						$bilder = $_POST["existierendeBilder"];
						$neueBilder = array();

						$query = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', `num` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1");
						$daten = $query->fetch_array(MYSQLI_ASSOC);
						$jahr = $daten["jahr"];
						$jahrkurz = substr($jahr, 2);
						$num = $daten["num"];
						$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/"; //Bilder

						// Bilder vergleichen
						foreach ($alteBilder as $key=>$bild) {
							if(!in_array($bild, $bilder)) { // Bild muss gelöscht werden
								//echo "$bild löschen\n";
								if(file_exists($path."e".$num."_".$jahrkurz."_".$bild.".jpg")) {
									unlink($path."e".$num."_".$jahrkurz."_".$bild.".jpg");
								}
							}else{
								if($bild != count($neueBilder)+1){
									//echo "$bild -> ".(count($neueBilder)+1)."\n";
									if(file_exists($path."e".$num."_".$jahrkurz."_".$bild.".jpg")) {
										rename($path."e".$num."_".$jahrkurz."_".$bild.".jpg", $path."e".$num."_".$jahrkurz."_".(count($neueBilder)+1).".jpg");
									}
								}
								$neueBilder[] = $bild;
							}
						}

						$anzNeueBilder = count($neueBilder);
						if($anzNeueBilder != $anzAlteBilder) {
							//Datenbank ändern
							$mysqli->query("UPDATE `einsaetze` SET `images`='$anzNeueBilder' WHERE `id` = '$einsatzid' LIMIT 1");
						}
						$anzBilder = $anzNeueBilder;
					}

					//Bilder
					if(isset($_FILES["bilder"]) && count($_FILES["bilder"]["name"]) > 0) {
						$anz = count($_FILES["bilder"]["name"]);
						$einsatzData = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', `num`, `images` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1")->fetch_array(MYSQLI_ASSOC);
						$bestBilder = $einsatzData["images"];
						$jahr = $einsatzData["jahr"];
						$jahrkurz = substr($jahr, 2);
						$num = $einsatzData["num"];
						$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/"; //Bilder

						$nr = $bestBilder;
						foreach($_FILES["bilder"]["name"] as $i => $bild) {
							if($_FILES["bilder"]["tmp_name"][$i]) { // muss existieren
								$nr++;
								if(file_exists($path."e".$num."_".$jahrkurz."_".$nr.".jpg")) {
									http_response_code(500);
									echo "Bild e".$num."_".$jahrkurz."_".$nr.".jpg existiert bereits";
									exit();
								}
								if (!move_uploaded_file($_FILES['bilder']['tmp_name'][$i], $path."e".$num."_".$jahrkurz."_".$nr.".jpg")) {
									http_response_code(500);
									echo "Bild '".$_FILES["bilder"]["name"][$i]."' konnte nicht als 'e".$num."_".$jahrkurz."_".$nr.".jpg' gespeichert werden. \n";
								}
							}
						}

						if($nr != $bestBilder) { //wenn Bilder dazu kamen
							$mysqli->query("UPDATE `einsaetze` SET `images`='$nr' WHERE `id` = '$einsatzid' LIMIT 1"); //Datenbank updaten
						}
					}
					http_response_code(204); //no content
					exit();
				}else{ //POST und GET id stimmen nicht überein -> fehler
					http_response_code(400); //Bad request
					exit();
				}
			}//ende update
			else if(isset($_GET["type"]) && $_GET["type"] == "new") {
				//Update simple information
				$sql= "INSERT INTO `einsaetze`(`num`, `assNum`, `year`, `title`, `startTime`, `endTime`, `einsatzArt`, `einsatzOrt`, `text`, `lightboxText`, `fahrzeuge`) VALUES (";
				$update = array();
				if(isset($_POST["num"])){$update[]="'".intval($_POST["num"])."'";}else{$update[]="''";}
				if(isset($_POST["assNum"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["assNum"])."'";}else{$update[]="''";}
				if(isset($_POST["beginn"]) && $_POST["beginn"] != ""){
					$zeit = new DateTime($_POST["beginn"]);
					$zeit = $zeit->format('Y');
					$update[]="'$zeit'";
				}else{$update[]="''";}
				if(isset($_POST["titel"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["titel"])."'";}else{$update[]="''";}
				if(isset($_POST["beginn"]) && $_POST["beginn"] != ""){
					$zeit = new DateTime($_POST["beginn"]);
					$zeit = $zeit->format('Y-m-d H:i:s');
					$update[]="'".$zeit."'";
				}else{$update[]="''";}
				if(isset($_POST["ende"]) && $_POST["ende"] != ""){
					$zeit = new DateTime($_POST["ende"]);
					$zeit = $zeit->format('Y-m-d H:i:s');
					$update[]="'".$zeit."'";
				}else{$update[]="''";}
				if(isset($_POST["art"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["art"])."'";}else{$update[]="''";} //nicht pflicht
				if(isset($_POST["ort"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["ort"])."'";}else{$update[]="''";}
				if(isset($_POST["text"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["text"])."'";}else{$update[]="''";}
				if(isset($_POST["lightboxText"])){$update[]="'".mysqli_real_escape_string($mysqli,$_POST["lightboxText"])."'";}else{$update[]="''";}
				if(isset($_POST["fahrzeuge"]) && is_array($_POST["fahrzeuge"])) {
					if($_POST["fahrzeuge"][0] != -1){ //verstecktes Element nicht gefunden
						http_response_code(400);
						echo "Fehler in der Fahrzeugübermittlung";
						exit();
					}
					$update[]="'".mysqli_real_escape_string($mysqli, implode(", ",array_splice($_POST["fahrzeuge"], 1)))."'"; //verstecktes Element rauskürzen und mit Komma Elemente trennen
				}else{$update[]="''";}
				$sql .= implode(", ",$update);
				$sql .= ")";
				$mysqli->query($sql);

				$einsatzid = $mysqli->insert_id;



				//Anwesende
				if(isset($_POST["anwesende"]) && is_array($_POST["anwesende"])) {
					// Mitglieder vergleichen
					$anwesende=$_POST["anwesende"];
					$tobeAdded = array();
					foreach($anwesende as $anwesender) {
						if($anwesender >= 0) { //Mitglied muss dem Einsatz hinzugefügt werden, wenn dies nicht das Prüfelement ist
							$tobeAdded[] = $anwesender;
						}
					}

					// Daten ändern
					if(count($tobeAdded) > 0){
						//Update
						$sql = "INSERT INTO `int__benutzer-einsatz-anwesenheit`(`einsatz_id`, `user_id`) VALUES ";
						foreach($tobeAdded as $key => $row) {
							$sql.="('".$einsatzid."', '".$row."')";
							if($key < count($tobeAdded)-1){$sql.=",";}
						}
						$mysqli->query($sql); // Benutzer zu Gruppen hinzufügen
					}
				}

				// existierende Bilder übersprungen

				//Bilder
				if(isset($_FILES["bilder"]) && count($_FILES["bilder"]["name"]) > 0) {
					$anz = count($_FILES["bilder"]["name"]);
					$einsatzData = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', `num`, `images` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1")->fetch_array(MYSQLI_ASSOC);
					$bestBilder = 0;
					$jahr = $einsatzData["jahr"];
					$jahrkurz = substr($jahr, 2);
					$num = $einsatzData["num"];
					$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/"; //Bilder

					if($jahr != "" && $num != ""){ //falls jahr und nummer nicht eingetragen wurden
						$nr = $bestBilder;
						foreach($_FILES["bilder"]["name"] as $i => $bild) {
							if($_FILES["bilder"]["tmp_name"][$i]) { // muss existieren
								$nr++;
								if(file_exists($path."e".$num."_".$jahrkurz."_".$nr.".jpg")) {
									http_response_code(500);
									echo "Bild e".$num."_".$jahrkurz."_".$nr.".jpg existiert bereits";
									exit();
								}
								if (!move_uploaded_file($_FILES['bilder']['tmp_name'][$i], $path."e".$num."_".$jahrkurz."_".$nr.".jpg")) {
									http_response_code(500);
									echo "Bild '".$_FILES["bilder"]["name"][$i]."' konnte nicht als 'e".$num."_".$jahrkurz."_".$nr.".jpg' gespeichert werden. \n";
								}
							}
						}
					}else{
						http_response_code(400); //bad request
						exit();
					}

					if($nr != $bestBilder) { //wenn Bilder dazu kamen
						$mysqli->query("UPDATE `einsaetze` SET `images`='$nr' WHERE `id` = '$einsatzid' LIMIT 1"); //Datenbank updaten
					}
				}

				header("Content-Type: application/json; charset=utf-8");
				echo "{\"id\":\"".$einsatzid."\"}";
				exit();
			} // ende neuer Einsatz
			else if(isset($_GET["type"]) && $_GET["type"] == "delete" && isset($_POST["id"]) && $_POST["id"] == $_GET["updateOperation"]) {
				$einsatz = mysqli_real_escape_string($mysqli,$_POST["id"]);
				$einsatzdata = $mysqli->query("SELECT `num`, YEAR(`startTime`) as 'jahr', `images` FROM `einsaetze` WHERE `id`='$einsatz' LIMIT 1");
				if($userdata->num_rows == 1){
					$einsatzdata = $einsatzdata->fetch_array(MYSQLI_ASSOC);
				}else{
					http_response_code(400); //Bad request
					exit;
				}

				// Benutzer aus Gruppen löschen
				$mysqli->query("DELETE FROM `int__benutzer-einsatz-anwesenheit` WHERE `einsatz_id` = '$einsatz'");

				// Bilder löschen
				$jahr = $einsatzdata["jahr"];
				$jahrkurz = substr($jahr, 2);
				$num = $einsatzdata["num"];
				$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/"; //Bilder
				for ($i = 1; $i <= $einsatzdata["images"]; $i++){
					unlink($path."e".$num."_".$jahrkurz."_".$nr.".jpg");
				}

				$mysqli->query("DELETE FROM `einsaetze` WHERE `id` = '$einsatz'");

				http_response_code(204);
				exit();
			}
		}else
		if(isset($_GET["updateNews"]) && $_GET["updateNews"] != "") {
			if(!check_right(7)) {http_response_code(403);exit();} //Darf News und Termine bearbeiten



			exit();
		}
	}
}
http_response_code(404);
exit;
?>
