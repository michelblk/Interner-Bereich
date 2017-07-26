var lastScrollPosition = 0;

/* ---------------------- Einsätze bearbeiten -------------------------- */

$(document).ready(function () {

	$("#einsaetze").on("click", "tbody tr[data-einsatzid]", function (e) {
		var einsatz = $(this).attr('data-einsatzid');
		switchMode(1);

		ladeEinsatz(einsatz);

	});

	$(".backtoSelectOperation").on("click", function (e) {
		e.preventDefault();
		switchMode(0);
	});


	function ladeEinsatz (einsatz) {
		neuEinsatzBearbeiten();
		$("#einsatzBearbeiten td[data-info='id'] input").val(einsatz);
		$("#einsatzBearbeiten").attr("data-einsatzid", einsatz);
		$("#einsatzBearbeiten").attr({"data-type": "update"});

		$.ajax({
			url: "get.php?getOperationData&operation="+einsatz,
			method: "GET",
			success: function (data) {
				try {
					data = $.parseJSON(data);
				}
				catch (e) { // error

				}
				finally{ //wenn JSON
					$("#einsatzBearbeiten td[data-info='id'] input").val(data["id"]);
					$("#einsatzBearbeiten td[data-info='titel'] input").val(data["titel"]);
					$("#einsatzBearbeiten td[data-info='art'] input").val(data["art"]);
					$("#einsatzBearbeiten td[data-info='ort'] input").val(data["ort"]);
					$("#einsatzBearbeiten td[data-info='num'] input[name='num']").val(data["num"]);
					$("#einsatzBearbeiten td[data-info='num'] input[name='assNum']").val(data["assNum"]);
					$("#einsatzBearbeiten td[data-info='zeit'] input[name='beginn']").val(data["beginn"]);
					$("#einsatzBearbeiten td[data-info='zeit'] input[name='ende']").val(data["ende"]);
					$("#einsatzBearbeiten td[data-info='text'] textarea").val(data["text"]);
					$("#einsatzBearbeiten td[data-info='lightboxText'] textarea").val(data["lightboxText"]);

					$.each(data["fahrzeuge"], function (index, value) {
						$("#einsatzBearbeiten td[data-info='fahrzeuge'] input[value='"+value+"']").prop('checked', true);
					});
					$.each(data["anwesende"], function (index, value) { /* --------------------- verstecktes element fehlt (Löschanomalie) ------------------ */
						$("#einsatzBearbeiten td[data-info='anwesende'] input[value='"+value+"']").prop('checked', true);
						$("#einsatzBearbeiten td[data-info='anwesende'] .checkbox-anwesende").has("input[value='"+value+"']").show();
					});

					if(data["bilder"] > 0) {
						var i = 1;
						while(i <= data["bilder"]) {
							$("#einsatzBearbeiten td[data-info='bilder'] div[data-info='bilder']").append("<div class=\"checkbox-bilder\"><label><input type=\"checkbox\" name=\"existierendeBilder[]\" value=\""+i+"\" checked /><div style=\"background-image: url('get.php?getOperationImage="+einsatz+"&nr="+i+"');\" data-image></div><span>"+i+"</span></label></div>");
							i++;
						}
					}
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
	}


	$('#einsatzBearbeitenForm').bind("keypress", function(e) {
		if (e.keyCode == 13) {
			if(e.target.tagName.toLowerCase() != "textarea"){
				e.preventDefault(); // Enter submit nicht annehmen
				return false;
			}
		}
	});

	$("#einsatzBearbeitenForm").submit(function (e) { // ------ überprüfen ---------
		e.preventDefault();
		$.ajax({
			url: "action.php?s=website&updateOperation="+$("#einsatzBearbeiten").attr('data-einsatzid')+"&type="+$("#einsatzBearbeiten").attr("data-type"),
			method: "POST",
			data: new FormData($(this)[0]),
			success: function (data) {
				if($("#einsatzBearbeiten").attr("data-type") == "new"){
					$("#einsatzBearbeiten").attr("data-type", 'update');
					if(typeof(data) == "undefined" || typeof(data["id"]) == "undefined" || data["id"] == "") {
						alert("Etwas ist schief gelaufen");
					}else
					$("#einsatzBearbeiten").attr('data-einsatzid', data["id"]);
				} //id setzen und in den Bearbeiten Modus wechseln
				ladeEinsatz($("#einsatzBearbeiten").attr('data-einsatzid'));
			},
			error: function (xhr, optionen, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$("#einsatzBearbeiten td[data-info]").click(function (e) {
		if($("#einsatzBearbeiten button[type='submit']").prop('disabled'))$("#einsatzBearbeiten button[type='submit']").prop('disabled', false);

		var target = $(this).attr("data-info");
		if(target == "fahrzeuge") {
			$("#einsatzBearbeiten td[data-info='fahrzeuge'] .checkbox-fahrzeuge").show();
			$("#einsatzBearbeiten td[data-info='fahrzeuge'] input").prop('disabled', false);
		}else if(target == "anwesende") {
			$("#einsatzBearbeiten td[data-info='anwesende'] .checkbox-anwesende").show();
			$("#einsatzBearbeiten td[data-info='anwesende'] input").prop('disabled', false);
		}else if(target == "bilder") {
			$("#einsatzBearbeiten td[data-info='bilder'] .checkbox-bilder").show();
			$("#einsatzBearbeiten td[data-info='bilder'] input").prop('disabled', false);
		}else
		{
			$(this).children("input, textarea").prop('disabled', false);
		}
	});

	$("#einsatzBearbeiten input[type='file']").on('change', function() { // Input[type='file']
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

		$(this).parents('.input-group').find(':text').val(numFiles+(numFiles == 1 ? " Datei":" Dateien"));
	});

});


//Hilfsfunktionen
function switchMode(mode) {
	if(mode == 0) {
		$("#einsatzBearbeiten").fadeOut(200, function () {
			$("#einsaetze").fadeIn(200, function () {
				$(window).scrollTop(lastScrollPosition); //zurück zur Position
			});
		});
	}else if (mode == 1) {
		lastScrollPosition = $(window).scrollTop();
		$("#einsaetze").fadeOut(200, function () {
			$("#einsatzBearbeiten").fadeIn(200, function () {
				$(window).scrollTop($(".panel").has("#einsatzBearbeiten").position().top); //zum Panel scrollen
			});
		});
	}
}
function neuEinsatzBearbeiten () {
	$("#einsatzBearbeiten button[type='submit']").prop('disabled', true);
	$("#einsatzBearbeiten td[data-info]:not(.donotclear) input, #einsatzBearbeiten td[data-info]:not(.donotclear) textarea").val("").prop('disabled', true);
	$("#einsatzBearbeiten td[data-info='fahrzeuge'] input, #einsatzBearbeiten td[data-info='anwesende'] input, #einsatzBearbeiten td[data-info='bilder'] input").prop('checked', false).prop('disabled', true);
	$("#einsatzBearbeiten div[data-info='bilder']").html("");
	$("#einsatzBearbeiten td[data-info='bilder'] input[type='file']").val("").change(); //trigger handler
	$("#einsatzBearbeiten td[data-info='anwesende'] .checkbox-anwesende").hide(); //zunächst nicht anwesende Mitglieder ausblenden
	$("#einsatzBearbeiten td[data-info='id'] input").attr("placeholder", "").val("");
}


//Event

var offset = -1;
function loadOperations (mode) {
	if(mode == 0) {offset = -1;}
	$.ajax({
		url: "get.php?getOperations&o="+offset,
		method: "GET",
		beforeSend: function () {
			if(mode == 0)$("#einsaetze table tbody").animate({"opacity": 0}, 200);
			$("#einsaetze-lademehr").hide();
		},
		success: function (data)  {
			try {
				if(mode == 0)$("#einsaetze table tbody").html("");
				$.each(data["einsaetze"], function(index, einsatz) {
					$("#einsaetze table tbody").append("<tr data-einsatzid=\""+einsatz["id"]+"\"><td data-info=\"einsatzid\">"+einsatz["id"]+"</td><td data-info=\"num\">"+(einsatz["assNum"] == "" ? einsatz["num"]:einsatz["assNum"])+"</td><td data-info=\"date\">"+einsatz["date"]+"</td><td data-info=\"duration\">"+einsatz["dauer"]+"</td><td data-info=\"name\">"+einsatz["title"]+"</td></tr>\n");
				});
				if(mode == 0)$("#einsaetze table tbody").stop(true, false).animate({"opacity": 1}, 200);
				offset = data["offset"];
			}
			catch (e) { // error
				alert("Fehler beim Datenverarbeiten");
				return;
			}
			finally {
				$("#einsaetze-lademehr").show();
			}
		},
		error: function (xhr, status, text) {
			if(xhr.status == 401) {
				alert("Es sieht so aus, als wurde dir die Berechtigung für diese Seite entzogen.");
			}else
			alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
		}
	});
}

function neuerEinsatz () {
	switchMode(1);
	neuEinsatzBearbeiten();
	$("#einsatzBearbeiten td[data-info='id'] input").attr("placeholder", "automatisch vergeben");
	$("#einsatzBearbeiten").attr("data-einsatzid", "-1");
	$("#einsatzBearbeiten").attr({"data-type": "new"});

	$("#einsatzBearbeiten td[data-info]:not(.donotclear) input, #einsatzBearbeiten td[data-info]:not(.donotclear) textarea").prop('disabled', false);
}

function loescheEinsatz () {
	if($("#einsatzBearbeiten").attr("data-type") == "update" && $("#einsatzBearbeiten").attr("data-einsatzid") != "-1") { //nicht neuer Einsatz
		alert("Leider noch nicht verfügbar :/");
		var id = $("#einsatzBearbeiten").attr("data-einsatzid");
		$.ajax({
			url: "action.php?s=website&updateOperation="+id+"&type=delete",
			method: "POST",
			data: {"id": id},
			success: function () {
				switchMode(0);
			},
			error: function (xhr, status, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			}
		});
	}else { //neuer Einsatz
		switchMode(0); //zurück
	}
}
