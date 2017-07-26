<?php require("../include/php/auth.php"); require("../include/php/db.php");

if(!check_right(2))
{ //Admins only: Recht NR 2!
	http_response_code(401);
	echo "Keine Berechtigung!";
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern - Registrieren</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
	<script src="../include/js/bootstrap.min.js"></script>
	<link href="../include/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../include/css/sb-admin.min.css" rel="stylesheet" />
	<style>
	.nav>li {
		height: 50px;
		line-height: 30px;
		float: left;

	}
	.nav>.active {
		background-color: #FFFFFF;
	}
	.navbar-right {
		margin-right: 0px;
	}
	#page-wrapper {
		background-color: #FFFFFF;
		width: 80%;
		max-width: 600px;
		margin: 0px auto;
		margin-bottom: 20px;
	}
	form {
		padding: 20px;
	}
	</style>
	<script>
		jQuery(function ($) {
		    var $inputs = $('input[name=email],input[name=benutzername]');
		    $inputs.on('input', function () {
		        // Set the required property of the other input to false if this input is not empty.
		        $inputs.not(this).prop('required', !$(this).val().length);
		    });
		});
	</script>
</head>
<body>
	<!-- Navigation -->
	<nav id="mainNav" class="navbar static-top navbar-inverse bg-inverse">
		<a class="navbar-brand" href="#">Intern</a>
	</nav>

	<!-- Main -->
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="col-lg-12">
				<h1 class="page-header">Registrieren</h1>
			</div>
		</div>
		<?php
		if(isset($_GET["err"])) {
			echo "<div class=\"alert alert-warning\">";
			if($_GET["err"] == "0") echo "Interner Fehler: Keine Verbindung zur Datenbank"; // Verbindungsfehler mit der DB
			else if($_GET["err"] == "1") echo "Ein Eintrag mit diesem Vor- und Nachnamen oder Benutzernamen oder E-Mail existiert bereits"; // Existiert bereits
			else if($_GET["err"] == "2") echo "Es wurden nicht alle notwendigen Felder ausgefült"; // Nicht alles ausgefuellt
			else echo "Es ist ein unbekannter Fehler aufgetreten"; // Unbekannt
			echo "</div>";
		}else if (isset($_GET['success'])) {
			echo "<div class=\"alert alert-success\">Erfolgreich registriert.</div>";
		}
		?>
		<form role="form" method="post" action='action.php?s=admin&register'>
			<div class="form-group">
				<label>Vorname</label>
				<input type="text" class="form-control" name="vorname" required />
			</div>
			<div class="form-group">
				<label>Nachname</label>
				<input type="text" class="form-control" name="nachname" required />
			</div>
			<div class="form-group">
				<label>Telefon</label><span> (optional)</span>
				<input type="text" class="form-control" name="telefon" pattern="\d*"/>
			</div>
			<div class="form-group">
				<label>Mobil</label><span> (optional)</span>
				<input type="text" class="form-control" name="mobil" pattern="\d*" />

			</div>
			<div class="form-group" style="clear: both;">
				<label>Straße und Hausnummer</label><span> (optional)</span>
				<input type="text" class="form-control" name="strasse" />
			</div>
			<div class="form-group" style="float: left; width: 25%; min-width: 7em;">
				<label>Postleitzahl</label>
				<input type="text" class="form-control" name="plz" pattern="\d*" />
			</div>
			<div style="float: left: width: 30px;"></div>
			<div class="form-group" style="float: right; width: calc(100% - 7em - 45px);">
				<label>Ort</label><span> (optional)</span>
				<input type="text" class="form-control" name="ort"/>
			</div>
			<div class="form-group" style="clear: both;">
				<label>E-Mail</label>
				<input type="email" class="form-control" name="email" required />
				<p class="help-block">Verwende eine E-Mail-Adresse die eindeutig zugeordnet werden kann.</p>
			</div>
			<div class="form-group">
				<label>Passwort</label>
				<input type="password" class="form-control" name="passwort" required />
			</div>
			<button type="submit" class="btn btn-default">Registrieren</button>
			<button type="reset" class="btn btn-default">Formular zurücksetzen</button>
		</div>
	</body>
	</html>
