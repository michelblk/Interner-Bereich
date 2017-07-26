<?php require("include/php/auth.php"); require("include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern</title>
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
	<link href="include/css/dashboard.css" rel="stylesheet" />
	<link href="include/css/dashboard-calendar.css" rel="stylesheet" />
	<script src="include/js/dashboard-comments.js"></script>
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
					<li class="active">
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
							Dashboard
							<small>Interner Bereich</small>
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<?php $readwrite = check_right(1); ?>
				<main id="kalender">
					<iframe src="dashboard-calendar-action.php?calendar" style="border: 0" width="70%" height="600" frameborder="0" scrolling="no" id="kalenderframe" name="kalenderframe"></iframe>


					<div id="kommentare">
						<div id="kommentare-container" <?php if($readwrite != 1){echo "data-readonly=\"true\"";} ?>>
							<div id="kommentare-benutzer">

							</div>
							<a id="kommentare-lademehr" onclick="aeltereNachrichten();">Mehr laden</a>
						</div>
						<?php
						if($readwrite == 1)
						{
						?>
						<div id="kommentare-eingabe">
							<form action="#" method="POST" id="submit-form">
								<div class="form-group">
									<textarea class="form-control" rows="3" placeholder="Dein Kommentar"></textarea>
								</div>
								 <button type="submit" class="btn btn-primary fa fa-paper-plane-o"></button>
							</form>
						</div>
						<?php
						}
						?>
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
