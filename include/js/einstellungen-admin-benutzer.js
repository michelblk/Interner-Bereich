/* Lehrgänge */

var lastScrollPosition = 0;

$(document).ready(function () {
	// Bootstrap Erweiterung
	$("#benutzerBearbeiten input[type='file']").on('change', function() { // Input[type='file']
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

		$(this).parents('.input-group').find(':text').val(numFiles+" Datei: "+label);
	});

	//Funktionen


	$("#benutzer tbody tr").on("click", function (e) {
		switchMode(1);
		var user = $(this).attr('data-userid');
		$("#benutzerBearbeiten button[type='submit']").prop('disabled', true);
		$("#benutzerBearbeiten td[data-info]:not([data-info='gruppen']):not([data-info='bild']):not([data-info='lehrgaenge']):not([data-info='pwzurueckDeaktiv']) input").val("").prop('disabled', true);
		$("#benutzerBearbeiten td[data-info='gruppen'] .checkbox, #benutzerBearbeiten td[data-info='lehrgaenge'] .checkbox").hide();
		$("#benutzerBearbeiten td[data-info='gruppen'] input, #benutzerBearbeiten td[data-info='lehrgaenge'] input").prop('checked', false).prop('disabled', true);
		$("#benutzerBearbeiten td[data-info='pwzurueckDeaktiv'] input").prop('checked', false); //nicht deaktivieren, damit auch unangeklickt übertragen wird
		$("#benutzerBearbeiten").attr("data-userid", user);

		$.ajax({
			url: "get.php?getUserData="+user,
			method: "GET",
			success: function (data) {
				try {
					data = $.parseJSON(data);
				}
				catch (e) { // error

				}
				finally{ //wenn JSON
					$("#benutzerBearbeiten td[data-info='email'] input").val(data["Mail"]);
					$("#benutzerBearbeiten td[data-info='vorname'] input").val(data["Vorname"]);
					$("#benutzerBearbeiten td[data-info='nachname'] input").val(data["Nachname"]);
					$("#benutzerBearbeiten td[data-info='strasse'] input").val(data["Strasse"]);
					$("#benutzerBearbeiten td[data-info='wohnort'] input").val(data["Wohnort"]);
					$("#benutzerBearbeiten td[data-info='PLZ'] input").val(data["PLZ"]);
					$("#benutzerBearbeiten td[data-info='telefon'] input").val(data["Telefon"]);
					$("#benutzerBearbeiten td[data-info='mobil'] input").val(data["Mobil"]);
					$("#benutzerBearbeiten td[data-info='pwzurueckDeaktiv'] input[type='checkbox']").prop('checked', (data["pwzurueckDeaktiv"] != 0 ? true:false));
					$("#benutzerBearbeiten div[data-info='bild']").css("background-image", "url('../adressliste-action.php?getUserImage&user="+user+"')");

					$.each(data["gruppen"], function (index, value) {
						$("#benutzerBearbeiten td[data-info='gruppen'] input[value='"+value["id"]+"']").prop('checked', true);
						$("#benutzerBearbeiten td[data-info='gruppen'] .checkbox").has("input[value='"+value["id"]+"']").show();
					});

					$.each(data["lehrgaenge"], function (index, value) {
						$("#benutzerBearbeiten td[data-info='lehrgaenge'] input[value='"+value+"']").prop('checked', true);
						$("#benutzerBearbeiten td[data-info='lehrgaenge'] .checkbox").has("input[value='"+value+"']").show();
					});
				}
			},
			error: function (xhr, status, text) {
				if(xhr.status == 401) {
					alert("Es sieht so aus, als wurde dir die Berechtigung für diese Seite entzogen.");
				}else
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			},
			complete: function () {

			}
		});

	});

	$(".backtoSelectUser").on("click", function (e) {
		e.preventDefault();
		switchMode(0);
	});

	function switchMode (mode) {
		if(mode == 0) {
			$("#benutzerBearbeiten").fadeOut(200, function () {
				$("#benutzer").fadeIn(200, function () {
					$(window).scrollTop(lastScrollPosition); //zurück zur Position
				});
			});
		}else if (mode == 1) {
			lastScrollPosition = $(window).scrollTop();
			$("#benutzer").fadeOut(200, function () {
				$("#meldungen").html(""); //Meldungen leeren
				$("#benutzerBearbeiten").fadeIn(200, function () {
					$(window).scrollTop($(".panel").has("#benutzerBearbeiten").position().top); //zum Panel scrollen
				});
			});
		}
	}

	$("#benutzerBearbeiten td[data-info]").click(function (e) {
		if($("#benutzerBearbeiten button[type='submit']").prop('disabled'))$("#benutzerBearbeiten button[type='submit']").prop('disabled', false);

		var target = $(this).attr("data-info");
		if(target == "gruppen") {
			$("#benutzerBearbeiten td[data-info='gruppen'] .checkbox").show();
			$("#benutzerBearbeiten td[data-info='gruppen'] input").prop('disabled', false);
		}else
		if(target == "lehrgaenge") {
			$("#benutzerBearbeiten td[data-info='lehrgaenge'] .checkbox").show();
			$("#benutzerBearbeiten td[data-info='lehrgaenge'] input").prop('disabled', false);
		}else{
			$(this).children("input").prop('disabled', false);
		}
	});

	$('#benutzerBearbeitenForm').bind("keypress", function(e) {
		if (e.keyCode == 13) {
			e.preventDefault(); // Enter submit nicht annehmen
			return false;
		}
	});

	$("#benutzerBearbeitenForm").submit(function (e) {
		if($("#benutzerBearbeiten button[type='submit']").prop('disabled') == true){return;}
		$("#benutzerBearbeiten button[type='submit']").prop('disabled', true);
		e.preventDefault();

		$.ajax({
			url: "action.php?s=admin&updateUser="+$("#benutzerBearbeiten").attr('data-userid'),
			method: "POST",
			data: new FormData($(this)[0]),
			success: function (data) {
				$("#meldungen").prepend("<div class=\"alert alert-success alert-dismissible fade show in\">Benutzerdaten erfolgreich aktualisiert<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Schliessen\"><span aria-hidden=\"true\">&times;</span></button></div>");
			},
			error: function (xhr, optionen, text) {
				$("#meldungen").prepend("<div class=\"alert alert-warning alert-dismissible fade show in\">Benutzer konnte nicht aktualisiert werden ("+xhr.status+": "+text+")<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Schliessen\"><span aria-hidden=\"true\">&times;</span></button></div>");
			},
			cache: false,
        	contentType: false,
			processData: false
		});
	});

	$("#deleteUserButton").click(function () {
		var userid = $("#benutzerBearbeiten").attr("data-userid");
		if(confirm("Benutzer (ID "+userid+") wirklich löschen? Dieser Vorgang kann nicht rückgängig gemacht werden. Eine Nachricht wird an Marvin Bielefeld gesendet.")){
			$.ajax({
				url: "action.php?s=admin&deleteUser="+userid,
				method: "POST",
				data: {"userid": userid},
				success: function () {
					location.reload(); //Seite neu laden -> Benutzer neu laden
				},
				error: function (xhr, optionen, text) {
					$("#meldungen").prepend("<div class=\"alert alert-danger alert-dismissible fade show in\">Benutzer konnte nicht gelöscht werden ("+xhr.status+": "+text+")<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Schliessen\"><span aria-hidden=\"true\">&times;</span></button></div>");
				}
			})
		}
	});
});
