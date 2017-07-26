$(document).ready(function () {
	$("#passwordform").submit(function (e) {
		e.preventDefault();
		var oldpw = $("#altesPasswort input").val();
		var newpw = $("#neuesPasswort input").val();
		var newpw2 = $("#neuesPasswort2 input").val();
		if(newpw != newpw2) {
			$("#neuesPasswort2").addClass("has-error");
			$("#neuesPasswort2 label").text("Passwort nicht identisch");
			return;
		}
		$.ajax({
			url: "action.php?s=account&changePassword",
			method: "POST",
			data: {"oldpw": oldpw, "newpw": newpw, "newpw2": newpw2},
			success: function () {
				$("#passwordform")[0].reset();
				neue_meldung(1, "Passwort erfolgreich geändert");
			},
			error: function () {
				neue_meldung(0, "Passwort konnte nicht geändert werden");
			}
		});
	});

	$("#personalDataForm").submit(function (e) {
		e.preventDefault();
		$.ajax({
			url: "action.php?s=account&changePersonalData",
			method: "POST",
			data: $("#personalDataForm").serialize(),
			success: function () {
				neue_meldung(1, "Persönliche Daten erfolgreich geändert!");
			},
			error: function () {
				neue_meldung(0, "Persönliche Daten konnten nicht geändert werden!")
			}
		});
	});

	$("#emailForm").submit(function (e) {
		e.preventDefault();
		var oldMail = $("#emailForm input[name='alteMail']").val();
		var newMail = $("#emailForm input[name='neueMail']").val();
		var newMail2 = $("#emailForm input[name='neueMail2']").val();
		if(newMail != newMail2) {
			$("#emailForm input[name='neueMail2']").parent().addClass("has-error");
			$("#emailForm input[name='neueMail2']").siblings("label").text("E-Mail nicht identisch");
			return;
		}
		$.ajax({
			url: "action.php?s=account&changeEMail",
			method: "POST",
			data: $("#emailForm").serialize(),
			success: function () {
				neue_meldung(1, "E-Mail erfolgreich geändert!");
				$("#emailForm input[name='alteMail']").val(newMail);
			},
			error: function () {
				neue_meldung(0, "E-Mail konnten nicht geändert werden!")
			}
		});
	});


	function neue_meldung (type, text) {
		if(type == 0) { //error
			$("#meldungen").prepend("<div class=\"alert alert-danger alert-dismissible fade show in\">"+text+"<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Schliessen\"><span aria-hidden=\"true\">&times;</span></button></div>");
		}else if (type == 1) { //success
			$("#meldungen").prepend("<div class=\"alert alert-success alert-dismissible fade show in\">"+text+"<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Schliessen\"><span aria-hidden=\"true\">&times;</span></button></div>");
		}
	}
});
