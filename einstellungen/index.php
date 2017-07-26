<?php require("../include/php/auth.php"); require("../include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Einstellungen</title>
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
	<link href="../include/css/einstellungen.css" rel="stylesheet" />
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
							Einstellungen
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<div class="row">
						<div class="col-lg-6 col-md-6">
							<a href="account.php">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-id-card-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge">Account</div>
												<div>E-Mail oder Passwort ändern</div>
											</div>
										</div>
									</div>

									<div class="panel-footer">
										<span class="pull-left">Accounteinstellungen</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>

								</div>
							</a>
						</div>
						<div class="col-lg-6 col-md-6">
							<a href="faq.php">
								<div class="panel panel-red">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-question-circle-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge">Hilfe</div>
												<div>Häufig gefragte Fragen</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<span class="pull-left">FAQ ansehen</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</div>
							</a>
						</div>
					</div>

					<div class="row">
					<?php;
					if(check_right(2) || check_right(6) || check_right(8) || check_right(9))
					{ ?>
						<div class="col-lg-6 col-md-6">
							<a href="admin.php">
								<div class="panel panel-yellow">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-address-card-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge">Admin</div>
												<div>Benutzer verwalten</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<span class="pull-left">Benutzerverwaltung starten</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</div>
							</a>
						</div>
					<?php }
					if(check_right(3) || check_right(7))
					{ ?>
						<div class="col-lg-6 col-md-6">
							<a href="website.php">
								<div class="panel panel-green">
									<div class="panel-heading">
										<div class="row">
											<div class="col-xs-3">
												<i class="fa fa-address-card-o fa-5x"></i>
											</div>
											<div class="col-xs-9 text-right">
												<div class="huge">Website</div>
												<div>Website und Einsätze verwalten</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<span class="pull-left">Websiteverwaltung starten</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</div>
							</a>
						</div>
					<?php } ?>
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
