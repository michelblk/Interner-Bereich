<?php require("include/php/auth.php"); require("include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Dienstplan</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="include/js/jQuery.js"></script>
	<script src="include/js/main.js"></script>
	<script src="include/js/bootstrap.min.js"></script>
	<script src="include/js/dienstplan.js"></script>
	<link href="include/css/morris-chart.css" rel="stylesheet" />
	<link href="include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="include/css/sb-admin.min.css" rel="stylesheet" />
	<link href="include/css/font-awesome.min.css" rel="stylesheet" />
	<link href="include/css/main.css" rel="stylesheet" />
	<link href="include/css/dienstplan.css" rel="stylesheet" />
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
					<li class="active">
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
							Dienstplan
						</h1>
					</div>
				</div>
				<main>
					<div class="row">
						<div class="col-lg-12">
							<button type="button" class="btn btn-outline btn-primary" onclick="$(this).hide();zeigeAlle();" style="margin-bottom: 5px;">Alte Einträge anzeigen</button>
							<div class="table-responsive nomoretables">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>Datum</th>
											<th>Kategorie</th>
											<th>Thema</th>
											<th>Verantwortliche(/-r)</th>
											<th>Zugf.</th>
											<th>HTLF</th>
											<th>LF10</th>
											<th>LF20</th>
											<th>GWL</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$wait = (24 * 60 * 60); //1 Tag
										$jetzt = time();
										$query = $mysqli->query("SELECT `id`, `datum`, `monat`, `thema`, `verantwortlich`, `kategorie`, `zf`, `htlf`, `lf10`, `lf20`, `gwl`, `datum2` FROM `dienstplan` ORDER BY `datum` ASC");
										while($dienst = $query->fetch_array(MYSQLI_ASSOC)) {
											echo "
											<tr data-id=\"".$dienst["id"]."\" data-show=\"".(($dienst["datum2"] != NULL ? strtotime($dienst["datum2"]):strtotime($dienst["datum"])) < ($jetzt-$wait) ? "0":"1")."\">
												<td data-title=\"Datum\" data-empty=\"".($dienst["datum"] == "" ? "1":"0")."\">".date( 'd.m.Y H:i', strtotime($dienst["datum"])).($dienst["datum2"] != NULL ? " - ".date( 'd.m.Y', strtotime($dienst["datum2"])) : "")."</td>
												<td data-title=\"Kategorie\" data-empty=\"".($dienst["kategorie"] == "" ? "1":"0")."\">".$dienst["kategorie"]."</td>
												<td data-title=\"Thema\" data-empty=\"".($dienst["thema"] == "" ? "1":"0")."\">".$dienst["thema"]."</td>
												<td data-title=\"Verantwortliche(/-r)\" data-empty=\"".($dienst["verantwortlich"] == "" ? "1":"0")."\">".$dienst["verantwortlich"]."</td>
												<td data-title=\"Zugf.\" data-empty=\"".($dienst["zf"] == "" ? "1":"0")."\">".$dienst["zf"]."</td>
												<td data-title=\"HTLF\" data-empty=\"".($dienst["htlf"] == "" ? "1":"0")."\">".$dienst["htlf"]."</td>
												<td data-title=\"LF10\" data-empty=\"".($dienst["lf10"] == "" ? "1":"0")."\">".$dienst["lf10"]."</td>
												<td data-title=\"LF20\" data-empty=\"".($dienst["lf20"] == "" ? "1":"0")."\">".$dienst["lf20"]."</td>
												<td data-title=\"GWL\" data-empty=\"".($dienst["gwl"] == "" ? "1":"0")."\">".$dienst["gwl"]."</td>
											</tr>\n";
										}
										?>
									</tbody>
								</table>
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
