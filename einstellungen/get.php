<?php
require("../include/php/auth.php"); require("../include/php/db.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){
	if(isset($_GET["getGroups"])) {
		if(!check_right(2))
		{ //Admins only: Recht NR 2!
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		if(!isset($_GET["orderBy"])){
			$order = "`int__gruppen`.`Beschreibung` ASC";
		}else{
			if($_GET["orderBy"] == "members"){ $order = "`Mitglieder`";}
			else if($_GET["orderBy"] == "priority") {$order = "`int__gruppen`.`Prioritaet`";}
			else if($_GET["orderBy"] == "name") {$order = "`int__gruppen`.`Beschreibung`";}
			else {$order = "`int__gruppen`.`Beschreibung`";}

			if(!isset($_GET["orderDirection"])) {
				$order .= " DESC";
			}else{
				if($_GET["orderDirection"] == "DESC") {$order .= " DESC";}
				else if($_GET["orderDirection"] == "ASC") {$order .= " ASC";}
				else {$order .= " ASC";}
			}
		}
		$gruppen = $mysqli->query("SELECT `int__gruppen`.`gruppen_id`, `int__gruppen`.`Beschreibung`, `int__gruppen`.`Prioritaet`, (SELECT COUNT(`int__benutzer-gruppen`.`user_id`) FROM `int__benutzer-gruppen` WHERE `int__gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` GROUP BY `int__benutzer-gruppen`.`gruppen_id`) as `Mitglieder` FROM `int__gruppen` ORDER BY $order");
		$output = array();
		while($gruppe = $gruppen->fetch_array(MYSQLI_ASSOC)) {
			$output[] = $gruppe;
		}

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getGroupData"]) && isset($_GET["group"]) && $_GET["group"] != "") {
		if(!check_right(2))
		{ //Admins only: Recht NR 2!
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		$group = mysqli_real_escape_string($mysqli, $_GET["group"]);

		$output = array();
		$groupdata = $mysqli->query("SELECT `int__gruppen`.`gruppen_id`, `int__gruppen`.`Beschreibung`, `int__gruppen`.`Prioritaet`, `int__gruppen-rechte`.`rechtnr` FROM `int__gruppen` LEFT JOIN `int__gruppen-rechte` ON `int__gruppen-rechte`.`gruppen_id` = `int__gruppen`.`gruppen_id` WHERE `int__gruppen`.`gruppen_id` = '".$group."'");
		while($fGroupData = $groupdata->fetch_array(MYSQLI_ASSOC)) {
			if(!isset($output["gruppen_id"])) {
				$output["gruppen_id"] = $fGroupData["gruppen_id"];
				$output["name"] = $fGroupData["Beschreibung"];
				$output["prioritaet"] = $fGroupData["Prioritaet"];
				$output["rechte"] = array();
				$output["mitglieder"] = array();
			}
			if($fGroupData["rechtnr"] != NULL)$output["rechte"][] = $fGroupData["rechtnr"];
		}

		$mitglieder = $mysqli->query("SELECT `int__benutzer-gruppen`.`user_id` FROM `int__benutzer-gruppen` WHERE `int__benutzer-gruppen`.`gruppen_id` = '".$group."'");

		while ($mitglied = $mitglieder->fetch_array(MYSQLI_ASSOC)) {
			if($mitglied["user_id"] != NULL)$output["mitglieder"][] = $mitglied["user_id"];
		}

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getOperations"])) {
		if(!check_right(3))
		{ //Webmaster only: Recht NR 3!
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}

		$out = array();
		$outeinsaetze = array();
		if(isset($_GET["o"]) && $_GET["o"] > 0){$off = "WHERE `startTime` < '".mysqli_real_escape_string($mysqli, $_GET["o"])."'";}
		$query = $mysqli->query("SELECT `id`, `num`, `assNum`, `title`, DATE(`startTime`) as 'datum', TIMESTAMPDIFF(MINUTE,`startTime`, `endTime`) as 'dauer', `startTime` as 'time' FROM `einsaetze` $off ORDER BY `startTime` DESC, `num` DESC LIMIT 20");
		if($query->num_rows == 0 && isset($_GET["o"]) && $_GET["o"] > 0) {
			$out["offset"] = $_GET["o"];
		}
		while($einsatz = $query->fetch_array(MYSQLI_ASSOC)) {
			if(!isset($out["offset"]) || strcmp($out["offset"], $einsatz["time"]) > 0){$out["offset"] = $einsatz["time"];}
			$tmp = array();
			$tmp["id"] = $einsatz["id"];
			$tmp["num"] = $einsatz["num"];
			$tmp["assNum"] = $einsatz["assNum"];
			$tmp["title"] = $einsatz["title"];
			$tmp["date"] = $einsatz["datum"];
			$tmp["dauer"] = $einsatz["dauer"];
			$outeinsaetze[] = $tmp;
		}
		$out["einsaetze"] = $outeinsaetze;

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getOperationData"])) {
		if(!check_right(3))
		{ //Webmaster only
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		if(isset($_GET["operation"]) && $_GET["operation"] != "") {
			$einsatzid = mysqli_real_escape_string($mysqli, $_GET["operation"]);
			$output = array();
			$query = "SELECT `einsaetze`.`id`, `einsaetze`.`num`, `einsaetze`.`assNum`, `einsaetze`.`title` as 'titel', `einsaetze`.`startTime` as 'beginn', `einsaetze`.`endTime` as 'ende', `einsaetze`.`einsatzArt` as 'art', `einsaetze`.`einsatzOrt` as 'ort', `einsaetze`.`text`, `einsaetze`.`images` as 'bilder', `einsaetze`.`lightboxText`, `einsaetze`.`fahrzeuge` FROM `einsaetze` WHERE `einsaetze`.`id` = '$einsatzid'";
			$query = $mysqli->query($query);
			$result = $query->fetch_array(MYSQLI_ASSOC);
			$output["id"] = $result["id"];
			$output["num"] = $result["num"];
			$output["assNum"] = $result["assNum"];
			$output["titel"] = $result["titel"];
			$output["beginn"] = date("Y-m-d\TH:i", strtotime($result["beginn"]));
			$output["startTime"] = $result["beginn"]; //unformatted
			$output["ende"] = date("Y-m-d\TH:i", strtotime($result["ende"]));
			$output["endTime"] = $result["ende"]; //unformatted
			$output["art"] = $result["art"];
			$output["ort"] = $result["ort"];
			$output["text"] = $result["text"];
			$output["lightboxText"] = $result["lightboxText"];
			$output["bilder"] = intval($result["bilder"]);
			$output["fahrzeuge"] = explode(", ", $result["fahrzeuge"]);

			$query = "SELECT `user_id` FROM `int__benutzer-einsatz-anwesenheit` WHERE `einsatz_id` LIKE '$einsatzid'"; //Anwesende abrufen
			$query = $mysqli->query($query);
			$tmp = array();
			while($result = $query->fetch_array(MYSQLI_ASSOC)) {
				$tmp[] = $result["user_id"];
			}
			$output["anwesende"] = $tmp;

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($output, JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			http_response_code(406); //falsche Parameter
			exit();
		}
	}else
	if(isset($_GET["getOperationImage"]) && $_GET["getOperationImage"] != "") {
		if(!check_right(3))
		{//Webmaster only
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		session_write_close();
		if(isset($_GET["nr"]) && $_GET["nr"] != "") {
			$einsatzid = mysqli_real_escape_string($mysqli, $_GET["getOperationImage"]);
			$nr = intval($_GET["nr"]);

			$query = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', `num` FROM `einsaetze` WHERE `id` = '$einsatzid' LIMIT 1");
			$daten = $query->fetch_array(MYSQLI_ASSOC);
			$jahr = $daten["jahr"];
			$jahrkurz = substr($jahr, 2);
			$num = $daten["num"];
			$nr = $_GET["nr"];

			$path = "../../FFW-Page/einsaetze/".$jahr."/images".$jahr."/e".$num."_".$jahrkurz."_".$nr.".jpg";
			if(file_exists($path)) {
				header("Content-Type: image/jpeg");
				echo file_get_contents($path);
				exit();
			}else{
				http_response_code(404);
				exit();
			}
		}else{
			http_response_code(406); //falsche Parameter
			exit();
		}
	}else
	if(isset($_GET["getCourses"])) {
		if(!check_right(9)) { // Darf Lehrgänge verwalten
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		$query = $mysqli->query("SELECT `lehrgang_id`, `name`, `abkuerzung`, `icon`, `reihenfolge` FROM `int__lehrgaenge` ORDER BY `int__lehrgaenge`.`reihenfolge` ASC, `int__lehrgaenge`.`name` ASC");
		$output = $query->fetch_all(MYSQLI_ASSOC);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getCourseData"]) && $_GET["getCourseData"] != "") {
		if(!check_right(9)) { // Darf Lehrgänge verwalten
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}
		$lehrgang = mysqli_real_escape_string($mysqli, $_GET["getCourseData"]);

		$query = $mysqli->query("SELECT `lehrgang_id`, `name`, `abkuerzung`, `icon`, `reihenfolge` FROM `int__lehrgaenge` WHERE `lehrgang_id` = '$lehrgang' LIMIT 1");
		$output = array();
		$daten = $query->fetch_array(MYSQLI_ASSOC);
		$output["lehrgang_id"] = $daten["lehrgang_id"];
		$output["name"] = $daten["name"];
		$output["abkuerzung"] = $daten["abkuerzung"];
		$output["icon"] = $daten["icon"];
		$output["reihenfolge"] = $daten["reihenfolge"];

		// Mitglieder mit diesen Lehrgängen abrufen
		$query = $mysqli->query("SELECT `user_id` FROM `int__benutzer-lehrgaenge` WHERE `lehrgang_id` = '$lehrgang'");
		$tmp = array();
		while($mitglied = $query->fetch_array(MYSQLI_ASSOC)) {
			$tmp[] = intval($mitglied["user_id"]);
		}
		$output["mitglieder"] = $tmp;

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getUserData"]) && $_GET["getUserData"] != "") {
		if(!check_right(2))
		{ //Admins only: Recht NR 2!
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}

		$userid = mysqli_real_escape_string($mysqli, $_GET["getUserData"]);

		$output = array();
		$result = $mysqli->query("SELECT `user_id`, `Nachname`, `Vorname`, `Telefon`, `Mobil`, `Mail`, `Strasse`, `Wohnort`, `PLZ`, `pw_zuruecksetzen` FROM `int__benutzer` WHERE `user_id` = '$userid' LIMIT 1")->fetch_array(MYSQLI_ASSOC);
		$output["userid"] = $result["user_id"];
		$output["Nachname"] = $result["Nachname"];
		$output["Vorname"] = $result["Vorname"];
		$output["Telefon"] = $result["Telefon"];
		$output["Mobil"] = $result["Mobil"];
		$output["Mail"] = $result["Mail"];
		$output["Strasse"] = $result["Strasse"];
		$output["Wohnort"] = $result["Wohnort"];
		$output["PLZ"] = $result["PLZ"];
		$output["pwzurueckDeaktiv"] = $result["pw_zuruecksetzen"];

		$query = $mysqli->query("SELECT `int__benutzer-gruppen`.`gruppen_id`, `Beschreibung`, `Prioritaet` FROM `int__benutzer-gruppen`, `int__gruppen` WHERE `int__benutzer-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id` AND `user_id` = '$userid'");
		$tmp = array();
		while($result = $query->fetch_array(MYSQLI_ASSOC)) {
			$gruppe = array();
			$gruppe["id"] = $result["gruppen_id"];
			$gruppe["beschreibung"] = $result["Beschreibung"];
			$gruppe["Prioritaet"] = $result["Prioritaet"];
			$output["gruppen"][] = $gruppe;
		}

		$query = $mysqli->query("SELECT `lehrgang_id` FROM `int__benutzer-lehrgaenge` WHERE `user_id` = '$userid'");
		$tmp = array();
		while($lehrgang = $query->fetch_array(MYSQLI_ASSOC)) {
			$tmp[] = intval($lehrgang["lehrgang_id"]);
		}
		$output["lehrgaenge"] = $tmp;

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output, JSON_UNESCAPED_UNICODE);

		exit;
	}else
	if(isset($_GET["getNews"])) {
		if(!check_right(7))
		{ //Darf News und Termine der Website bearbeiten
			http_response_code(401);
			echo "Keine Berechtigung!";
			exit();
		}

		$out = array();
		$outnews = array();
		if(isset($_GET["o"]) && $_GET["o"] > 0){$off = "WHERE `datum` < '".mysqli_real_escape_string($mysqli, $_GET["o"])."'";}
		$query = $mysqli->query("SELECT `id`, `titel`, `datum`, `kategorie` FROM `news` $off ORDER BY `datum` DESC, `id` DESC LIMIT 20");
		if($query->num_rows == 0 && isset($_GET["o"]) && $_GET["o"] > 0) {
			$out["offset"] = $_GET["o"];
		}
		while($news = $query->fetch_array(MYSQLI_ASSOC)) {
			if(!isset($out["offset"]) || strcmp($out["offset"], $news["datum"]) > 0){$out["offset"] = $news["datum"];}
			$tmp = array();
			$tmp["id"] = $news["id"];
			$tmp["kategorie"] = $news["kategorie"];
			$tmp["datum"] = date('d.m.Y', strtotime($news["datum"]));
			$tmp["titel"] = $news["titel"];
			$outnews[] = $tmp;
		}
		$out["news"] = $outnews;

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
		exit();
	}else
	if(isset($_GET["getFAQs"])) {
		if(!check_right(8))
		{ // Darf das FAQ verwalten
			http_response_code(401);
			exit();
		}

		$query = $mysqli->query("SELECT `faq_id`, `prioritaet`, `frage`, `antwort` FROM `int__faq` ORDER BY `prioritaet`");

		$out = array();

		while($faq = $query->fetch_array(MYSQLI_ASSOC)) {
			$tmp = array();
			$tmp["id"] = $faq["faq_id"];
			$tmp["folge"] = $faq["prioritaet"];
			$tmp["frage"] = $faq["frage"];
			$tmp["antwort"] = $faq["antwort"];
			$out[] = $tmp;
		}

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($out, JSON_UNESCAPED_UNICODE);
		exit();

		exit();
	}
}

http_response_code(404);
exit();
?>
