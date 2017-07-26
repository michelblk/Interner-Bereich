<?php require("../include/php/auth.php"); require("../include/php/db.php");

$recht3 = check_right(3);
$recht7 = check_right(7);
if(!$recht3 && !$recht7)
{ //Webmaster only: Recht NR 3! / Darf News bearbeiten: RECHT NR 7
	http_response_code(401);
	echo "Keine Berechtigung!";
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Website bearbeiten</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="../include/js/jQuery.js"></script>
	<script src="../include/js/main.js"></script>
	<script src="../include/js/bootstrap.min.js"></script>
	<link href="../include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../include/css/sb-admin.min.css" rel="stylesheet" />
	<link href="../include/css/font-awesome.min.css" rel="stylesheet" />
	<link href="../include/css/main.css" rel="stylesheet" />
	<link href="../include/css/einstellungen-website.css" rel="stylesheet" />
</head>
<body>
	<div id="wrapper">
		<!-- Navigation -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="../index.php">Intern</a>
			</div>
			<!-- Top Menu Items -->
			<div class="collapse navbar-collapse navbar-ex1-collapse top-nav-overflow">
				<ul class="nav navbar-right top-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $_SESSION["vorname"]." ".$_SESSION["nachname"]; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li>
								<a href="../einstellungen/"><i class="fa fa-fw fa-gear"></i> Einstellungen</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="../logout.php"><i class="fa fa-fw fa-power-off"></i> Abmelden</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">
					<li>
						<a href="../index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
					</li>
					<li>
						<a href="../adressliste.php"><i class="fa fa-fw fa-address-book"></i> Adressbuch</a>
					</li>
					<li>
						<a href="../lehrgangsliste.php"><i class="fa fa-fw fa-address-card"></i> Lehrgangsliste</a>
					</li>
					<li>
						<a href="../dienstplan.php"><i class="fa fa-fw fa-calendar"></i> Dienstplan</a>
					</li>
					<li>
						<a href="../dateifreigabe.php"><i class="fa fa-fw fa-files-o"></i> Dateiablage</a>
					</li>
					<li>
						<a href="../statistik.php"><i class="fa fa-fw fa-line-chart"></i> Statistik</a>
					</li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>

		<div id="page-wrapper">
			<div class="container-fluid">
				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">
							Website
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<?php if(!isset($_GET["operations"]) && !isset($_GET["news"])) { //nicht gewählt ?>
						<a href="index.php">Zurück</a>
						<?php if($recht3){ ?>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<a href="?operations">
									<div class="panel panel-yellow">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-id-card-o fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Einsätze</div>
												</div>
											</div>
										</div>

										<div class="panel-footer">
											<span class="pull-left">Einsätze bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>

									</div>
								</a>
							</div>
							<?php }
							if($recht7) { ?>
							<div class="col-lg-6 col-md-6">
								<a href="?news">
									<div class="panel panel-green">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-users fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Aktuelles</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">Aktuelles bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</a>
							</div>
							<?php } ?>
						</div>
					<?php }
					else if(isset($_GET["operations"]) && $recht3){ // Einsätze bearbeiten ?>
						<script src="../include/js/einstellungen-website-einsaetze.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-yellow">
							<div class="panel-heading">
								<h4><i class="fa fa-id-card-o"></i> Einsatzverwaltung</h4>
							</div>
							<div class="panel-body">
								<div id="einsaetze">
									<button type="button" class="btn btn-outline btn-primary" onclick="neuerEinsatz();">Erstelle einen Eintrag</button> oder wähle einen Einsatz.
									<table class="table table-hover table-striped">
										<thead>
											<tr><th>ID</th><th>AssNum</th><th>Datum</th><th><i class="fa fa-clock-o"></i> Min</th><th>Name</th></tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<script>$(document).ready(function (){loadOperations(0);});</script>
									<a id="einsaetze-lademehr" onclick="loadOperations(1)">Mehr laden</a>
								</div>
								<div id="einsatzBearbeiten">
									<a href="#" class="backtoSelectOperation">Zurück und anderen Einsatz wählen</a>
									<form action="#" method="POST" id="einsatzBearbeitenForm">
										<table class="table table-hover table-striped">
											<thead>
												<tr><th colspan="2">Einsatzinformationen</th></tr>
											</thead>
											<tbody>
												<tr><td>ID</td><td data-info="id" class="donotclear"><input type="number" readonly class="input-control" name="id"></tr>
												<tr><td>Titel</td><td data-info="titel"><input class="input-control" type="text" name="titel" required /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Art</td><td data-info="art"><input class="input-control" type="text" name="art" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Ort</td><td data-info="ort"><input class="input-control" type="text" name="ort" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Nummer</td><td data-info="num"><input class="input-control" type="number" name="num" required /> als <input class="input-control" type="text" name="assNum" placeholder="z.B. 12-14 (optional)" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Zeit</td><td data-info="zeit"><input class="input-control" type="datetime-local" name="beginn" required /> - <input class="input-control" type="datetime-local" name="ende" required /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Text</td><td data-info="text"><textarea class="form-control" rows="7" name="text"></textarea><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Lightbox Text</td><td data-info="lightboxText"><textarea class="form-control" rows="2" name="lightboxText"></textarea><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr>
													<td>Fahrzeuge</td>
													<td data-info="fahrzeuge" class="donotclear">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<div style="display: none; height: 0px; width: 0px"><div class="checkbox-fahrzeuge"><label><input type="text" name="fahrzeuge[]" value="-1" /></label></div></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="elw" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/elw.jpg');" data-image></div><span>ELW</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="lf10" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/lf10.JPG');" data-image></div><span>LF10 KatS</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="lf20" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/lf20.JPG');" data-image></div><span>LF20</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="tlf" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/tlf.jpg');" data-image></div><span>HTLF</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="gwl" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/gwl.JPG');" data-image></div><span>GW-L1</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="klkw" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/klkw.JPG');" data-image></div><span>KLKW</span></label></div>
															<div class="checkbox-fahrzeuge"><label><input type="checkbox" name="fahrzeuge[]" value="mtf" /><div style="background-image: url('http://www.domain.de/einsaetze/fahrzeuge/mtf.jpg');" data-image></div><span>MTF</span></label></div>
														</div>
													</td>
												</tr>
												<tr><td>Bilder</td>
													<td data-info="bilder" class="donotclear">
														<div class="input-group">
															<label class="input-group-btn">
																<span class="btn btn-primary btn-outline">
																	Dateien auswählen <input class="input-control" type="file" accept="image/jpg,image/jpeg" name="bilder[]" style="display: none;" multiple />
																</span>
															</label>
															<input type="text" class="form-control" readonly style="width: 100%; max-width: 150px;" />
														</div>
														<div style="display: none; height: 0px; width: 0px"><div class="checkbox-bilder"><label><input type="text" name="existierendeBilder[]" value="-1" /></label></div></div>
														<div data-info="bilder" class="form-group">

														</div>
													</td>
												</tr>
												<tr>
													<td>Anwesende</td>
													<td data-info="anwesende" class="donotclear">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
																<div style="display: none; height: 0px; width: 0px"><div class="checkbox-amwesende"><label><input type="text" name="anwesende[]" value="-1" /></label></div></div>
															<?php
															$users = $mysqli->query("SELECT `user_id`, `Vorname`, `Nachname` FROM `int__benutzer` ORDER BY `Nachname` ASC, `Vorname` ASC");
															while($user = $users->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox-anwesende\"><label><input type=\"checkbox\" name=\"anwesende[]\" value=\"".$user["user_id"]."\" /><div style=\"background-image: url('../adressliste-action.php?getUserImage&user=".$user["user_id"]."');\" data-image></div><span>".$user["Nachname"].", ".$user["Vorname"]."</span></label></div>\n";
															}
															?>
														</div>
													</td>
												</tr>
												<tr><td>Einsatz</td><td><button type="button" class="btn btn-outline btn-danger btn-xs" id="deleteOperationButton" onclick="loescheEinsatz();">Einsatz löschen</button></td></tr>
											</tbody>
										</table>
										<button type="submit" class="btn btn-outline btn-primary" disabled>Daten ändern</button>
										<br />
										<p><a href="#" class="backtoSelectOperation">Zurück und anderen Einsatz wählen</a></p>
									</form>
								</div>
							</div>
						</div>
					<?php }else if(isset($_GET["news"]) && $recht7){ //News bearbeiten ?>
						<script src="../include/js/einstellungen-website-news.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-green">
							<div class="panel-heading">
								<h4><i class="fa fa-users"></i> News-Verwaltung</h4>
							</div>
							<div class="panel-body">
								<div id="news">
									<button type="button" class="btn btn-outline btn-primary" onclick="neuerEinsatz();">Erstelle</button> oder wähle einen Eintrag.
									<table class="table table-hover table-striped">
										<thead>
											<tr><th>ID</th><th>Datum</th><th>Kategorie</th><th>Titel</th></tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<script>$(document).ready(function (){loadNews(0);});</script>
									<a id="news-lademehr" onclick="loadNews(1)">Mehr laden</a>
								</div>
								<div id="newsBearbeiten">
									- geplant -
								</div>
							</div>
					<?php }else{ ?>
						Nicht gefunden oder keine Berechtigung.
					<?php } ?>
				</main>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->
</body>
</html>
