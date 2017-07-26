<?php require("../include/php/auth.php"); require("../include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - FAQ</title>
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
	<link href="../include/css/einstellungen-faq.css" rel="stylesheet" />
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
							Häufig gestellte Fragen
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<?php
						$faqs = $mysqli->query("SELECT `faq_id`, `frage`, `antwort` FROM `int__faq` ORDER BY `prioritaet` DESC, `faq_id` ASC");
						while($faq = $faqs->fetch_array(MYSQLI_ASSOC)) {
							echo "<div class=\"row\" data-faqid=\"".$faq["faq_id"]."\">
								<div class=\"col-md-12\">
									<div class=\"panel panel-default\">
										<div class=\"panel-heading\"><a data-toggle=\"collapse\" href=\"#faq".$faq["faq_id"]."\">".$faq["frage"]."</a></div>
										<div class=\"panel-collapse collapse\" id=\"faq".$faq["faq_id"]."\">
											<div class=\"panel-body\">".$faq["antwort"]."</div>
										</div>
									</div>
								</div>
							</div>";
						}
					?>
				</main>

			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->
</body>
</html>
