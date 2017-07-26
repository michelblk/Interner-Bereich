<?php require("include/php/auth.php"); require("include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Statistik</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="include/js/jQuery.js"></script>
	<script src="include/js/main.js"></script>
	<script src="include/js/raphael-chart.js"></script>
	<script src="include/js/morris-chart.js"></script>
	<script src="include/js/bootstrap.min.js"></script>
	<link href="include/css/morris-chart.css" rel="stylesheet" />
	<link href="include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="include/css/sb-admin.min.css" rel="stylesheet" />
	<link href="include/css/font-awesome.min.css" rel="stylesheet" />
	<link href="include/css/main.css" rel="stylesheet" />
	<link href="include/css/statistik.css" rel="stylesheet" />
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
				<a class="navbar-brand" href="index.php">Intern</a>
			</div>
			<!-- Top Menu Items -->
			<div class="collapse navbar-collapse navbar-ex1-collapse top-nav-overflow">
				<ul class="nav navbar-right top-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $_SESSION["vorname"]." ".$_SESSION["nachname"]; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li>
								<a href="einstellungen/"><i class="fa fa-fw fa-gear"></i> Einstellungen</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Abmelden</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">
					<li>
						<a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
					</li>
					<li>
						<a href="adressliste.php"><i class="fa fa-fw fa-address-book"></i> Adressbuch</a>
					</li>
					<li>
						<a href="lehrgangsliste.php"><i class="fa fa-fw fa-address-card"></i> Lehrgangsliste</a>
					</li>
					<li>
						<a href="dienstplan.php"><i class="fa fa-fw fa-calendar"></i> Dienstplan</a>
					</li>
					<li>
						<a href="dateifreigabe.php"><i class="fa fa-fw fa-files-o"></i> Dateiablage</a>
					</li>
					<li class="active">
						<a href="statistik.php"><i class="fa fa-fw fa-line-chart"></i> Statistik</a>
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
							Statistik
						</h1>
					</div>
				</div>
				<main>
					<?php if(!isset($_GET["detail"])) {?>
						<script src="include/js/statistik.js.php"></script>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-success">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-male fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge"><span id="anwesenheit_platz"></span> <span id="anwesenheit"></span></div>
												<div><span data-toggle="tooltip"  id="anwesenheit-beschreibung">deine Anwesenheit in diesem Jahr</span></div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div class="row" id="awBestenliste">
											<div class="bester" data-nr="1">
												<div class="bild"></div>
												<div class="name"></div>
												<div class="platz"></div>
												<div class="prozent"></div>
											</div>
											<div class="bester" data-nr="2">
												<div class="bild"></div>
												<div class="name"></div>
												<div class="platz"></div>
												<div class="prozent"></div>
											</div>
											<div class="bester" data-nr="3">
												<div class="bild"></div>
												<div class="name"></div>
												<div class="platz"></div>
												<div class="prozent"></div>
											</div>
											<div class="bester" data-nr="4">
												<div class="bild"></div>
												<div class="name"></div>
												<div class="platz"></div>
												<div class="prozent"></div>
											</div>
											<div class="bester" data-nr="5">
												<div class="bild"></div>
												<div class="name"></div>
												<div class="platz"></div>
												<div class="prozent"></div>
											</div>
										</div>
									</div>
									<!--<div class="panel-footer">
										<div id="anwesenheit-donut"></div>
									</div>-->
								</div>
							</div>
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-success">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-group fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="durchschnittliche-mitgliederanzahl">--</div>
												<div>Durschnittliche Mitglieder beim Einsatz</div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div id="durchschnittliche-mitgliederanzahl-uhrzeit" style="max-height: 300px;"></div>
										<div class="small text-center"><i class="fa fa-info"></i> Diese Grafik ist durch die geringe Zahl an verfügbaren Daten noch sehr ungenau.</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-fire-extinguisher fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="einsaetze-dieses-jahr">--</div>
												<div>Einsätze in diesem Jahr</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<div id="einsaetze-pro-jahr"></div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-calendar-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="einsaetze-diesen-monat">--</div>
												<div>Einsätze in diesem Monat</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<div id="einsaetze-pro-monat"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-info">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-line-chart fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="durchschnittliche-einsaetze-diesen-monat">--</div>
												<div>Einsätze durchschnittlich im <span class="monatsnamen"></span></div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div id="einsaetze-pro-monat-im-durchschnitt"></div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-info">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-clock-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="durchschnittliche-einsatzdauer-dieses-jahr">--</div>
												<div>durchschnittliche Länge der Einsätze <span class="jahr"></span></div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div id="durchschnittliche-einsatzdauer"></div>
										<div class="small text-center"><i class="fa fa-info"></i> Diese Grafik kann durch zusammengefasste Einsätze verfälscht sein.</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-moon-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge" id="nachteinsaetze">-- %</div>
												<div>Einsätze zwischen 18 und 07 Uhr</div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div id="durchschnittliche-einsatzzeit"></div>
										<div class="small text-center"><i class="fa fa-info"></i> Diese Grafik kann durch zusammengefasste Einsätze verfälscht sein.</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="small text-center"><i class="fa fa-info"></i> Die Durchschnittswerte basieren auf den Einsatzzahlen der letzten 12 Jahre.</div>
							<div class="small text-center"><i class="fa fa-info"></i> Monate, in denen kein Einsatz stattgefunden hat, werden nicht berücksichtigt.</div>
						</div>

						<hr />

						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="panel panel-success">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-users fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge">Gruppen</div>
												<div><!-- Beschreibung --></div>
											</div>
										</div>
									</div>
									<div class="panel-body">
										<div id="gruppenauswertung"></div>
										<div id="gruppenauswertung-namen" class="small"><i class="fa fa-info"></i><span>Hier werden die Gruppenmitglieder angezeigt</span></div>
									</div>
								</div>
							</div>
						</div>
						<?php }else{
							if($_GET["detail"] == "einsatz") { ?>
								<script src="include/js/statistik-detail.js"></script>
								<script>$(document).ready(function () {
									<?php if(isset($_GET["jahr"])){ ?>
									ladeDaten('jahr', <?php echo (isset($_GET["sortBy"]) ? "\"".$_GET["sortBy"]."\"":"\"\"").", ".(isset($_GET["sortOrder"]) ? "\"".$_GET["sortOrder"]."\"":"\"\"").", \"".$_GET["jahr"]."\""; ?>);
									<?php }else if(isset($_GET["monat"])){ ?>
									ladeDaten('monat', <?php echo (isset($_GET["sortBy"]) ? "\"".$_GET["sortBy"]."\"":"\"\"").", ".(isset($_GET["sortOrder"]) ? "\"".$_GET["sortOrder"]."\"":"\"\"").", \"".$_GET["monat"]."\""; ?>);
									<?php }else if(isset($_GET["zeit"])){ ?>
									ladeDaten('zeit', <?php echo (isset($_GET["sortBy"]) ? "\"".$_GET["sortBy"]."\"":"\"\"").", ".(isset($_GET["sortOrder"]) ? "\"".$_GET["sortOrder"]."\"":"\"\"").", \"".$_GET["zeit"]."\""; ?>);
									<?php }else if(isset($_GET["monatjahr"])){ ?>
									ladeDaten('monatjahr', <?php echo (isset($_GET["sortBy"]) ? "\"".$_GET["sortBy"]."\"":"\"\"").", ".(isset($_GET["sortOrder"]) ? "\"".$_GET["sortOrder"]."\"":"\"\"").", \"".$_GET["monatjahr"]."\""; ?>);
									<?php }else{
										echo "alert('Keinen Modus gewählt');";
									} ?>
								});</script>
								<a href="?">Zurück</a>
								<div class="panel panel-green" style="overflow-x: hidden;">
									<div class="panel-heading">
										<h4><i class="fa fa-bar-chart"></i> Einsatzauswertung</h4>
									</div>
									<div class="panel-body">
										<div id="einsaetze" data-jahr="">
											<table class="table table-hover table-striped">
												<thead>
													<tr>
														<th data-column="nr" data-sort="DESC">Nr.</th>
														<th data-column="datum" data-sort>Datum</th>
														<th data-column="zeit" data-sort>Zeit</th>
														<th data-column="name" data-sort>Name</th>
														<th data-column="anwesende" data-sort>Anwesende Mitglieder</th>
														<th data-column="dauer" data-sort>Dauer</th>
													</tr>
												</thead>
												<tbody>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php } ?>
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
