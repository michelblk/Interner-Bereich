<?php
require("include/php/auth.php"); require("include/php/db.php");
if($_SERVER["REQUEST_METHOD"] == "GET"){
	if(isset($_GET["detail"])) {
		if($_GET["detail"] == "einsaetze") {
			if(isset($_GET["typ"]) && $_GET["typ"] != "" && isset($_GET["filter"]) && $_GET["filter"] != ""){

				//Sortierung
				$order = "";
				if(isset($_GET["sortBy"]) && $_GET["sortBy"] != ""){
					if($_GET["sortBy"] == "nr"){$order.="`num`";}
					else if($_GET["sortBy"] == "datum"){$order.="DATE(`einsaetze`.`startTime`)";}//nicht `datum`, weil falsches format
					else if($_GET["sortBy"] == "zeit"){$order.="`zeit`";}
					else if($_GET["sortBy"] == "name"){$order.="`title`";}
					else if($_GET["sortBy"] == "anwesende"){$order.="`anwesende`";}
					else if($_GET["sortBy"] == "dauer"){$order.="`dauer`";}
					else {
						$order.="`num`";
					}
				}else{
					$order.="`num`";
				}
				if(isset($_GET["sortOrder"]) && $_GET["sortOrder"] != ""){
					if($_GET["sortOrder"] == "ASC"){$order.=" ASC";}
					else if($_GET["sortOrder"] == "DESC") {$order.=" DESC";}
					else {
						$order.=" DESC";
					}
				}else{
					$order.=" DESC";
				}


				// Typ
				if($_GET["typ"] == "jahr"){
					$where = "`einsaetze`.`year` =  '".mysqli_real_escape_string($mysqli, $_GET["filter"])."'";
				}else
				if($_GET["typ"] == "monat") {
					$where = "MONTH(`einsaetze`.`startTime`) =  '".mysqli_real_escape_string($mysqli, $_GET["filter"])."'";
				}else
				if($_GET["typ"] == "zeit") {
					$where = "HOUR(`einsaetze`.`startTime`) =  '".mysqli_real_escape_string($mysqli, $_GET["filter"])."'";
				}else
				if($_GET["typ"] == "monatjahr") {
					$jahr = explode(", ",$_GET["filter"])[0];
					$monat = explode(", ",$_GET["filter"])[1];
					$monateNachNamen = json_decode('{"Januar":1,"Februar":2,"März":3,"April":4,"Mai": 5,"Juni":6,"Juli":7,"August":8,"September":9,"Oktober":10,"November":11,"Dezember":12}', true);
					$monat = $monateNachNamen[$monat];
					$where = "MONTH(`einsaetze`.`startTime`) = '$monat' AND `einsaetze`.`year` = '$jahr'";
				}else{ //typ unbekannt
					http_response_code(404);
					exit();
				}

				//Output
				$out = array();
				$jahre = 12; //Zeige die letzen 12 Jahre
				$einsaetze = $mysqli->query("SELECT `einsaetze`.`id`, `einsaetze`.`assNum`, `einsaetze`.`num`, `einsaetze`.`title`, DATE_FORMAT(`einsaetze`.`startTime`, '%d.%m.%Y') AS 'datum', DATE_FORMAT(`einsaetze`.`startTime`, '%H:%i') AS 'zeit', YEAR(`startTime`) as 'jahr', TIMESTAMPDIFF(MINUTE,`startTime`, `endTime`) AS 'dauer', count(`int__benutzer-einsatz-anwesenheit`.`user_id`) as 'anwesende' FROM `einsaetze` LEFT JOIN `int__benutzer-einsatz-anwesenheit` ON `einsaetze`.`id` = `int__benutzer-einsatz-anwesenheit`.`einsatz_id` WHERE $where AND `year` >= YEAR(NOW())-$jahre GROUP BY `einsaetze`.`id` ORDER BY ".$order);
				while($einsatz = $einsaetze->fetch_array(MYSQLI_ASSOC)) {
					$tmp = array();
					$tmp["einsatz_id"] = $einsatz["id"];
					$tmp["num"] = $einsatz["num"];
					$tmp["assNum"] = ($einsatz["assNum"] == "" ? $einsatz["num"] : $einsatz["assNum"]);
					$tmp["name"] = $einsatz["title"];
					$tmp["datum"] = $einsatz["datum"];
					$tmp["zeit"] = $einsatz["zeit"];
					$tmp["jahr"] = $einsatz["jahr"];
					$tmp["dauer"] = (intval($einsatz["dauer"]) >= 60 ? (str_pad(floor(intval($einsatz["dauer"])/60), 2 ,'0', STR_PAD_LEFT).":".str_pad((intval($einsatz["dauer"]) % 60), 2, '0', STR_PAD_LEFT)." h") : (str_pad($einsatz["dauer"], 2, '0', STR_PAD_LEFT)." min"));
					$tmp["anwesende"] = intval($einsatz["anwesende"]);

					$out[] = $tmp;
				}
				header("Content-Type: application/json; charset=utf-8");
				echo json_encode($out, JSON_UNESCAPED_UNICODE);
				exit();
			}
		}
	}
}
http_response_code(404);
exit();
?>
