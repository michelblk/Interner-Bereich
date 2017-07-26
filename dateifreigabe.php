<?php require("include/php/auth.php"); require("include/php/db.php");
$basefolder = "include/php/userdata/filesharing/";

function checkfolder($folder) {
	global $basefolder;
	if(is_dir($basefolder.$folder) && basename($folder) == ".."){ //.. nicht erlaubt (am Ende)
		echo "Nicht erlaubt";
		exit();
	}

	$f = explode("/", $folder); // .. als Ordnername nicht erlaubt
	foreach($f as $tmp) {
		if ($tmp == "..") {
			echo "Nicht erlaubt";
			exit();
		}
	}

}

function getIconByContentType ($ct) {
	if(substr($ct, 0, 6) == "video/"){ //Video
		return "file-video-o";
		exit;
	}else
	if(substr($ct, 0, 6) == "audio/"){ //Audio
		return "file-audio-o";
		exit;
	}else
	if(substr($ct, 0, 6) == "image/"){ //Bild
		return "file-image-o";
		exit;
	}else
	if($ct == "text/html" || $ct == "text/php") {
		return "file-code-o";
		exit;
	}else
	if(substr($ct, 0, 5) == "text/"){ //Plain text
		return "file-text-o";
		exit;
	}else
	if(substr($ct, 0, 39) == "application/vnd.oasis.opendocument.text") { //ODT
		return "file-word-o";
		exit;
	}else
	if(substr($ct, 0, 46) == "application/vnd.oasis.opendocument.spreadsheet") { //ODS
		return "file-excel-o";
		exit;
	}else
	if(substr($ct, 0, 47) == "application/vnd.oasis.opendocument.presentation") { //ODP
		return "file-powerpoint-o";
		exit;
	}else
	if($ct == "application/msword" || substr($ct, 0, 62) == "application/vnd.openxmlformats-officedocument.wordprocessingml") { // DOC / DOCX
		return "file-word-o";
		exit;
	}else
	if(substr($ct, 0, 29) == "application/vnd.ms-powerpoint" || substr($ct, 0, 60) == "application/vnd.openxmlformats-officedocument.presentationml") { // Powerpoint
		return "file-powerpoint-o";
		exit;
	}else
	if(substr($ct, 0, 59) == "application/vnd.openxmlformats-officedocument.spreadsheetml") { //Excel
		return "file-excel-o";
		exit;
	}else
	if($ct == "application/pdf"){ //PDF
		return "file-pdf-o";
		exit;
	}else
	if($ct == "application/zip") { //zip
		return "file-archive-o";
		exit;
	}else
	if(substr($ct, 0, 11) == "application") { // Andre Anwendungen
		return "file-code-o";
		exit;
	}else{
		return "file-o";
		exit;
	}

}

if(isset($_GET["file"]) && $_GET["file"] != "") {

	$file = $_GET["file"]; //! nicht urldecode benutzen
	checkfolder($file);
	$mainfolder = explode("/", $file)[0];
	$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".$mainfolder."' LIMIT 1");
	if($ordner->num_rows == 1) {
		$path = ($ordner->fetch_object()->ordner_id).substr($file, strlen($mainfolder), strlen($file));
		if(!file_exists($basefolder.$path)) {
			http_response_code(404);
			echo "Datei nicht gefunden";
			exit();
		}
		header("Content-Type: ".mime_content_type($basefolder.$path));
		header("Content-Disposition:".(!isset($_GET["download"]) ? "inline":"attachment")."; filename=\"".pathinfo($file)["basename"]."\"");
		header("Content-Length: ".filesize($basefolder.$path));
		header("Cache-Control:no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Content-Security-Policy:script-src 'none'");
		echo file_get_contents($basefolder.$path);
		exit();
	}else{
		echo "Keine Berechtigung";
		exit();
	}

}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Dateiablage</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="include/js/jQuery.js"></script>
	<script src="include/js/main.js"></script>
	<script src="include/js/bootstrap.min.js"></script>
	<script src="include/js/dateifreigabe.js"></script>
	<link href="include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="include/css/sb-admin.min.css" rel="stylesheet" />
	<link href="include/css/font-awesome.min.css" rel="stylesheet" />
	<link href="include/css/main.css" rel="stylesheet" />
	<link href="include/css/dateifreigabe.css" rel="stylesheet" />
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
					<li class="active">
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
							Dateiablage
						</h1>
						<ol class="breadcrumb">
							<li><a href="?folder">Menü</a></li>
							<?php
							if(isset($_GET["folder"]) && $_GET["folder"] != ""){
								$path = explode("/", $_GET["folder"]);
								$breadcrumbs = "";
								foreach($path as $key => $breadcrumb) {
									$breadcrumbs .= $breadcrumb."/";
									if($breadcrumb != "" && $breadcrumb != "/")echo "<li><a href=\"?folder=$breadcrumbs\">".$breadcrumb."</a></li>\n";
								}
							}
							?>
						</ol>
					</div>
				</div>
				<!-- /.row -->
				<main data-ordner="<?php if(isset($_GET["folder"])) echo $_GET["folder"]; ?>">
					<div class="col-lg-12">
						<?php
						if((!isset($_GET["folder"]) || $_GET["folder"] == "" || $_GET["folder"] == "/")) { //nicht gewählt
							$editRight = check_right(5); //Hauptordner erstellen
							?>
							<div id="hauptordner" class="row">
								<?php
								$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner`.`ordner_id`, `int__dateifreigabe-ordner`.`name`, `int__dateifreigabe-ordner-gruppen`.`schreiben` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."'");
								while($ord = $ordner->fetch_array(MYSQLI_ASSOC)) {
									echo "<a href=\"?folder=".urlencode($ord["name"])."\"><div class=\"ordner\" data-id=\"".$ord["ordner_id"]."\"><i class=\"fa fa-folder-o fa-2x ordner-icon\" aria-hidden=\"true\"></i><div class=\"ordner-name\">".$ord["name"]."</div></div></a>\n";
								}
								?>
							</div>
							<ul class="dropdown-menu contextmenu" id="contextmenu-space"  data-type="space">
								<?php if($editRight){ ?><li><a  href="javascript:void(0);" onclick="neuerOrdner();"><i class="fa fa-fw fa-folder-o"></i> Neuer Ordner</a></li> <?php }else{echo "<li>- keine Aktionen verfügbar -</li>";} ?>
							</ul>
							<ul class="dropdown-menu contextmenu" id="contextmenu-folder" data-type="ordner">
								<?php if($editRight){ ?><li><a href="javascript:void(0);" onclick="neuerOrdner();"><i class="fa fa-fw fa-folder-o"></i> Neuer Ordner</a></li><?php } ?>
								<li><a href="javascript:void(0);" onclick="ordnerHerunterladen($(this));"><i class="fa fa-fw fa-download"></i> Ordner herunterladen</a></li>
								<?php if($editRight){ ?><li><a href="javascript:void(0);" onclick="umbenennen($(this));"><i class="fa fa-fw fa-pencil"></i> Umbenennen</a></li>
								<li><a href="javascript:void(0);" onclick="ordnerLoeschen();"><i class="fa fa-fw fa-trash-o"></i> Ordner löschen</a></li>
								<li><a href="javascript:void(0);" onclick="ordnerEigenschaften($(this));"><i class="fa fa-fw fa-list"></i> Eigenschaften</a></li><?php } ?>
							</ul>
							<?php }else if(isset($_GET["folder"]) && $_GET["folder"] != "" && $_GET["folder"] != "/"){ //unterordner
							$editRight = check_right(5); //Hauptordner bearbeiten
							$folder = rtrim($_GET["folder"], '/') . '/'; //! nicht urldecode benutzen
							checkfolder($folder);
							$mainfolder = explode("/", $folder)[0];
							$ordner = $mysqli->query("SELECT `int__dateifreigabe-ordner-gruppen`.`schreiben`, `int__dateifreigabe-ordner`.`ordner_id` FROM `int__dateifreigabe-ordner`, `int__dateifreigabe-ordner-gruppen`, `int__benutzer-gruppen` WHERE `int__dateifreigabe-ordner`.`ordner_id` = `int__dateifreigabe-ordner-gruppen`.`ordner_id` AND `int__dateifreigabe-ordner-gruppen`.`gruppen_id` = `int__benutzer-gruppen`.`gruppen_id` AND `int__benutzer-gruppen`.`user_id` = '".$_SESSION["userid"]."' AND `int__dateifreigabe-ordner`.`name` = '".$mainfolder."' LIMIT 1");
							if($ordner->num_rows == 1) {
								$ordner = $ordner->fetch_array(MYSQLI_ASSOC);
								$rw = $ordner["schreiben"]; //boolean
								if($editRight)$rw = 1;
								$path = $ordner["ordner_id"].substr($folder, strlen($mainfolder), strlen($folder));
								if(!is_dir($basefolder.$path)) {
									http_response_code(404);
									echo "Ordner nicht gefunden";
									exit();
								}
								$ordner = scandir($basefolder.$path);
								$outFolder = array();
								$outFiles = array();
								if(count($ordner) == 2) { //Ordner leer (".", "..")
									echo "<p>Dieser Ordner ist leer.</p>";
									if($rw)echo "<p><button class=\"btn btn-outline btn-danger\" onclick=\"dateiHochladen();\">Dateien hochladen</button>
									<button class=\"btn btn-outline btn-danger\" onclick=\"neuerOrdner();\">Ordner erstellen</button></p>";
								}else{ //Ordner nicht leer
									foreach($ordner as $ordfile) {
										if(is_dir($basefolder.$path."/".$ordfile)) {
											if($ordfile != "." && $ordfile != "..")
											$outFolder[] = $ordfile;
										}else{
											$tmp = array();
											$tmp["name"] = $ordfile;
											$tmp["type"] = mime_content_type($basefolder.$path.$ordfile);
											$outFiles[] = $tmp;
										}
									}
								}

								//Ordner ausgeben
								echo "<div class=\"row\">";
								foreach($outFolder as $newFolder) {
									echo "<a href=\"?folder=".urlencode($folder.$newFolder)."\"><div class=\"ordner\"><i class=\"fa fa-folder-o fa-2x ordner-icon\" aria-hidden=\"true\"></i><div class=\"ordner-name\">".$newFolder."</div></div></a>\n";
								}
								echo "</div>";

								//Dateien ausgeben
								echo "<div class=\"row\">";
								foreach($outFiles as $newFile) {
									echo "<!-- ".$newFile["type"]." -->";
									$fa = getIconByContentType($newFile["type"]);
									echo "<a href=\"?file=".urlencode($folder.$newFile["name"])."\" target=\"_blank\"><div class=\"datei\" data-type=\"".$newFile["type"]."\"><i class=\"datei-icon fa fa-$fa fa-2x\"></i><div class=\"datei-name\">".$newFile["name"]."</div></div></a>";
								}
								echo "</div>";
								?>
								<ul class="dropdown-menu contextmenu" id="contextmenu-space" data-type="space">
									<?php if($rw){?><li><a href="javascript:void(0);" onclick="neuerOrdner();"><i class="fa fa-fw fa-folder-o"></i> Neuer Ordner</a></li>
									<li><a  href="javascript:void(0);" onclick="dateiHochladen();"><i class="fa fa-fw fa-files-o"></i> Dateien hochladen</a></li><?php } ?>
									<li><a href="javascript:void(0);" onclick="ordnerHerunterladen();"><i class="fa fa-fw fa-download"></i> Ordner herunterladen</a></li>
								</ul>
								<ul class="dropdown-menu contextmenu" id="contextmenu-folder" data-type="ordner">
									<?php if($rw){ ?><li><a  href="javascript:void(0);" onclick="neuerOrdner();"><i class="fa fa-fw fa-folder-o"></i> Neuer Ordner</a></li>
									<li><a  href="javascript:void(0);" onclick="dateiHochladen();"><i class="fa fa-fw fa-files-o"></i> Dateien hochladen</a></li><?php } ?>
									<li><a href="javascript:void(0);" onclick="ordnerHerunterladen($(this));"><i class="fa fa-fw fa-download"></i> Ordner herunterladen</a></li>
									<?php if($rw){?><li><a href="javascript:void(0);" onclick="umbenennen($(this));"><i class="fa fa-fw fa-pencil"></i> Umbenennen</a></li>
									<li><a  href="javascript:void(0);" onclick="ordnerLoeschen();"><i class="fa fa-fw fa-trash-o"></i> Ordner löschen</a></li><?php } ?>
									<li><a href="javascript:void(0);" onclick="ordnerEigenschaften($(this));"><i class="fa fa-fw fa-list"></i> Eigenschaften</a></li>
								</ul>
								<ul class="dropdown-menu contextmenu" id="contextmenu-file" data-type="datei">
									<?php if($rw){ ?><li><a  href="javascript:void(0);" onclick="neuerOrdner();"><i class="fa fa-fw fa-folder-o"></i> Neuer Ordner</a></li>
									<li><a  href="javascript:void(0);" onclick="dateiHochladen();"><i class="fa fa-fw fa-files-o"></i> Dateien hochladen</a></li><?php } ?>
									<li><a href="javascript:void(0);" onclick="dateiHerunterladen();"><i class="fa fa-fw fa-download"></i> Datei herunterladen</a></li>
									<li><a href="javascript:void(0);" onclick="ordnerHerunterladen();"><i class="fa fa-fw fa-download"></i> Ordner herunterladen</a></li>
									<?php if($rw){ ?><li><a href="javascript:void(0);" onclick="umbenennen($(this));"><i class="fa fa-fw fa-pencil"></i> Umbenennen</a></li>
									<li><a href="javascript:void(0);" onclick="dateiLoeschen();"><i class="fa fa-fw fa-trash-o"></i> Datei löschen</a></li><?php } ?>
									<li><a href="javascript:void(0);" onclick="dateiEigenschaften($(this));"><i class="fa fa-fw fa-list"></i> Eigenschaften</a></li>
								</ul>

								<?php
							}else{
								echo "Keine Berechtigung.";
							}
						}else{
							echo "Seite nicht gefunden";
						}?>
					</div>
					<div class="clearfix"></div>
				</main>
				<div class="overlay" id="promptOverlay">
					<div class="prompt-container">
						<div class="prompt col-sm-3">
							<div id="neuerOrdner" class="prompt-content">
								<h3>
									Ordner erstellen
								</h3>
								<div class="col-lg-12">
									<form method="POST" action="#" role="form">
										<div class="row">
											<input type="text" placeholder="Ordnername" name="name" class="form-control" required autofocus />
											<input type="hidden" class="hidden" value="ordner" name="type" />
											<input type="hidden" class="hidden" value="" name="id" />
										</div>
										<div class="row">
											<?php if((!isset($_GET["folder"]) || $_GET["folder"] == "/" || $_GET["folder"] == "") && $editRight){ ?>
											<div class="dropdown" style="float: left;" id="gruppenauswahl">
												<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Gruppen
												<span class="caret"></span></button>
												<ul class="dropdown-menu" style="max-height: 200px; overflow-y: scroll;">
													<?php
													// Gruppen abrufen
													if(!check_right(6))
													{ //darf nur zu Gruppen, in denen man Mitglied ist, einen Ordner hinzufügen
														$query = $mysqli->query("SELECT `int__benutzer-gruppen`.`gruppen_id`, `Beschreibung` FROM `int__benutzer-gruppen`, `int__gruppen` WHERE `int__benutzer-gruppen`.`gruppen_id` = `int__gruppen`.`gruppen_id` AND `user_id` = '".$_SESSION["userid"]."' ORDER BY `Beschreibung` ASC");
													}else{ //darf zu allen Gruppen Ordner hinzufügen
														$query = $mysqli->query("SELECT `gruppen_id`, `Beschreibung` FROM `int__gruppen` ORDER BY `Beschreibung` ASC");
													}
													$gruppen = array();
													while($gruppe = $query->fetch_array(MYSQLI_ASSOC))
													{
														$tmp = array();
														$tmp["id"] = $gruppe["gruppen_id"];
														$tmp["name"] = $gruppe["Beschreibung"];
														$gruppen[] = $tmp;
													}

													//Gruppen ausgeben
													echo "<table class=\"table table-hover table-striped\">";
													echo "<tr>
														<th>Gruppe</th>
														<th>Lesen</th>
														<th>Schreiben</th>
													</tr>";
													foreach ($gruppen as $gruppe) {
														echo "<tr>
															<td>".$gruppe["name"]."</td>
															<td class=\"checkbox\"><label><input type=\"checkbox\" name=\"gruppe[".$gruppe["id"]."]\" value=\"r\"><span class=\"cr\"><i class=\"cr-icon fa fa-check\"></i></span></label></td>
															<td class=\"checkbox\"><label><input type=\"checkbox\" name=\"gruppe[".$gruppe["id"]."]\" value=\"rw\"><span class=\"cr\"><i class=\"cr-icon fa fa-check\"></i></span></label></td>
														</tr>\n";
													}
													echo "</table>";
													?>
												</ul>
											</div>
											<?php } ?>
											<input type="submit" class="btn btn-danger pull-right" value="Erstellen" />
											<input type="reset" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Abbrechen" />
										</div>
									</form>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="ordnerLoeschen" class="prompt-content">
								<h3>
									Ordner löschen?
								</h3>
								<p class="help-block">Achtung: Die Dateien werden dauerhaft gelöscht!</p>
								<div class="col-lg-12">
									<form method="POST" action="#" role="form">
										<div class="row">
											<input type="text" placeholder="Ordnername" name="name" class="form-control" required readonly />
											<input type="hidden" class="hidden" value="ordner" name="type" />
											<input type="hidden" class="hidden" value="" name="id" />
										</div>
										<div class="row">
											<input type="submit" class="btn btn-danger pull-right" value="Löschen" autofocus />
											<input type="reset" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Abbrechen" />
										</div>
									</form>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="dateiLoeschen" class="prompt-content">
								<h3>
									Datei löschen?
								</h3>
								<p class="help-block">Achtung: Die Datei wird dauerhaft gelöscht!</p>
								<div class="col-lg-12">
									<form method="POST" action="#" role="form">
										<div class="row">
											<input type="text" placeholder="Ordnername" name="name" class="form-control" required readonly />
											<input type="hidden" class="hidden" value="file" name="type" />
											<input type="hidden" class="hidden" value="" name="id" />
										</div>
										<div class="row">
											<input type="submit" class="btn btn-danger pull-right" value="Löschen" autofocus />
											<input type="reset" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Abbrechen" />
										</div>
									</form>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="umbenennen" class="prompt-content">
								<h3>
									Umbenennen
								</h3>
								<div class="col-lg-12">
									<form method="POST" action="#" role="form">
										<div class="row">
											<input type="text" placeholder="Ordnername" name="neuerName" class="form-control" required autofocus />
											<input type="hidden" class="hidden" value="" name="alterName" required />
											<input type="hidden" class="hidden" value="" name="type" required />
											<input type="hidden" class="hidden" value="" name="id" />
										</div>
										<div class="row">
											<input type="submit" class="btn btn-danger pull-right" value="Umbenennen" />
											<input type="reset" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Abbrechen" />
										</div>
									</form>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="dateiHochladen" class="prompt-content">
								<h3>
									Hochladen
								</h3>
								<div class="col-lg-12">
									<form method="POST" action="#" role="form">
										<div class="row">
											<div class="input-group">
												<label class="input-group-btn">
													<span class="btn btn-primary btn-outline">
														Datei auswählen <input type="file" name="dateien[]" required autofocus multiple style="display: none;" />
													</span>
												</label>
												<input type="text" class="form-control" readonly >
											</div>
											<p class="help-block" style="font-size: 0.8em;">Maximal 20 Dateien oder <?php echo (ini_get("upload_max_filesize") > ini_get("post_max_size") ? ini_get("upload_max_filesize") : ini_get("post_max_size")); ?></p>
											<input type="hidden" class="hidden" value="dateien" name="type" required />
											<input type="hidden" class="hidden" value="" name="id" />
										</div>
										<div id="dropzone">
											<!-- angedacht -->
										</div>
										<div class="row">
											<div class="progress">
												<div class="progress-bar" role="progressbar">
													0%
												</div>
											</div>
											<input type="submit" class="btn btn-danger pull-right" value="Hochladen" />
											<input type="reset" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Abbrechen" />
										</div>
									</form>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="ordnerEigenschaften" class="prompt-content">
								<h3>
									Eigenschaften
								</h3>
								<div class="col-lg-12">
									<div class="row">
										<div class="table-responsive">
											<table class="table table-hover table-bordered">
												<tbody>
													<tr>
														<td>Name</td>
														<td data-info="name"><span>---</span></td>
													</tr>
													<tr>
														<td>Inhalt</td>
														<td data-info="inhalt"><span>---</span></td>
													</tr>
													<tr>
														<td>Größe</td>
														<td data-info="groesse"><span>---</span></td>
													</tr>
													<tr>
														<td>Leserechte</td>
														<td data-info="leserechte"><span>---</span></td>
													</tr>
													<tr>
														<td>Schreibrechte</td>
														<td data-info="schreibrechte"><span>---</span></td>
													</tr>
												</tbody>
											</table>
										</div>
										<input type="hidden" class="hidden" value="" name="id" />
									</div>
									<div class="row">
										<input type="button" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Schließen" />
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="dateiEigenschaften" class="prompt-content">
								<h3>
									Eigenschaften
								</h3>
								<div class="col-lg-12">
									<div class="row">
										<div class="table-responsive">
											<table class="table table-hover table-bordered">
												<tbody>
													<tr>
														<td>Name</td>
														<td data-info="name"><span>---</span></td>
													</tr>
													<tr>
														<td>Letzte Änderung</td>
														<td data-info="zeit"><span>---</span></td>
													</tr>
													<tr>
														<td>Größe</td>
														<td data-info="groesse"><span>---</span></td>
													</tr>
													<tr>
														<td>Leserechte</td>
														<td data-info="leserechte"><span>---</span></td>
													</tr>
													<tr>
														<td>Schreibrechte</td>
														<td data-info="schreibrechte"><span>---</span></td>
													</tr>
												</tbody>
											</table>
										</div>
										<input type="hidden" class="hidden" value="" name="id" />
									</div>
									<div class="row">
										<input type="button" class="btn btn-default pull-right" onclick="schliessePrompt();" value="Schließen" />
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->
</body>
</html>
