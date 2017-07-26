/* Gruppen */

var lastScrollPosition = 0;

$(document).ready(function () {
	$("#gruppen th[data-sort]").click(function () {
		var newOrder = "ASC";
		if($(this).attr("data-sort") == "" || $(this).attr("data-sort") == "DESC") {
			newOrder = "ASC";
		}else
		if($(this).attr("data-sort") == "ASC") {
			newOrder = "DESC";
		}
		$("#gruppen th[data-sort]").attr("data-sort", "");
		$(this).attr("data-sort", newOrder);

		reloadGroups($(this).attr('data-column'),newOrder);
	});

	$("#gruppen table tbody").on('click', 'tr[data-groupid]', function () { //Gruppe ausgewählt
		var gruppe = $(this).attr('data-groupid');
		switchGroupMode(1);

		$("#gruppeBearbeiten button[type='submit']").prop('disabled', true);
		$("#gruppeBearbeiten td[data-info]:not([data-info='mitglieder']):not([data-info='rechte']) input").val("").prop('disabled', true);
		$("#gruppeBearbeiten td[data-info='mitglieder'] .checkbox-userselect").hide();
		$("#gruppeBearbeiten td[data-info='mitglieder'] input").prop('checked', false).prop('disabled', true);
		$("#gruppeBearbeiten td[data-info='rechte'] .checkbox").hide();
		$("#gruppeBearbeiten td[data-info='rechte'] input").prop('checked', false).prop('disabled', true);
		$("#gruppeBearbeiten").attr("data-groupid", gruppe);
		$("#gruppeBearbeiten").attr({"data-type": "update"});

		$.ajax({
			url: "get.php?getGroupData&group="+gruppe,
			method: "GET",
			success: function (data) {
				try {
					data = $.parseJSON(JSON.stringify(data));
				}
				catch (e) { // error
					alert("Fehler beim verarbeiten der Daten");
					console.log(e);
				}
				finally{ //wenn JSON
					$("#gruppeBearbeiten td[data-info='name'] input").val(data["name"]);
					$("#gruppeBearbeiten td[data-info='prioritaet'] input").val(data["prioritaet"]);

					$.each(data["mitglieder"], function (index, value) {
						$("#gruppeBearbeiten td[data-info='mitglieder'] input[value='"+value+"']").prop('checked', true);
						$("#gruppeBearbeiten td[data-info='mitglieder'] .checkbox-userselect").has("input[value='"+value+"']").show();
					});

					$.each(data["rechte"], function (index, value) {
						$("#gruppeBearbeiten td[data-info='rechte'] input[value='"+value+"']").prop('checked', true);
						$("#gruppeBearbeiten td[data-info='rechte'] .checkbox").has("input[value='"+value+"']").show();
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

	$("#createGroup").click(function () {
		$("#gruppeBearbeiten button[type='submit']").prop('disabled', true);
		$("#gruppeBearbeiten td[data-info]:not([data-info='mitglieder']):not([data-info='rechte']) input").val("").prop('disabled', false);
		$("#gruppeBearbeiten td[data-info='mitglieder'] .checkbox-userselect").hide();
		$("#gruppeBearbeiten td[data-info='mitglieder'] input").prop('checked', false).prop('disabled', false);
		$("#gruppeBearbeiten td[data-info='rechte'] .checkbox").hide();
		$("#gruppeBearbeiten td[data-info='rechte'] input").prop('checked', false).prop('disabled', false);
		$("#gruppeBearbeiten").attr("data-groupid", "");

		switchGroupMode(1);

		// Alles leer lassen

		$("#gruppeBearbeiten").attr({"data-type": "new"});
	});

	$(".backtoSelectGroup").on("click", function (e) {
		e.preventDefault();
		switchGroupMode(0);
	});

	$("#gruppeBearbeiten td[data-info]").click(function (e) {
		if($("#gruppeBearbeiten button[type='submit']").prop('disabled'))$("#gruppeBearbeiten button[type='submit']").prop('disabled', false);

		var target = $(this).attr("data-info");
		if(target == "mitglieder") {
			$("#gruppeBearbeiten td[data-info='mitglieder'] .checkbox-userselect").show();
			$("#gruppeBearbeiten td[data-info='mitglieder'] input").prop('disabled', false);
		}else
		if(target == "rechte") {
			$("#gruppeBearbeiten td[data-info='rechte'] .checkbox").show();
			$("#gruppeBearbeiten td[data-info='rechte'] input").prop('disabled', false);
		}else{
			$(this).children("input").prop('disabled', false);
		}
	});

	function switchGroupMode (mode) {
		if(mode == 0) {
			$("#gruppeBearbeiten").fadeOut(200, function () {
				$("#gruppen").fadeIn(200, function () {
					$(window).scrollTop(lastScrollPosition); //zurück zur Position
				});
			});
		}else if (mode == 1) {
			lastScrollPosition = $(window).scrollTop();
			$("#gruppen").fadeOut(200, function () {
				$("#gruppeBearbeiten").fadeIn(200, function () {
					$(window).scrollTop($(".panel").has("#gruppeBearbeiten").position().top); //zum Panel scrollen
				});
			});
		}
	}

	$('#gruppeBearbeitenForm').bind("keypress", function(e) {
		if (e.keyCode == 13) {
			e.preventDefault(); // Enter submit nicht annehmen
			return false;
		}
	});

	$("#gruppeBearbeitenForm").submit(function (e) {
		if($("#gruppeBearbeiten button[type='submit']").prop('disabled') == true){return;}
		$("#gruppeBearbeiten button[type='submit']").prop('disabled', true);
		e.preventDefault();

		$.ajax({
			url: "action.php?s=admin&updateGroup="+$("#gruppeBearbeiten").attr('data-groupid')+"&type="+$("#gruppeBearbeiten").attr('data-type'),
			method: "POST",
			data: $(this).serialize(),
			success: function (data) {
				alert("Erfolg");
				if($("#gruppeBearbeiten").attr('data-type') == "new"){$("#gruppeBearbeiten").attr({'data-type':'update'})} //Doppeleinträge verhindern, wenn nach erstellen noch etwas geändert werden soll
				if(typeof(data) != "undefined"){$("#gruppeBearbeiten").attr("data-groupid", data["gruppenid"]);} //Gruppenid festlegen

				reloadGroups($("#gruppen th[data-sort!='']").attr('data-column'), $("#gruppen th[data-sort!='']").attr('data-sort')); //Gruppen schon mal neu laden
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

	$("#deleteGroupButton").click(function () {
		var groupid = $("#gruppeBearbeiten").attr("data-groupid");
		if(confirm("Gruppe (ID "+groupid+") wirklich löschen? Dieser Vorgang kann nicht rückgängig gemacht werden. Benutzer verlieren die durch die Gruppe erlangten Rechte.")){
			$.ajax({
				url: "action.php?s=admin&deleteGroup="+groupid,
				method: "POST",
				data: {"groupid": groupid},
				success: function () {
					reloadGroups($("#gruppen th[data-sort!='']").attr('data-column'), $("#gruppen th[data-sort!='']").attr('data-sort')); //Gruppen schon mal neu laden
					switchGroupMode(0);
				},
				error: function (xhr, optionen, text) {
					alert("Gruppe konnte nicht gelöscht werden ("+xhr.status+": "+text+")");
				}
			})
		}
	});
});

function reloadGroups(orderBy, orderDirection) {
	$.ajax({
		url: "get.php?getGroups&orderBy="+orderBy+"&orderDirection="+orderDirection,
		method: "GET",
		beforeSend: function () {
			$("#gruppen table tbody").animate({"opacity": 0}, 200);
		},
		success: function (data)  {
			try {
				data = $.parseJSON(data);
			}
			catch (e) { // error

			}
			finally{ //wenn JSON
				$("#gruppen table tbody").html("");
				$.each(data, function(index, gruppe) {
					$("#gruppen table tbody").append("<tr data-groupid=\""+gruppe["gruppen_id"]+"\"><td data-info=\"priority\">"+(gruppe["Prioritaet"] != 0 ? gruppe["Prioritaet"] : "")+"</td><td data-info=\"beschreibung\">"+gruppe["Beschreibung"]+"</td><td data-info=\"numberOfMembers\">"+gruppe["Mitglieder"]+"</td></tr>\n");
				});
				$("#gruppen table tbody").stop(true, false).animate({"opacity": 1}, 200);
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
