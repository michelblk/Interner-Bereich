<?php require("../include/php/auth.php"); require("../include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Accounteinstellungen</title>
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
	<link href="../include/css/einstellungen-account.css" rel="stylesheet" />
	<script src="../include/js/einstellungen-account.js"></script>
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
							Accounteinstellungen
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<div id="meldungen">

					</div>
					<div class="row">
						<?php
						$query = $mysqli->query("SELECT `Vorname`, `Nachname`, `Strasse`, `Wohnort`, `PLZ`, `Telefon`, `Mobil`, `Mail` FROM `int__benutzer` WHERE `user_id` LIKE '".$_SESSION["userid"]."' LIMIT 1");
						$data = $query->fetch_array(MYSQLI_ASSOC);
						?>
						<div class="col-lg-4">
							<div class="panel panel-default" id="changePersonalData">
								<div class="panel-heading">Persönliche Daten</div>
								<div class="panel-body">
									<form action="#change-personalData" method="post" id="personalDataForm">
										<div class="form-group input-group">
											<span class="input-group-addon">Vorname</span>
											<input type="text" class="form-control" value="<?php echo $data["Vorname"] ?>" name="vorname">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">Nachname</span>
											<input type="text" class="form-control" value="<?php echo $data["Nachname"] ?>" name="nachname">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">Straße</span>
											<input type="text" class="form-control" value="<?php echo $data["Strasse"] ?>" name="strasse">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">Wohnort</span>
											<input type="text" class="form-control" value="<?php echo $data["Wohnort"] ?>" name="wohnort">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">PLZ</span>
											<input type="text" class="form-control" value="<?php echo $data["PLZ"] ?>" name="PLZ">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">Telefonnummer</span>
											<input type="text" class="form-control" value="<?php echo $data["Telefon"] ?>" name="telefon">
										</div>
										<div class="form-group input-group">
											<span class="input-group-addon">Mobilfunknummer</span>
											<input type="text" class="form-control" value="<?php echo $data["Mobil"] ?>" name="mobil">
										</div>
										<button type="submit" class="btn btn-outline btn-primary">Daten ändern</button>
									</form>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="panel panel-default">
								<div class="panel-heading">E-Mail</div>
								<div class="panel-body">
									<form action="#change-email" method="post" id="emailForm">
										<div class="form-group">
											<label class="control-label">Aktuelle E-Mail</label>
											<input type="email" class="form-control" value="<?php echo $data["Mail"] ?>" readonly name="alteMail">
										</div>
										<div class="form-group">
											<label class="control-label">Neue E-Mail</label>
											<input type="email" class="form-control" value="" name="neueMail" required name="neueMail">
										</div>
										<div class="form-group">
											<label class="control-label">Neue E-Mail wiederholen</label>
											<input type="email" class="form-control" value="" name="neueMail2" required name="neueMail2">
										</div>
										<button type="submit" class="btn btn-outline btn-primary">E-Mail ändern</button>
									</form>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="panel panel-default">
								<div class="panel-heading">Passwort</div>
								<div class="panel-body">
									<form action="#change-password" method="post" id="passwordform">
										<div class="form-group" id="altesPasswort">
											<label class="control-label">Altes Passwort</label>
											<input type="password" class="form-control" value="" name="oldpw" required>
										</div>
										<div class="form-group" id="neuesPasswort">
											<label class="control-label">Neues Passwort</label>
											<input type="password" class="form-control" value="" name="newpw" required>
										</div>
										<div class="form-group" id="neuesPasswort2">
											<label class="control-label">Neues Passwort wiederholen</label>
											<input type="password" class="form-control" value="" name="newpw2" required>
										</div>
										<button type="submit" class="btn btn-outline btn-primary">Passwort ändern</button>
									</form>
								</div>
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
