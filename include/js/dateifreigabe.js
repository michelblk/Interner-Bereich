$(document).ready(function () {
	$("main").contextmenu(function(e) {
		e.preventDefault();
		$(".contextmenu").hide();
		$("#contextmenu-space").attr('data-target', $("main").attr('data-ordner'));
		$("#contextmenu-space").css({
			display: "block",
			left: ((e.pageX + $("#contextmenu-space").width()) > $(window).width() ? ($(window).width() - $("#contextmenu-space").width() - 10) : e.pageX),
			top: e.pageY
		});
		return false;
	});


	$("main").on("contextmenu", ".ordner", function (e) {
		e.preventDefault();
		$(".contextmenu").hide();
		$("#contextmenu-folder").attr('data-target', $(this).children(".ordner-name").text());
		$("#contextmenu-folder").css({
			display: "block",
			left: ((e.pageX + $("#contextmenu-folder").width()) > $(window).width() ? ($(window).width() - $("#contextmenu-folder").width() - 10) : e.pageX),
			top: e.pageY
		});

		return false;
	});

	$("main").on("contextmenu", ".datei", function (e) {
		e.preventDefault();
		$(".contextmenu").hide();
		$("#contextmenu-file").attr('data-target', $(this).children(".datei-name").text());
		$("#contextmenu-file").css({
			display: "block",
			left: ((e.pageX + $("#contextmenu-file").width()) > $(window).width() ? ($(window).width() - $("#contextmenu-file").width() - 10) : e.pageX),
			top: e.pageY
		});

		return false;
	});

	$("#gruppenauswahl").on('click', 'table', function (e) {
		e.stopPropagation();
	});

	$("body").click(function (e) {
		$(".contextmenu").hide();
	});
});

function neuerOrdner () {
	var ordner = $("main").attr('data-ordner');
	zeigePrompt("neuerOrdner", ordner);
	$("#neuerOrdner input[name='name']").focus();
}
$(document).ready(function () {
	$("#neuerOrdner form").submit(function (e)  {
		e.preventDefault();

		if($("#neuerOrdner form input[type=checkbox]").length > 0) {
			if(!$("#neuerOrdner form input[type=checkbox]:checked").length){
				alert("Mindestens eine Gruppe auswählen");
				return false;
			}
		}

		$.ajax({
			url: "dateifreigabe-action.php?neu",
			method: "POST",
			data: $("#neuerOrdner form").serialize(),
			success: function (data) {
				location.reload();
			},
			error: function (xhr, status, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			}
		});
		return false;
	});
});


function dateiHochladen () {
	var ordner = $("main").attr('data-ordner');
	$("#dateiHochladen .progress").hide();
	zeigePrompt("dateiHochladen", ordner);
}
$(document).ready(function () {
	$("#dateiHochladen input[type='file']").on('change', function() {
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		$(this).parents('.input-group').find(':text').val(numFiles > 1 ? numFiles + ' Dateien gewählt' : label);
	});

	var uploadXhr = $.ajaxSettings.xhr();

	$("#dateiHochladen form").submit(function (e) {
		e.preventDefault();
		$.ajax({
			url: "dateifreigabe-action.php?upload",
			method: "POST",
			data: new FormData($(this)[0]),
			beforeSend: function () {
				$("#dateiHochladen input[type='submit']").prop('disabled', true);
				$("#dateiHochladen .progress-bar").removeClass('progress-bar-danger').css('width', '0%').text('0%');
				$("#dateiHochladen .progress").show();
			},
			xhr: function () {
				if(uploadXhr.upload) {
					uploadXhr.upload.addEventListener('progress',function (e) {
						if(e.lengthComputable) {
							var max = e.total;
							var current = e.loaded;

							var percentage = Math.round((current * 100)/max);
							$("#dateiHochladen .progress-bar").css('width', percentage+'%').text(percentage+'%');
							if(percentage >= 100) {
								$("#dateiHochladen .progress-bar").css('width', '100%').text("Fertig!");
							}
						}
					}, false);
				}
				return uploadXhr;
			},
			success: function (data) {
				$("#dateiHochladen input[type='submit']").prop('disabled', false);
				if(data) {
					alert(data);
				}
				location.reload();
			},
			error: function (xhr, optionen, text) {
				$("#dateiHochladen input[type='submit']").prop('disabled', false);
				$("#dateiHochladen .progress-bar").text((xhr.status != 0 ? "Fehler "+xhr.status : "Upload nicht möglich")).css('width', '100%').addClass('progress-bar-danger');
				if(xhr.status!=0)alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")"); //0: Abgebrochen
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});
	$("#dateiHochladen form").on("reset", function () {
		uploadXhr.abort();
	});
});

function dateiHerunterladen () {
	var ordner = $("main").attr('data-ordner');
	var datei = $("#contextmenu-file").attr('data-target');
	window.open("?file="+encodeURIComponent(ordner)+"/"+encodeURIComponent(datei)+"&download", "_blank");
}

function ordnerHerunterladen (elem) {
	var ordner = $("main").attr('data-ordner');
	if(elem !== undefined) {
		ordner += "/"+$(elem).parents(".contextmenu").attr('data-target');
	}
	if(ordner == "" || ordner == "/")return;

	window.open("dateifreigabe-action.php?folder="+encodeURIComponent(ordner)+"&download", "_blank");
}

function ordnerLoeschen () {
	var ordner = $("#contextmenu-folder").attr('data-target');
	var base = $("main").attr('data-ordner');
	$("#ordnerLoeschen form input[name='name']").val(ordner);
	zeigePrompt("ordnerLoeschen", base);
	$("#ordnerLoeschen form input[autofocus]").focus();
}
$(document).ready(function () {
	$("#ordnerLoeschen form").submit(function (e)  {
		e.preventDefault();
		$.ajax({
			url: "dateifreigabe-action.php?loeschen",
			method: "POST",
			data: $("#ordnerLoeschen form").serialize(),
			success: function (data) {
				location.reload();
			},
			error: function (xhr, status, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			}
		});
		return false;
	});
});

function dateiLoeschen () {
	var datei = $("#contextmenu-file").attr('data-target');
	var base = $("main").attr('data-ordner');
	$("#dateiLoeschen form input[name='name']").val(datei);
	zeigePrompt("dateiLoeschen", base);
	$("#dateiLoeschen form input[autofocus]").focus();
}
$(document).ready(function () {
	$("#dateiLoeschen form").submit(function (e)  {
		e.preventDefault();
		$.ajax({
			url: "dateifreigabe-action.php?loeschen",
			method: "POST",
			data: $("#dateiLoeschen form").serialize(),
			success: function (data) {
				location.reload();
			},
			error: function (xhr, status, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			}
		});
		return false;
	});
});

function dateiEigenschaften (elem) {
	var base = $("main").attr("data-ordner");
	var name = $(elem).parents(".contextmenu").attr('data-target');
	zeigePrompt("dateiEigenschaften", name);

	$("#dateiEigenschaften td[data-info] span").text("---");
	$("#dateiEigenschaften td[data-info='name'] span").text(name);
	$.ajax({
		url: "dateifreigabe-action.php?dateiEigenschaften="+encodeURIComponent(base+"/"+name),
		method: "GET",
		success: function (data) {
			try {
				$("#dateiEigenschaften td[data-info='zeit'] span").text(data["zeit"]);
				$("#dateiEigenschaften td[data-info='groesse'] span").text(data["groesse"]);
				$("#dateiEigenschaften td[data-info='leserechte'] span").text(data["lesen"].join(", "));
				$("#dateiEigenschaften td[data-info='schreibrechte'] span").text(data["schreiben"].join(", "));
			}
			catch(err) {
				alert("Unerwartete Serverantwort");
			}
		},
		error: function (xhr, status, text) {
			alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
		}
	});
}

function umbenennen (elem) {
	var type = $(elem).parents(".contextmenu").attr('data-type');
	var target = $(elem).parents(".contextmenu").attr('data-target');
	var base = $("main").attr('data-ordner');
	$("#umbenennen form input[name='alterName']").val(target);
	$("#umbenennen form input[name='neuerName']").val(target);
	$("#umbenennen form input[name='type']").val(type);
	zeigePrompt("umbenennen", base);
	$("#umbenennen input[name='neuerName']").focus();
}
$(document).ready(function () {
	$("#umbenennen form").submit(function (e)  {
		e.preventDefault();
		$.ajax({
			url: "dateifreigabe-action.php?umbenennen",
			method: "POST",
			data: $("#umbenennen form").serialize(),
			success: function (data) {
				location.reload();
			},
			error: function (xhr, status, text) {
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			}
		});
		return false;
	});
});

function ordnerEigenschaften (elem) {
	var base = $("main").attr("data-ordner");
	var name = $(elem).parents(".contextmenu").attr('data-target');
	zeigePrompt("ordnerEigenschaften", name);

	$("#ordnerEigenschaften td[data-info] span").text("---");
	$("#ordnerEigenschaften td[data-info='name'] span").text(name);
	$.ajax({
		url: "dateifreigabe-action.php?ordnerEigenschaften="+encodeURIComponent(base+"/"+name),
		method: "GET",
		success: function (data) {
			try {
				$("#ordnerEigenschaften td[data-info='inhalt'] span").text(data["dateien"] + " Datei(/-en) in " + data["ordner"] + " Unterordner(/-n)");
				$("#ordnerEigenschaften td[data-info='groesse'] span").text(data["groesse"]);
				$("#ordnerEigenschaften td[data-info='leserechte'] span").text(data["lesen"].join(", "));
				$("#ordnerEigenschaften td[data-info='schreibrechte'] span").text(data["schreiben"].join(", "));
			}
			catch(err) {
				alert("Unerwartete Serverantwort");
			}
		},
		error: function (xhr, status, text) {
			alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
		}
	});
}

function zeigePrompt (elem, id) {
	$("#promptOverlay").fadeIn(200).css('display','table');
	$("#"+elem).stop().fadeIn();
	$("#"+elem+" input[name='id']").val(id);
}
$("#promptOverlay").click(function (e) {
	if(e.target == this || e.target == $(this).children(".prompt-container")[0]){ // klicke auf Prompt-Container zulassen
		schliessePrompt();
	}
});

function schliessePrompt () {
	$("#promptOverlay").fadeOut(200, function () {
		$(".prompt-content").hide();
		$(".prompt-content form")[0].reset();
	});
}
