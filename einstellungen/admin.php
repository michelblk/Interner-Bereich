<?php require("../include/php/auth.php"); require("../include/php/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Administration</title>
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
	<link href="../include/css/einstellungen-admin.css" rel="stylesheet" />
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
							Administration
						</h1>
					</div>
				</div>
				<!-- /.row -->
				<main>
					<?php if(!isset($_GET["users"]) && !isset($_GET["groups"]) && !isset($_GET["lehrgaenge"]) && !isset($_GET["ordner"]) && !isset($_GET["faq"])) { //nicht gewählt ?>
						<?php if(check_right(2)){ // Darf Benutzer und Gruppen bearbeiten ?>
						<a href="index.php">Zurück</a>
						<div class="row">
							<div class="col-lg-6 col-md-6">
								<a href="?users">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-id-card-o fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Benutzer</div>
												</div>
											</div>
										</div>

										<div class="panel-footer">
											<span class="pull-left">Benutzer bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>

									</div>
								</a>
							</div>
							<div class="col-lg-6 col-md-6">
								<a href="?groups">
									<div class="panel panel-green">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-users fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Gruppen</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">Gruppen bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</a>
							</div>
						</div>
						<?php } ?>
						<div class="row">
							<?php if(check_right(9)){ // Darf Lehrgänge bearbeiten ?>
							<div class="col-lg-6 col-md-6">
								<a href="?lehrgaenge">
									<div class="panel panel-red">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-address-card fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Lehrgänge</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">Lehrgänge bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</a>
							</div>
							<?php } if(check_right(6)) { // Darf Ordner verwalten ?>
							<div class="col-lg-6 col-md-6">
								<a href="?ordner">
									<div class="panel panel-yellow">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-folder-o fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">Ordner</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">Ordner bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</a>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php if(check_right(8)) { // Darf das FAQ verwalten ?>
							<div class="col-lg-6 col-md-6">
								<a href="?faq">
									<div class="panel panel-default">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-question-circle-o fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right text-middle">
													<div class="huge">FAQ</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">"Häufig gestellte Fragen" bearbeiten</span>
											<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</a>
							</div>
							<?php } ?>
						</div>
					<?php }else
					if(isset($_GET["users"]) && check_right(2)){ // Benutzer verwalten ?>
						<a href="?">Zurück</a>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4><i class="fa fa-id-card-o"></i> Benutzerverwaltung</h4>
							</div>
							<div class="panel-body">
								<div id="benutzer">
									Wähle einen Benutzer oder <a href="register.php" target="_blank">registriere einen neuen Benutzer</a>.
									<table class="table table-hover table-striped">
										<thead>
											<tr><th>Bild</th><th>Nachname</th><th>Vorname</th></tr>
										</thead>
										<tbody>
											<?php
											$users = $mysqli->query("SELECT `int__benutzer`.`user_id`, `int__benutzer`.`Nachname`, `int__benutzer`.`Vorname` FROM `int__benutzer` ORDER BY `Nachname` ASC, `Vorname` ASC");
											while ($user = $users->fetch_array(MYSQLI_ASSOC)) {
												echo "<tr data-userid=\"".$user["user_id"]."\"><td data-info=\"image\" style=\"background-image: url('../adressliste-action.php?getUserImage&user=".$user["user_id"]."');\"></td><td data-info=\"nachname\">".$user["Nachname"]."</td><td data-info=\"vorname\">".$user["Vorname"]."</td></tr>\n";
											}
											?>
										</tbody>
									</table>
								</div>
								<div id="benutzerBearbeiten">
									<a href="#" class="backtoSelectUser">Zurück und anderen Benutzer wählen</a>
									<form action="#" method="POST" id="benutzerBearbeitenForm">
										<table class="table table-hover table-striped">
											<thead>
												<tr><th colspan="2">Benutzerinformationen</th></tr>
											</thead>
											<tbody>
												<tr><td>Vorname</td><td data-info="vorname"><input class="input-control" type="text" name="vorname" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Nachname</td><td data-info="nachname"><input class="input-control" type="text" name="nachname" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>E-Mail</td><td data-info="email"><input class="input-control" type="text" name="email" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Passwort</td><td data-info="passwort"><input class="input-control" type="text" name="passwort" placeholder="Neues Passwort" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Strasse</td><td data-info="strasse"><input class="input-control" type="text" name="strasse" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Wohnort</td><td data-info="wohnort"><input class="input-control" type="text" name="wohnort" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>PLZ</td><td data-info="PLZ"><input class="input-control" type="text" name="PLZ" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Telefon</td><td data-info="telefon"><input class="input-control" type="text" name="telefon" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Mobil</td><td data-info="mobil"><input class="input-control" type="text" name="mobil" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr>
													<td>Passwort zurücksetzen</td>
													<td data-info="pwzurueckDeaktiv">
														<div class="checkbox"><label><input type="checkbox" name="pwzurueckDeaktiv" value="1" />Funktion deaktivieren</label></div>
													</td>
												</tr>
												<tr>
													<td>Gruppen</td>
													<td data-info="gruppen">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<?php
															$groups = $mysqli->query("SELECT `gruppen_id`, `Beschreibung` FROM `int__gruppen` ORDER BY `Beschreibung` ASC");
															while($group = $groups->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"gruppen[]\" value=\"".$group["gruppen_id"]."\" /> ".$group["Beschreibung"]."</label></div>\n";
															}
															?>
															<div style="display: none; height: 0px; width: 0px"><div class="checkbox"><label><input type="text" name="gruppen[]" value="-1" /></label></div></div>
														</div>
													</td>
												</tr>
												<tr>
													<td>Lehrgänge</td>
													<td data-info="lehrgaenge">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<?php
															$lehrgaenge = $mysqli->query("SELECT `lehrgang_id`, `name`, `icon` FROM `int__lehrgaenge` ORDER BY `reihenfolge` ASC, `name` ASC");
															while($lehrgang = $lehrgaenge->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"lehrgaenge[]\" value=\"".$lehrgang["lehrgang_id"]."\" /> <i class=\"fa fa-".$lehrgang["icon"]."\"></i> ".$lehrgang["name"]."</label></div>\n";
															}
															?>
															<div style="display: none; height: 0px; width: 0px"><div class="checkbox"><label><input type="text" name="lehrgaenge[]" value="-1" /></label></div></div>
														</div>
													</td>
												</tr>
												<tr><td>Bild</td>
													<td data-info="bild">
														<div class="input-group">
															<label class="input-group-btn">
																<span class="btn btn-primary btn-outline">
																	Datei auswählen <input class="input-control" type="file" accept="image/jpg,image/jpeg" name="bild" style="display: none;" />
																</span>
															</label>
															<input type="text" class="form-control" readonly style="width: 100%; max-width: 150px;" />
														</div>
														<div data-info="bild"></div>
													</td>
												</tr>
												<tr><td>Account</td><td><button type="button" class="btn btn-outline btn-danger btn-xs" id="deleteUserButton">Nutzer löschen</button></td></tr>
											</tbody>
										</table>
										<div id="meldungen">

										</div>
										<button type="submit" class="btn btn-outline btn-primary" disabled>Daten ändern</button>

										<p><a href="#" class="backtoSelectUser">Zurück und anderen Benutzer wählen</a></p>
									</form>
								</div>
							</div>
						</div>
						<script src="../include/js/einstellungen-admin-benutzer.js"></script>
					<?php }else
					if(isset($_GET["groups"]) && check_right(2)){ // Gruppen verwalten ?>
						<script src="../include/js/einstellungen-admin-gruppen.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-green">
							<div class="panel-heading">
								<h4><i class="fa fa-users"></i> Gruppenverwaltung</h4>
							</div>
							<div class="panel-body">
								<div id="gruppen">
									Wähle eine Gruppe oder <a id="createGroup">erstelle eine neue Gruppe</a>.
									<table class="table table-hover table-striped">
										<thead>
											<tr><th data-column="priority" data-sort>Priorität</th><th data-column="name" data-sort="ASC">Gruppenname</th><th data-column="members" data-sort>Anzahl Mitglieder</th></tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<script>$(document).ready(function () {reloadGroups("","");});</script> <!-- Lade Gruppen -->
								</div>
								<div id="gruppeBearbeiten">
									<a href="#" class="backtoSelectGroup">Zurück und andere Gruppe wählen</a>
									<form action="#" method="POST" id="gruppeBearbeitenForm">
										<table class="table table-hover table-striped">
											<thead>
												<tr><th colspan="2">Gruppeninformationen und Zugehörigkeiten</th></tr>
											</thead>
											<tbody>
												<tr><td>Gruppenname</td><td data-info="name"><input class="input-control" type="text" name="name" required /><i class="fa fa-fw fa-pencil"></i><p class="help-block">Der Name sollte möglichst kurz sein</p></td></tr>
												<tr><td>Priorität</td><td data-info="prioritaet"><input class="input-control" type="number" name="prioritaet" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Rechte</td>
													<td data-info="rechte">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<?php
															$rights = $mysqli->query("SELECT `rechtnr`, `Beschreibung` FROM `int__rechte` ORDER BY `Beschreibung` ASC");
															while($right = $rights->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"rechte[]\" value=\"".$right["rechtnr"]."\" /> ".$right["Beschreibung"]."</label></div>\n";
															}
															?>
															<div style="display: none; height: 0px; width: 0px;"><div class="checkbox"><label><input type="text" name="rechte[]" value="-1" /></label></div></div>
														</div>
													</td>
												</tr>
												<tr><td>Mitglieder</td>
													<td data-info="mitglieder">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<?php
															$users = $mysqli->query("SELECT `user_id`, `Vorname`, `Nachname` FROM `int__benutzer` ORDER BY `Nachname` ASC, `Vorname` ASC");
															while($user = $users->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox-userselect\"><label><input type=\"checkbox\" name=\"mitglieder[]\" value=\"".$user["user_id"]."\" /><div style=\"background-image: url('../adressliste-action.php?getUserImage&user=".$user["user_id"]."');\" data-image></div><span>".$user["Nachname"].", ".$user["Vorname"]."</span></label></div>\n";
															}
															?>
														</div>
													</td>
												</tr>
												<tr><td>Gruppe</td><td><button type="button" class="btn btn-outline btn-danger btn-xs" id="deleteGroupButton">Gruppe löschen</button></td></tr>
											</tbody>
										</table>
										<button type="submit" class="btn btn-outline btn-primary" disabled>Daten ändern</button>

										<p><a href="#" class="backtoSelectGroup">Zurück und andere Gruppe wählen</a></p>
									</form>
								</div>
							</div>
						</div>
					<?php }else
					if(isset($_GET["lehrgaenge"]) && check_right(9)){ // Lehrgänge ?>
						<script src="../include/js/einstellungen-admin-lehrgaenge.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-red">
							<div class="panel-heading">
								<h4><i class="fa fa-address-card"></i> Lehrgänge verwalten</h4>
							</div>
							<div class="panel-body">
								<div id="lehrgaenge">
									<button type="button" class="btn btn-outline btn-primary" onclick="neuerLehrgang();">Erstelle einen Lehrgangseintrag</button>
									<table class="table table-hover table-striped">
										<thead>
											<tr><th class="thin">Folge</th><th>Name</th><th>Abkürzung</th><th>Icon</th></tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<script>$(document).ready(function () {loadCourses();});</script> <!-- Lade Gruppen -->
								</div>
								<div id="lehrgangBearbeiten">
									<a href="#" class="backtoSelectCourse">Zurück und anderen Lehrgang wählen</a>
									<form action="#" method="POST" id="lehrgangBearbeitenForm">
										<table class="table table-hover table-striped">
											<tbody>
												<tr><td>ID</td><td data-info="id" class="donotclear"><input type="number" readonly class="input-control" name="id"></td></tr>
												<tr><td>Name</td><td data-info="name"><input class="input-control" type="text" name="name" required /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Abkürzung</td><td data-info="abkuerzung"><input class="input-control" type="text" name="abkuerzung" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Icon</td><td data-info="icon"><input class="input-control" type="text" name="icon" /><i class="fa fa-fw fa-pencil"></i><p class="help-block">Name des Icons von <a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a> (Ver. 4.7)</p></td></tr>
												<tr><td>Reihenfolge</td><td data-info="reihenfolge"><input class="input-control" type="number" name="reihenfolge" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Mitglieder</td>
													<td data-info="mitglieder">
														<i class="fa fa-fw fa-pencil"></i>
														<div class="form-group">
															<div style="display: none; height: 0px; width: 0px"><div class="checkbox-userselect"><label><input type="text" name="mitglieder[]" value="-1" /></label></div></div>
															<?php
															$users = $mysqli->query("SELECT `user_id`, `Vorname`, `Nachname` FROM `int__benutzer` ORDER BY `Nachname` ASC, `Vorname` ASC");
															while($user = $users->fetch_array(MYSQLI_ASSOC)) {
																echo "<div class=\"checkbox-userselect\"><label><input type=\"checkbox\" name=\"mitglieder[]\" value=\"".$user["user_id"]."\" /><div style=\"background-image: url('../adressliste-action.php?getUserImage&user=".$user["user_id"]."');\" data-image></div><span>".$user["Nachname"].", ".$user["Vorname"]."</span></label></div>\n";
															}
															?>
														</div>
													</td>
												</tr>
												<tr><td>Lehrgang</td><td><button type="button" class="btn btn-outline btn-danger btn-xs" onclick="loescheLehrgang();">Lehrgang löschen</button></td></tr>
											</tbody>
										</table>
										<button type="submit" class="btn btn-outline btn-primary" disabled>Daten ändern</button>

										<p><a href="#" class="backtoSelectCourse">Zurück und anderen Lehrgang wählen</a></p>
									</form>
								</div>
							</div>
						</div>
					<?php }else
					if(isset($_GET["ordner"]) && check_right(6)){ ?>
						<script src="../include/js/einstellungen-admin-ordner.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-yellow">
							<div class="panel-heading">
								<h4><i class="fa fa-folder-o"></i> Ordner verwalten</h4>
							</div>
							<div class="panel-body">
								- geplant -
							</div>
						</div>
					<?php } else
					if(isset($_GET["faq"]) && check_right(8)){ ?>
						<script src="../include/js/einstellungen-admin-faq.js"></script>
						<a href="?">Zurück</a>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><i class="fa fa-question-circle-o"></i> "Häufig gestellte Fragen" verwalten</h4>
							</div>
							<div class="panel-body">
								<div id="FAQ">
									<button type="button" class="btn btn-outline btn-primary" onclick="neueAntwort();">Erstelle eine neue Antwort</button>
									<table class="table table-hover table-striped">
										<thead>
											<tr><th class="thin">Folge</th><th>Frage</th><th>Antwort</th></tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<script>$(document).ready(function () {ladeFAQ();});</script>
								</div>
								<div id="FAQbearbeiten">
									<a href="javascript:void(0);" onclick="back();" class="back">Zurück und andere Frage wählen</a>
									<form action="#" method="POST" id="bearbeiten">
										<table class="table table-hover table-striped">
											<tbody>
												<tr><td>ID</td><td data-info="id" class="donotclear"><input type="number" readonly class="input-control" name="id"></td></tr>
												<tr><td>Folge</td><td data-info="folge"><input type="number" class="input-control" name="folge" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Frage</td><td data-info="name"><input class="input-control" type="text" name="frage" required /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Antwort</td><td data-info="abkuerzung"><input class="input-control" type="text" name="abkuerzung" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Reihenfolge</td><td data-info="reihenfolge"><input class="input-control" type="number" name="reihenfolge" /><i class="fa fa-fw fa-pencil"></i></td></tr>
												<tr><td>Frage</td><td><button type="button" class="btn btn-outline btn-danger btn-xs" onclick="loeschen();">Frage löschen</button></td></tr>
											</tbody>
										</table>
										<button type="submit" class="btn btn-outline btn-primary" disabled>Daten ändern</button>

										<p><a href="javascript:void(0);" onclick="back();" class="back">Zurück und andere Frage wählen</a></p>
									</form>
								</div>
							</div>
						</div>
					<?php }else { //Seite nicht gefunden oder keine Berechtigung ?>
						Seite nicht gefunden oder keine Berechtigung.
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
