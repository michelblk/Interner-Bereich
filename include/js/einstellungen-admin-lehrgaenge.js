/* Benutzer */

var lastScrollPosition = 0;

function loadCourses () {
	$("#lehrgaenge tbody td").remove();
	$.ajax({
		url: "get.php?getCourses",
		method: "GET",
		success: function (data) {
			$.each(data, function(index, lehrgang) {
				$("#lehrgaenge table tbody").append("<tr data-lehrgangid=\""+lehrgang["lehrgang_id"]+"\"><td data-info=\"reihenfolge\">"+lehrgang["reihenfolge"]+"</td><td data-info=\"name\">"+lehrgang["name"]+"</td><td data-info=\"abkuerzung\">"+lehrgang["abkuerzung"]+"</td><td data-info=\"icon\"><i class=\"fa fa-"+lehrgang["icon"]+"\"></i></td></tr>\n");
			});
		},
		error: function (xhr, status, text) {
			if(xhr.status == 401) {
				alert("Es sieht so aus, als wurde dir die Berechtigung für diese Seite entzogen.");
			}else
			alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
		}
	});
}

function switchCourseMode (mode) {
	if(mode == 0) {
		$("#lehrgangBearbeiten").fadeOut(200, function () {
			$("#lehrgaenge").fadeIn(200, function () {
				$(window).scrollTop(lastScrollPosition); //zurück zur Position
			});
		});
	}else if (mode == 1) {
		lastScrollPosition = $(window).scrollTop();
		$("#lehrgaenge").fadeOut(200, function () {
			$("#lehrgangBearbeiten").fadeIn(200, function () {
				$(window).scrollTop($(".panel").has("#lehrgangBearbeiten").position().top); //zum Panel scrollen
			});
		});
	}
}

function neuerLehrgang () {
	switchCourseMode(1);
	$("#lehrgangBearbeiten button[type='submit']").prop('disabled', true);
	$("#lehrgangBearbeiten td[data-info]:not([data-info='mitglieder']):not(.donotclear) input").val("").prop('disabled', false); // ! deaktiviert die sperre
	$("#lehrgangBearbeiten td[data-info='mitglieder'] .checkbox-userselect").hide();
	$("#lehrgangBearbeiten td[data-info='mitglieder'] input").prop('checked', false).prop('disabled', true);
	$("#lehrgangBearbeiten td[data-info='id'] input").attr("placeholder", "automatisch vergeben");
	$("#lehrgangBearbeiten").attr("data-lehrgangid", "-1");
	$("#lehrgangBearbeiten td[data-info='id'] input").val("");
	$("#lehrgangBearbeiten").attr({"data-type": "new"});
}

function loescheLehrgang () {
	var lehrgang = $("#lehrgangBearbeiten").attr("data-lehrgangid");
	if(confirm("Lehrgang (ID "+lehrgang+") wirklich löschen? Dieser Vorgang kann nicht rückgängig gemacht werden.")){
		$.ajax({
			url: "action.php?s=admin&deleteCourse="+lehrgang,
			method: "POST",
			data: {"id": lehrgang},
			success: function () {
				loadCourses();
				switchCourseMode(0);
			},
			error: function (xhr, optionen, text) {
				alert("Lehrgang konnte nicht gelöscht werden ("+xhr.status+": "+text+")");
			}
		})
	}
}

$(document).ready(function () {
	$("#lehrgaenge table tbody").on('click', 'tr[data-lehrgangid]', function () { //Gruppe ausgewählt
		var lehrgang = $(this).attr('data-lehrgangid');
		switchCourseMode(1);

		$("#lehrgangBearbeiten button[type='submit']").prop('disabled', true);
		$("#lehrgangBearbeiten td[data-info]:not([data-info='mitglieder']) input").val("").prop('disabled', true);
		$("#lehrgangBearbeiten td[data-info='mitglieder'] .checkbox-userselect").hide();
		$("#lehrgangBearbeiten td[data-info='mitglieder'] input").prop('checked', false).prop('disabled', true);
		$("#lehrgangBearbeiten").attr("data-lehrgangid", lehrgang);
		$("#lehrgangBearbeiten").attr({"data-type": "update"});

		$.ajax({
			url: "get.php?getCourseData="+lehrgang,
			method: "GET",
			success: function (data) {
				$("#lehrgangBearbeiten td[data-info='id'] input").val(data["lehrgang_id"]);
				$("#lehrgangBearbeiten td[data-info='name'] input").val(data["name"]);
				$("#lehrgangBearbeiten td[data-info='abkuerzung'] input").val(data["abkuerzung"]);
				$("#lehrgangBearbeiten td[data-info='icon'] input").val(data["icon"]);
				$("#lehrgangBearbeiten td[data-info='reihenfolge'] input").val(data["reihenfolge"]);

				$.each(data["mitglieder"], function (index, value) {
					$("#lehrgangBearbeiten td[data-info='mitglieder'] input[value='"+value+"']").prop('checked', true);
					$("#lehrgangBearbeiten td[data-info='mitglieder'] .checkbox-userselect").has("input[value='"+value+"']").show();
				});
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


	$(".backtoSelectCourse").on("click", function (e) {
		e.preventDefault();
		switchCourseMode(0);
	});

	$("#lehrgangBearbeiten td[data-info]").click(function (e) {
		if($("#lehrgangBearbeiten button[type='submit']").prop('disabled'))$("#lehrgangBearbeiten button[type='submit']").prop('disabled', false);

		var target = $(this).attr("data-info");
		if(target == "mitglieder") {
			$("#lehrgangBearbeiten td[data-info='mitglieder'] .checkbox-userselect").show();
			$("#lehrgangBearbeiten td[data-info='mitglieder'] input").prop('disabled', false);
		}else{
			$(this).children("input").prop('disabled', false);
		}
	});

	$('#lehrgangBearbeitenForm').bind("keypress", function(e) {
		if (e.keyCode == 13) {
			e.preventDefault(); // Enter submit nicht annehmen
			return false;
		}
	});

	$("#lehrgangBearbeitenForm").submit(function (e) {
		if($("#lehrgangBearbeiten button[type='submit']").prop('disabled') == true){return;}
		$("#lehrgangBearbeiten button[type='submit']").prop('disabled', true);
		e.preventDefault();

		$.ajax({
			url: "action.php?s=admin&updateCourse="+$("#lehrgangBearbeiten").attr('data-lehrgangid')+"&type="+$("#lehrgangBearbeiten").attr('data-type'),
			method: "POST",
			data: $(this).serialize(),
			success: function (data) {
				//alert("Erfolg");
				if($("#lehrgangBearbeiten").attr('data-type') == "new"){$("#lehrgangBearbeiten").attr({'data-type':'update'})} //Doppeleinträge verhindern, wenn nach erstellen noch etwas geändert werden soll
				switchCourseMode(0);
				loadCourses(); //Lehrgänge neu laden
			},
			error: function (xhr, status, text) {
				if(xhr.status == 401) {
					alert("Es sieht so aus, als wurde dir die Berechtigung für diese Seite entzogen.");
				}else
				alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
			},
			cache: false
		});
	});
});
