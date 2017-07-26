<?php

require("include/php/db.php");

function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_GET['login'])) {
		if(isset($_POST['email']) && $_POST['email'] != "" &&
		isset($_POST['pw']) && $_POST['pw'] != "") {
			$email = mysqli_real_escape_string($mysqli,$_POST['email']);
			$pw = mysqli_real_escape_string($mysqli,$_POST['pw']);

			// get password
			$dbPWquery = $mysqli->query("SELECT `user_id`, `Passwort`, `Vorname`, `Nachname`, `pw_zuruecksetzen` from `int__benutzer` WHERE `Mail` LIKE '$email' LIMIT 1");
			$dbUser = $dbPWquery->fetch_array(MYSQLI_ASSOC);
			$dbPW = $dbUser["Passwort"];
			if($dbPWquery->num_rows == 1 && password_verify($pw, $dbPW)) { // Benutzer gefunden und Passwort richtig
				session_start();
				$_SESSION["userid"] = $dbUser["user_id"];
				$_SESSION["vorname"] = $dbUser["Vorname"];
				$_SESSION["nachname"] = $dbUser["Nachname"];
				if($dbUser["pw_zuruecksetzen"] == 1){
					$mysqli->query("UPDATE `int__benutzer` SET `pw_zuruecksetzen`='0' WHERE `user_id` = '".$dbUser["user_id"]."' LIMIT 1"); //PW zurücksetzen wieder aktivieren
				}
				http_response_code(204);
				exit;
			}else{ // Benutzer nicht gefunden oder Passwort falsch
				http_response_code(401);
				exit;
			}
		}else{
			http_response_code(400); // bad input
			exit;
		}
	}else if(isset($_GET["forgotPassword"])){
		if(isset($_POST["email"]) && $_POST["email"] != "") {
			$mail = mysqli_real_escape_string($mysqli,$_POST['email']);
			$query = $mysqli->query("SELECT `user_id`,`Vorname`,`Nachname`, `pw_zuruecksetzen` FROM `int__benutzer` WHERE `Mail` LIKE '$mail' LIMIT 1");
			if($query->num_rows == 1)
			{
				$query = $query->fetch_array(MYSQLI_ASSOC);
				if($query["pw_zuruecksetzen"] == 0){ //Passwort zurücksetzen erlaubt
					$name = $query["Vorname"]." ".$query["Nachname"];
					$userid = $query["user_id"];

					$pw = generateRandomString(10);
					$passwordHash = password_hash($pw, PASSWORD_DEFAULT);
					$updatePW = $mysqli->query("UPDATE `int__benutzer` SET `Passwort`='$passwordHash' WHERE `user_id`='$userid' LIMIT 1");

					$betreff = "Passwort vergessen";
					$headers   = array();
					$headers[] = 'From: Interner Bereich <no-reply@domain.de>';
					$headers[] = 'Reply-To: webmaster@domain.de';
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
					<p>Hallo '.$name.',<br/><br/>
					dein Passwort für deinen Zugang für den Internen Bereich wurde zurückgesetzt.<br/>
					Dein neues Passwort lautet jetzt: <b>'.$pw.'</b></p>
					<p>Bitte vergiss nicht, dein Passwort auf ein von dir gewähltes Passwort zurückzusetzen. Das kannst du <a href="https://intern.domain.de/einstellungen/account.php?change-password" target="_blank">hier</a> machen.</p>

					<p>Falls du dein Passwort nicht zurücksetzen wolltest, wende dich bitte an einen Administrator, um diese Funktion für deine E-Mail sperren zu lassen.</p>

					<p>Interner Bereich</p>
					<p>Hinweis: Dies ist eine automatisch generierte E-Mail, auf welche nicht geantwortet werden kann.</p>
					</body>
					</html>';
					$header = implode("\r\n",$headers);

					if(mail("\"".$name."\" <".$mail.">", $betreff, $text, $header)){
						$mysqli->query("UPDATE `int__benutzer` SET `pw_zuruecksetzen`='1' WHERE `user_id`='$userid' LIMIT 1"); //Funktion temporär sperren
						http_response_code(204);
					}else{
						http_response_code(500);
					}
					exit;
				}else if($query["pw_zuruecksetzen"] == 1) { //Passwort wurde bereits angefordert
					http_response_code(429); //too many requests
					exit();
				}else if($query["pw_zuruecksetzen"] == 2) { //Passwort zurücksetzen wurde deaktiviert
					http_response_code(401); //nicht authorisiert
					exit();
				}else{ //Unbekannter Fehler
					http_response_code(500); //internal server error
					exit();
				}
			}else{ //Benutzer nicht gefunden
				http_response_code(401);
				exit;
			}
			exit;
		}else{
			http_response_code(400);
		}
	}else
	http_response_code(404);
}

http_response_code(404);
?>
