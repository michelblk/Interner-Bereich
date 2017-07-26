<?php require("include/php/auth.php"); require("include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Lehrgangsliste</title>
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
	<link href="include/css/lehrgangsliste.css" rel="stylesheet" />
</head>
<body style="padding-bottom: 0px;">
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
					<li class="active">
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
		<!-- /#page-wrapper -->
			<div class="container-fluid">
				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">
							Lehrgangsliste
						</h1>
					</div>
				</div>
				<main>
					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive" id="ausbildungsliste">
								<table class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>Name</th>
											<?php
											$query = $mysqli->query("SELECT `lehrgang_id`, `name`, `abkuerzung`, `icon` FROM `int__lehrgaenge` WHERE `zusammenfassen` = '0' ORDER BY `int__lehrgaenge`.`reihenfolge` ASC, `int__lehrgaenge`.`name` ASC");
											while ($lehrgang = $query->fetch_array(MYSQLI_ASSOC)) {
												echo "<th data-id=\"".$lehrgang["lehrgang_id"]."\">".($lehrgang["abkuerzung"] != "" ? $lehrgang["abkuerzung"] : $lehrgang["name"])."</th>";
											}
											?>
											<th data-id="-1">Weitere Lehrgänge</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$query = $mysqli->query("SELECT `user_id`, `Vorname`, `Nachname` FROM `int__benutzer` ORDER BY `Nachname` ASC, `Vorname` ASC");
										while ($benutzer = $query->fetch_array(MYSQLI_ASSOC)) {
											$zusammenfassen = array();
											echo "<tr data-userid=\"".$benutzer["user_id"]."\">";
											echo "<td>".$benutzer["Nachname"].", ".$benutzer["Vorname"]."</td>";
											$lehrgaenge = $mysqli->query("SELECT `int__lehrgaenge`.`lehrgang_id`, `icon`, `name`, `zusammenfassen`, (SELECT count(*) FROM `int__benutzer-lehrgaenge` WHERE `int__benutzer-lehrgaenge`.`user_id` = '".$benutzer["user_id"]."' AND `int__lehrgaenge`.`lehrgang_id` = `int__benutzer-lehrgaenge`.`lehrgang_id` ORDER BY `int__lehrgaenge`.`reihenfolge` ASC, `int__lehrgaenge`.`name` ASC) as 'state' FROM `int__lehrgaenge`");
											while($lehrgang = $lehrgaenge->fetch_array(MYSQLI_ASSOC)) {
												if($lehrgang["zusammenfassen"]=="0"){
													echo "<td data-zusammengefasst=\"0\">";
													echo "<i data-state=\"".$lehrgang["state"]."\" class=\"icon fa fa-".($lehrgang["icon"] != "" ? $lehrgang["icon"]:"handshake-o")."\"  data-toggle=\"tooltip\" title=\"".$lehrgang["name"]."\"></i>";
													echo "</td>";
												}else{ //zusammenfassen
													$tmp = array();
													$tmp["id"] = $lehrgang["lehrgang_id"];
													$tmp["state"] = $lehrgang["state"];
													$tmp["icon"] = $lehrgang["icon"];
													$tmp["name"] = $lehrgang["name"];
													$zusammenfassen[] = $tmp;
												}
											}
											echo "<td data-zusammengefasst=\"1\">";
											foreach($zusammenfassen as $lehrgang) {
												if($lehrgang["state"] != "0"){
													echo "<i data-state=\"".$lehrgang["state"]."\" class=\"icon fa fa-".($lehrgang["icon"] != "" ? $lehrgang["icon"]:"handshake-o")."\" data-toggle=\"tooltip\" title=\"".$lehrgang["name"]."\"></i>\n";
												}
											}
											echo "</td>";
											echo "</tr>\n";
										}
										?>
									</tbody>
								</table>
								<script>$("#ausbildungsliste tbody td[data-zusammengefasst] i").tooltip();</script>
							</div>
						</div>
					</div>
				</main>
			</div>
		</div>
	<!-- /#wrapper -->
</body>
</html>
