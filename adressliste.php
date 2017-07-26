<?php require("include/php/auth.php"); require("include/php/db.php");
function sortierung($name) {
	if((isset($_GET["sortBy"]) && $_GET["sortBy"] == $name) || ($name == "nachname" && (!isset($_GET["sortBy"]) || $_GET["sortBy"] == ""))){
		if(isset($_GET["sortOrder"]) && ($_GET["sortOrder"] == "DESC" || $_GET["sortOrder"] == "ASC")){
			return "='".$_GET["sortOrder"]."'";
		}else{
			return "='ASC'";
		}
	}else{
		return "";
	}
}?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Adressliste</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="include/js/jQuery.js"></script>
	<script src="include/js/main.js"></script>
	<script src="include/js/bootstrap.min.js"></script>
	<link href="include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="include/css/sb-admin.min.css" rel="stylesheet" />
	<link href="include/css/font-awesome.min.css" rel="stylesheet" />
	<link href="include/css/main.css" rel="stylesheet" />
	<link href="include/css/adressliste.css" rel="stylesheet" />
	<script src="include/js/adressliste.js"></script>
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
					<li class="active">
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
					<li>
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
							Adressbuch
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<div class="col-lg-6">
						<div class="panel panel-default">
							<div class="panel-body" id="users">
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th data-column="nachname" data-sort<?php echo sortierung("nachname"); ?>>Nachname</th>
											<th data-column="vorname" data-sort<?php echo sortierung("vorname"); ?>>Vorname</th>
											<th data-column="gruppe" data-sort<?php echo sortierung("gruppe"); ?>>Gruppe</th></tr>
									</thead>
									<tbody>
										<?php
										//Sortierung
										$richtung = "ASC";
										if(isset($_GET["sortOrder"]) && $_GET["sortOrder"] != ""){
											if($_GET["sortOrder"] == "ASC"){$richtung="ASC";}
											else if($_GET["sortOrder"] == "DESC") {$richtung="DESC";}
											else {$richtung.="DESC";}
										}
										$order = "`Nachname` $richtung, `Vorname` $richtung";
										if(isset($_GET["sortBy"]) && $_GET["sortBy"] != ""){
											if($_GET["sortBy"] == "nachname"){$order="`Nachname` $richtung, `Vorname` $richtung";}
											else if($_GET["sortBy"] == "vorname"){$order="`Vorname` $richtung, `Nachname` $richtung";}//nicht `datum`, weil falsches format
											else if($_GET["sortBy"] == "gruppe"){$order="`Gruppe` $richtung, `Nachname` $richtung, `Vorname` $richtung";}
											else {
												$order="`Nachname` $richtung, `Vorname` $richtung";
											}
										}

										$users = $mysqli->query("SELECT `int__benutzer`.`user_id`, `int__benutzer`.`Nachname`, `int__benutzer`.`Vorname`, (SELECT `int__gruppen`.`Beschreibung` FROM `int__gruppen`, `int__benutzer-gruppen` WHERE `int__gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer`.`user_id` = `int__benutzer-gruppen`.`user_id` ORDER BY `int__gruppen`.`Prioritaet` DESC, `int__gruppen`.`Beschreibung` ASC LIMIT 1) as 'Gruppe' FROM `int__benutzer` ORDER BY $order"); // Wichtigste Gruppe ausgeben
										/* SELECT `int__benutzer`.`user_id`, `int__benutzer`.`Nachname`, `int__benutzer`.`Vorname`, GROUP_CONCAT(`int__benutzer-gruppen`.`gruppen_id` ORDER BY `int__gruppen`.`Beschreibung`) as GruppenIDs, GROUP_CONCAT(`int__gruppen`.`Beschreibung` ORDER BY `int__gruppen`.`Beschreibung`) as Gruppen
											FROM `int__benutzer`
											LEFT JOIN `int__benutzer-gruppen` ON `int__benutzer`.`user_id` = `int__benutzer-gruppen`.`user_id`
											LEFT JOIN `int__gruppen` ON `int__benutzer-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id`
											GROUP BY `int__benutzer`.`user_id`
											ORDER BY `Nachname` ASC, `Vorname` ASC // Alle Gruppen ausgeben */
										while ($user = $users->fetch_array(MYSQLI_ASSOC)) {
											echo "<tr data-userid=\"".$user["user_id"]."\"><td data-info=\"nachname\">".$user["Nachname"]."</td><td data-info=\"vorname\">".$user["Vorname"]."</td><td data-info=\"gruppe\">".$user["Gruppe"]."</td></tr>\n";
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<div id="userSelectionNote">Bitte wähle einen Benutzer aus</div>
								<div id="userinfo">
									<table class="table table-hover table-striped">
										<thead>
											<tr><th colspan="3">Benutzerinformationen</th></tr>
										</thead>
										<tbody>
											<tr><td data-legend>Vorname</td><td data-info="vorname"></td><td rowspan="3" id="userImage"></td></tr>
											<tr><td data-legend>Nachname</td><td data-info="nachname"></td></tr>
											<tr><td data-legend>E-Mail</td><td data-info="email"></td></tr>
											<tr><td data-legend>Strasse</td><td data-info="strasse" colspan="2"></td></tr>
											<tr><td data-legend>Wohnort</td><td data-info="wohnort" colspan="2"></td></tr>
											<tr><td data-legend>Telefon</td><td data-info="telefon" colspan="2"></td></tr>
											<tr><td data-legend>Mobil</td><td data-info="mobil" colspan="2"></td></tr>
											<tr><td data-legend>Gruppen</td><td data-info="gruppen" colspan="2"></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</main>

			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->
</body>
</html>
