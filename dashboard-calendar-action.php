<?php
require("include/php/auth.php");
require("include/php/db.php");

if($_SERVER['REQUEST_METHOD'] == "GET"){
	if(isset($_GET["calendar"])) {
		$content = file_get_contents("<Google Calendar Embed URL>");
		$content = str_replace('</title>','</title><base href="https://www.google.com/calendar/" />', $content);
		$content = str_replace('</head>','<link rel="stylesheet" href="https://'.$_SERVER["HTTP_HOST"].'/include/css/dashboard-calendar.css" /></head>', $content);
		echo $content;
	}else
	if(isset($_GET['chat'])) {
		if(isset($_GET["o"]) && $_GET["o"] != "" &&
		isset($_GET["n"]) && $_GET["n"] != "") {

			$anzahl = intval(mysqli_real_escape_string($mysqli, $_GET["n"]));
			if($anzahl > 30 || $anzahl < 1) {
				$anzahl = 30; //default
			}
			$offset = intval(mysqli_real_escape_string($mysqli, $_GET["o"]));
			if ($offset < 0) {
				$offset = ($mysqli->query("SELECT `kommentar_id` FROM `int__dashboard-kommentare` ORDER BY `kommentar_id` DESC LIMIT 1")->fetch_object()->kommentar_id) + 1;
			}

			// Überprüfen, ob Nutzer auch Kommentare löschen darf, die er nicht selbst verfasst hat
			$admin = check_right(4);

			$query = $mysqli->query("SELECT `int__benutzer`.`Vorname`, `int__benutzer`.`Nachname`, `int__dashboard-kommentare`.* FROM `int__benutzer`, `int__dashboard-kommentare` WHERE `int__benutzer`.`user_id` LIKE `int__dashboard-kommentare`.`user_id` AND `int__dashboard-kommentare`.`kommentar_id` <= $offset ORDER BY `int__dashboard-kommentare`.`kommentar_id` DESC LIMIT $anzahl");

			$out = [];
			while($data = $query->fetch_array(MYSQLI_ASSOC)){
				$new = [];
				$new["text"] = $data["Text"];
				$new["name"] = $data["Vorname"]." ".$data["Nachname"];
				$new["user_id"] = $data["user_id"];
				$new["time"] = date("d.m.Y - H:i", strtotime($data["Zeit"]));
				$new["id"] = $data["kommentar_id"];
				$new["deletable"] = ($data["user_id"] == $_SESSION["userid"] || $admin ? true:false);
				$out[] = $new;
			}
			$out["o"] = $offset;
			$out["n"] = $anzahl;

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
}else
if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_GET["sendtoChat"])) {
		if(isset($_POST["text"]) && strlen($_POST["text"])> 0) { //zwei Zeichen mindestens
			if(check_right(1)){ // check Rechte
				$text = mysqli_real_escape_string($mysqli, $_POST["text"]);
				$time = date('Y-m-d H:i:s', time());
				$query = $mysqli->query("INSERT INTO `int__dashboard-kommentare`(`user_id`, `Zeit`, `Text`) VALUES ('".$_SESSION["userid"]."','".$time."','".$text."')");
				$id = $mysqli->insert_id;

				header('Content-Type: application/json; charset=utf-8');
				$out["text"] = str_replace('\n',"\n", $_POST["text"]);
				$out["name"] = $_SESSION["vorname"]." ".$_SESSION["nachname"];
				$out["user_id"] = $_SESSION["userid"];
				$out["time"] = $time;
				$out["id"] = $id;
				$out["deletable"] = true; //weil eigener Kommentar

				echo json_encode($out, JSON_UNESCAPED_UNICODE);
				exit;
			}
		}else{
			http_response_code(400);
			exit;
		}
	}else
	if(isset($_GET["deleteComment"]) && $_GET["deleteComment"] != "" && isset($_POST["id"]) && $_POST["id"] == $_GET["deleteComment"]) {
		if(check_right(4)) {
			$kommentarid = mysqli_real_escape_string($mysqli, $_POST["id"]);
			$query = $mysqli->query("DELETE FROM `int__dashboard-kommentare` WHERE `kommentar_id` = '".$kommentarid."' LIMIT 1");
			if($query) {
				http_response_code(204); // Success with no content
				exit();
			}else{
				http_response_code(500); //Internal Server Error
				exit();
			}
		}else{
			http_response_code(403); //Kein Recht
			exit;
		}
		exit();
	}
}
http_response_code(404);
exit();
?>
