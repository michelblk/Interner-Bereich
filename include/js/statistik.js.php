<?php
require("../php/auth.php");
require("../php/db.php");
header("Content-Type:application/javascript; charset=utf-8");
$monatsNamen = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

// ------------------------ Abfragen und Berechnungen --------------------------

//Anwesenheit
$query = $mysqli->query("SELECT COUNT(DISTINCT `int__benutzer-einsatz-anwesenheit`.`einsatz_id`) as 'anzahl' FROM `int__benutzer-einsatz-anwesenheit`, `einsaetze` WHERE `user_id` = '".$_SESSION["userid"]."' AND `einsaetze`.`id` = `int__benutzer-einsatz-anwesenheit`.`einsatz_id` AND YEAR(`einsaetze`.`startTime`) = YEAR(NOW())");
$anwesend = $query->fetch_object()->anzahl;
// Auswertung von XX Einsätzen
$query = $mysqli->query("SELECT COUNT(DISTINCT `int__benutzer-einsatz-anwesenheit`.`einsatz_id`) as 'gesamt' FROM `int__benutzer-einsatz-anwesenheit`, `einsaetze` WHERE `einsaetze`.`id` = `int__benutzer-einsatz-anwesenheit`.`einsatz_id` AND YEAR(`einsaetze`.`startTime`) = YEAR(NOW())");
$gesamt = $query->fetch_object()->gesamt;
// Einträge des Jahres
$query = $mysqli->query("SELECT COUNT(*) as 'anzahl' FROM `einsaetze` WHERE YEAR(`startTime`) = YEAR(now())");
$eintraege_dieses_jahr = $query->fetch_object()->anzahl;
if($gesamt == NULL) {$anwesenheit = "k.A."; $anwesenheit_anzahl = 0;}else{$anwesenheit = round(($anwesend/$gesamt) * 100, 0);$anwesenheit_anzahl = $gesamt;}

//Anwesenheit bestenliste in statistik.php
$AwBestenlisteQuery = $mysqli->query("SELECT `int__benutzer`.`user_id`, `int__benutzer`.`Vorname`, `int__benutzer`.`Nachname`, COUNT(DISTINCT `int__benutzer-einsatz-anwesenheit`.`einsatz_id`) as 'anzahl' FROM `int__benutzer` LEFT JOIN `int__benutzer-einsatz-anwesenheit` ON `int__benutzer`.`user_id` = `int__benutzer-einsatz-anwesenheit`.`user_id` LEFT JOIN `einsaetze` ON `einsaetze`.`id` = `int__benutzer-einsatz-anwesenheit`.`einsatz_id` AND YEAR(`einsaetze`.`startTime`) = YEAR(NOW()) GROUP BY `int__benutzer`.`user_id` ORDER BY `anzahl` DESC, `Nachname` ASC, `Vorname` ASC");

//Einsätze pro Monat
$query = $mysqli->query("SELECT (MAX(`num`) - MIN(`num`) + 1) as 'anzahl', MONTH(`startTime`) as 'monat', YEAR(`startTime`) as 'jahr' FROM `einsaetze` WHERE `startTime` >= DATE_SUB(NOW(), INTERVAL 2 YEAR)  GROUP BY `jahr`, `monat`");
$epm = array();
$epm_max = 0;
$einsaetze_diesen_monat = 0;
while($anzahl = $query->fetch_array(MYSQLI_ASSOC)) {
	if($anzahl["jahr"] == date("Y") && $anzahl["monat"] == date("n")){$einsaetze_diesen_monat = $anzahl["anzahl"];}
	if($anzahl["anzahl"] > $epm_max){$epm_max = $anzahl["anzahl"];}
	$tmp = array();
	$tmp["monat"] = $anzahl["jahr"].", ".$monatsNamen[$anzahl["monat"]-1];
	$tmp["anzahl"] = $anzahl["anzahl"];
	$tmp["m"] = $anzahl["monat"];
	$tmp["j"] = $anzahl["jahr"];

	$epm[] = $tmp;
}


$jahre = 12; // Wieviele Jahren einbezogen werden sollen

// Einsätze pro Jahr
$einsaetze_dieses_jahr = 0;
$epj_max = 0;
$epj_min = 9999;
$query = $mysqli->query("SELECT MAX(`num`) as 'anzahl', YEAR(`startTime`) as 'jahr' FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre  GROUP BY `jahr`");
$epj = array();
while($anzahl = $query->fetch_array(MYSQLI_ASSOC)) {
	if($anzahl["jahr"] == date("Y")){$einsaetze_dieses_jahr = $anzahl["anzahl"];}
	if($anzahl["anzahl"] > $epj_max){$epj_max = $anzahl["anzahl"];}
	if($anzahl["anzahl"] < $epj_min){$epj_min = $anzahl["anzahl"];}

	$tmp = array();
	$tmp["jahr"] = $anzahl["jahr"];
	$tmp["anzahl"] = intval($anzahl["anzahl"]);
	$epj[] = $tmp;
}

// Einsätze pro Monat im Durchschnitt
$query = $mysqli->query("SELECT `monat`, (SUM(`qu`.`num`)/count(DISTINCT `year`)) as 'avg' FROM (SELECT MONTH(`startTime`) as 'monat', (MAX(`num`) - MIN(`num`) + 1) as 'num', `year` FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre GROUP BY `year`, `monat`) qu GROUP BY `monat`");
/* Mit id zählen: SELECT (count(`id`) / count(DISTINCT `year`)) as 'avg', MONTH(`startTime`) as 'monat' FROM `einsaetze` WHERE `startTime` > DATE_SUB(NOW(), INTERVAL 15 YEAR)  GROUP BY `monat` */
$epmid = array();
$durschn_einsaetze_des_monats = 0;
while($anzahl = $query->fetch_array(MYSQLI_ASSOC)) {
	$tmp = array();
	$tmp["monat"] = $monatsNamen[$anzahl["monat"]-1];
	$tmp["avg"] = round($anzahl["avg"], 1);
	$epmid[] = $tmp;

	if($anzahl["monat"] == date("n")){$durschn_einsaetze_des_monats = $tmp["avg"];}
}

//Durchschnittliche Einsatzdauer
$query = $mysqli->query("SELECT YEAR(`startTime`) as 'jahr', AVG(TIMESTAMPDIFF(MINUTE,`startTime`, `endTime`)) as 'zeit' FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre GROUP BY `jahr`");
$ded = array();
$ded_min = 9999;
$ded_dieses_jahr = 0;
while($avg = $query->fetch_array(MYSQLI_ASSOC)) {
	$tmp = array();
	$tmp["jahr"] = $avg["jahr"];
	$tmp["zeit"] = round($avg["zeit"], 1);
	$ded[] = $tmp;

	if($tmp["zeit"] < $ded_min){$ded_min = $tmp["zeit"];}
	if($tmp["jahr"] == date("Y")){$ded_dieses_jahr = $tmp["zeit"];}
}

//Nachteinsaetze
$query = $mysqli->query("SELECT count(*)/(SELECT count(*) FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre ) as 'num' FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre AND (HOUR(`startTime`) < 7 OR HOUR(`startTime`) >= 18)");
$nachteinsaetze = round($query->fetch_object()->num * 100, 0);

//Durchschnittliche Einsatzzeit
$jahre = 12;
$query = $mysqli->query("SELECT HOUR(`einsaetze`.`startTime`) as 'stunde', (COUNT(*) / `e`.`cnt`)*100 as 'prozent' FROM `einsaetze` CROSS JOIN (SELECT count(*) as 'cnt' FROM `einsaetze` WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre) e WHERE YEAR(`startTime`) >= YEAR(NOW())-$jahre GROUP BY `stunde`");
$dez = array();
while($avg = $query->fetch_array(MYSQLI_ASSOC)) {
	$tmp = array();
	$tmp["stunde"] = str_pad($avg["stunde"], 2, 0, STR_PAD_LEFT).":00";
	$tmp["prozent"] = round($avg["prozent"], 1);
	$dez[] = $tmp;
}


// Durchschnittliche Anzahl von Mitgliedern nach Uhrzeit
$query = $mysqli->query("SELECT HOUR(`einsaetze`.`startTime`) as 'stunde', COUNT(`int__benutzer-einsatz-anwesenheit`.`user_id`)/COUNT(DISTINCT `int__benutzer-einsatz-anwesenheit`.`einsatz_id`) as 'avg' FROM `einsaetze`, `int__benutzer-einsatz-anwesenheit` WHERE `einsaetze`.`id` = `int__benutzer-einsatz-anwesenheit`.`einsatz_id` AND YEAR(`einsaetze`.`startTime`) >= YEAR(NOW())-$jahre GROUP BY `stunde`");
$dMaU = array();
$dMaUMax = 0;
$dMaUMin = 999999;
$dMa = 0;
$i = 0;
while($avg = $query->fetch_array(MYSQLI_ASSOC)) {
	$tmp = array();
	$tmp["stunde"] = str_pad($avg["stunde"], 2, 0, STR_PAD_LEFT).":00";
	$tmp["mitglieder"] = round($avg["avg"], 0);
	if(round($avg["avg"], 0) > $dMaUMax)$dMaUMax = round($avg["avg"], 0);
	if(round($avg["avg"], 0) < $dMaUMin)$dMaUMin = round($avg["avg"], 0);
	$dMaU[] = $tmp;
	$dMa += round($avg["avg"], 0);
	$i++;
}
$dMa = round($dMa / $i, 0); //Durchschnittliche Mitgliederanzahl ohne Uhrzeit

//Gruppenauswertung
$query = $mysqli->query("SELECT `int__gruppen`.`Beschreibung`, GROUP_CONCAT(' ', `int__benutzer`.`Vorname`, ' ', `int__benutzer`.`Nachname` ORDER BY `int__benutzer`.`Nachname` ASC, `int__benutzer`.`Vorname` ASC) as 'namen', COUNT(`int__benutzer-gruppen`.`user_id`) as 'anzahl' FROM `int__gruppen` LEFT JOIN `int__benutzer-gruppen` ON `int__gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` INNER JOIN `int__benutzer` ON `int__benutzer-gruppen`.`user_id` = `int__benutzer`.`user_id` GROUP BY `int__gruppen`.`gruppen_id` ORDER BY `anzahl`DESC, `int__gruppen`.`Beschreibung` ASC");
$groups = array();
$group_members = array();
while($group = $query->fetch_array(MYSQLI_ASSOC)) {
$tmp = array();
$tmp["name"] = $group["Beschreibung"];
$tmp["anzahl"] = $group["anzahl"];
$groups[] = $tmp;
$group_members[] = $group["namen"];
}


// ---------------------------------- Ausgabe ----------------------------------

?>

$(document).ready(function () {

	$("#anwesenheit").text("<?php echo $anwesenheit; ?>%");
	$("#anwesenheit-beschreibung, #anwesenheit").attr('title', '<?php echo $anwesenheit_anzahl; ?> von <?php echo $eintraege_dieses_jahr; ?> Einträge ausgewertet').tooltip();
	<?php
	$i = 1;
	$found = false;
	$max = 99999;
	$platz = 0;
	while($bester = $AwBestenlisteQuery->fetch_array(MYSQLI_ASSOC)) {
		if($bester["anzahl"] < $max){$platz++; $max = $bester["anzahl"];}
		if($i <= 5){ // Die 5 Besten
			echo "$('#awBestenliste div[data-nr=$i] .bild').css('background-image', 'url(\"adressliste-action.php?getUserImage&user=".$bester["user_id"]."\")');\n";
			echo "$('#awBestenliste div[data-nr=$i] .name').text('".$bester["Vorname"]." ".$bester["Nachname"]."');\n";
			echo "$('#awBestenliste div[data-nr=$i] .platz').text('$platz');\n";
			echo "$('#awBestenliste div[data-nr=$i] .prozent').text('".round(($bester["anzahl"]/$anwesenheit_anzahl)*100, 0)."%');\n";
		}
		if($bester["user_id"] == $_SESSION["userid"] && !$found) {
			$found = true;
			echo "$('#anwesenheit_platz').text('$platz');";
		}
		$i++;
	}
	?>
	$("#anwesenheit_platz").attr('title', 'Von <?php echo $platz; ?> Plätzen').tooltip();

	$("#einsaetze-dieses-jahr").text("<?php echo $einsaetze_dieses_jahr; ?>");
	$("#einsaetze-diesen-monat").text("<?php echo $einsaetze_diesen_monat; ?>");
	$("#nachteinsaetze").text("<?php echo $nachteinsaetze; ?> %");

	$("#durchschnittliche-einsaetze-diesen-monat").text("<?php echo $durschn_einsaetze_des_monats; ?>");
	$(".monatsnamen").text("<?php echo $monatsNamen[date("n")-1]; ?>");
	$(".jahr").text("<?php echo date("Y"); ?>");
	$("#durchschnittliche-einsatzdauer-dieses-jahr").text("<?php echo $ded_dieses_jahr; ?> Min");
	$("#durchschnittliche-mitgliederanzahl").text("<?php echo $dMa; ?>");
});

$(function() {

	// Durchschnittliche Anzahl der Mitglieder nach Uhrzeit
	Morris.Area({
		element: 'durchschnittliche-mitgliederanzahl-uhrzeit',
		data: <?php echo json_encode($dMaU, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'stunde',
		ykeys: ['mitglieder'],
		labels: ['Mitglieder'],
		xLabels: ['hour'],
		parseTime: false,
		pointSize: 1,
		hideHover: 'auto',
		resize: true,
		grid: true,
		fillOpacity: 0.1,
		smooth: true,
		lineColors: ["#429b43"],
		ymax: <?php echo $dMaUMax+5; ?>,
		ymin: <?php echo $dMaUMin-5; ?>,
	}).on('click', function (i, info) {
		Info('zeit', info["stunde"].split(":")[0]);
	});

	/*//Anwesenheit Kuchendiagramm
	Morris.Donut({
		element: 'anwesenheit-donut',
		data: [
		{label: "Anwesend", value: <?php echo $anwesend; ?>},
		{label: "Abwesend", value: <?php echo $anwesenheit_anzahl - $anwesend; ?>},
		{label: "Unbekannt", value: <?php echo $jahrgesamt - $anwesenheit_anzahl; ?>}
		],
		colors: ["#429b43", "#a94442", "#eeeeee"],
		resize: true
	});*/

	// Einsätze Pro Jahr
	Morris.Bar({
		element: 'einsaetze-pro-jahr',
		data: <?php echo json_encode($epj, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'jahr',
		ykeys: ['anzahl'],
		labels: ['Anzahl'],
		xLabels: "year",
		hideHover: 'auto',
		resize: true,
		grid: true,
		ymax: <?php echo $epj_max+5; ?>,
		ymin: <?php echo $epj_min-5; ?>,
		barColors: ["#a94442"]
	}).on('click', function (i, info) {
		Info('jahr', info["jahr"]);
	});

	// Einsätze Pro Monat
	Morris.Bar({
		element: 'einsaetze-pro-monat',
		data: <?php echo json_encode($epm, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'monat',
		ykeys: ['anzahl'],
		labels: ['Anzahl'],
		xLabels: "month",
		pointSize: 1,
		hideHover: 'auto',
		resize: true,
		grid: true,
		ymax: <?php echo $epm_max+2; ?>,
		barColors: ["#a94442"]
	}).on('click', function (i, info) {
		Info('monatJahr', info["monat"]);
	});;

	// Einsätze Pro Monat im Durchschnitt
	Morris.Bar({
		element: 'einsaetze-pro-monat-im-durchschnitt',
		data: <?php echo json_encode($epmid, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'monat',
		ykeys: ['avg'],
		labels: ['Durchschnitt'],
		parseTime: false,
		pointSize: 1,
		hideHover: 'auto',
		resize: true,
		grid: true,
		xLabelAngle: 35,
		barColors: ["#31708f"]
	}).on('click', function (i, info) {
		Info('monat', i+1); //statt Monatsnamen, Monatsnummer
	});

	// Durchschnittliche Einsatzdauer
	Morris.Area({
		element: 'durchschnittliche-einsatzdauer',
		data: <?php echo json_encode($ded, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'jahr',
		ykeys: ['zeit'],
		labels: ['Minuten'],
		parseTime: false,
		pointSize: 1,
		hideHover: 'auto',
		resize: true,
		grid: true,
		fillOpacity: 0.1,
		smooth: true,
		ymin: <?php echo $ded_min-5; ?>,
		lineColors: ["#31708f"]
	}).on('click', function (i, info) {
		Info('LaengeJahr', info["jahr"]);
	});

	// Durchschnittliche Stunde des Einsatzstartes
	Morris.Area({
		element: 'durchschnittliche-einsatzzeit',
		data: <?php echo json_encode($dez, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'stunde',
		ykeys: ['prozent'],
		labels: ['Prozent'],
		xLabels: ['hour'],
		parseTime: false,
		pointSize: 1,
		hideHover: 'auto',
		resize: true,
		grid: true,
		fillOpacity: 0.1,
		smooth: true,
		lineColors: ["#337ab7"]
	}).on('click', function (i, info) {
		Info('zeit', info["stunde"].split(":")[0]);
	});

	// Gruppen
	var gruppenmitglieder = <?php echo json_encode($group_members, JSON_UNESCAPED_UNICODE); ?>;
	Morris.Bar({
		element: 'gruppenauswertung',
		data: <?php echo json_encode($groups, JSON_UNESCAPED_UNICODE); ?>,
		xkey: 'name',
		ykeys: ['anzahl'],
		labels: ['Anzahl'],
		parseTime: false,
		hideHover: 'auto',
		resize: true,
		grid: true,
		xLabelAngle: 35,
		barColors: ["#429b43"]
	}).on('click', function (index, info) {
		$("#gruppenauswertung-namen span").html("<b> "+info["name"]+ "</b>: " + gruppenmitglieder[index]);
	});
});

function Info(typ, filter) {
	if(typ == "LaengeJahr") location.href = "?detail=einsatz&jahr="+filter+"&sortBy=dauer&sortOrder=DESC";
	if(typ == "jahr") location.href = "?detail=einsatz&jahr="+filter+"&sortBy=nr&sortOrder=ASC";
	if(typ == "zeit") location.href = "?detail=einsatz&zeit="+filter+"&sortBy=zeit&sortOrder=ASC";
	if(typ == "monat") location.href = "?detail=einsatz&monat="+filter+"&sortBy=datum&sortOrder=ASC";
	if(typ == "monatJahr") location.href = "?detail=einsatz&monatjahr="+filter+"&sortBy=nr&sortOrder=ASC";
}

<?php $mysqli->close(); ?>
