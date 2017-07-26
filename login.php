<?php

if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
	header("LOCATION: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	echo "Bitte zu HTTPS wechseln";
	exit();
}

session_start();
if(isset($_SESSION['userid'])){ //check ob bereits angemeldet
	header("LOCATION: index.php");
	echo "Sie sind bereits angemeldet.";
	exit;
}
// nur für ausgeloggte Benutzer
?>
<!DOCTYPE html>
<html>
<head>
	<title>Intern</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.red-blue.min.css" />
	<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
	<script src="include/js/login.js"></script>
	<link rel="stylesheet" type="text/css" href="include/css/login-dialog-polyfill.css" />

	<!--Main Stylesheet -->
	<link rel="stylesheet" href="include/css/login.css" />

</head>
<body>
	<dialog id="loginDialog" class="mdl-dialog">
		<h4 class="mdl-dialog__title">Anmelden</h4>
		<div class="mdl-dialog__content">
			<form action="login-action.php?login" method="post" id="logininput">
				<p id="loginError"></p>
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="email" id="loginEmail" name="email">
					<label class="mdl-textfield__label" for="email">E-Mail</label>
				</div>
				<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					<input class="mdl-textfield__input" type="password" id="loginPassword" name="password">
					<label class="mdl-textfield__label" for="password">Passwort</label>
				</div>
			</div>
			<div class="mdl-dialog__actions">
				<!-- Colored raised button -->
				<button id="loginBtn" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">
					Anmelden
				</button>
				<!-- MDL Spinner Component -->
				<button type="button" id="forgotPassword" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">Passwort vergessen</button>
				<!--<div id="loginProgress" class="mdl-spinner mdl-js-spinner is-active"></div>-->
			</div>
		</form>
	</dialog>
	<script src="include/js/login-dialog-polyfill.js"></script>
	<script>
	var dialog = document.querySelector('dialog');
	dialogPolyfill.registerDialog(dialog);
	// Now dialog acts like a native <dialog>.
	dialog.showModal();
	</script>

	<div class="login-cover">
		<!-- MDL Spinner Component -->
		<div class="page-loader mdl-spinner mdl-js-spinner is-active"></div>
	</div>
</body>
</html>
