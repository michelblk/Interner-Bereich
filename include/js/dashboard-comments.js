$(document).ready(function () {
	ladeNachrichten(-1, 20); // 20 neuesten Nachrichten

	$("#submit-form").submit(function (e) {
		e.preventDefault();

		$.ajax({
			url: "dashboard-calendar-action.php?sendtoChat",
			method: "POST",
			data: {text: $("#kommentare-eingabe .form-group textarea").val()},
			beforeSend: function () {
				$("#kommentare-eingabe button").hide();
			},
			success: function (data) {
				try {
					data = $.parseJSON(data);
				}
				catch (e) { // error

				}
				finally{ //wenn JSON
					if(typeof data["name"] != "undefined" && typeof data["text"] != "undefined" && typeof data["time"] != "undefined"){
						$("#kommentare-eingabe .form-group textarea").val("");
						NachrichtAnhaengen(data);
					}
				}
			},
			error: function () {
				alert("Ein Fehler ist aufgetreten. Bitte versuche es später erneut");
			},
			complete: function () {
				$("#kommentare-eingabe button").show();
			}
		});
	});

	$("#kommentare-benutzer").on("click", ".loeschen", function () {
		var id = $(this).parent().attr('data-id');
		var elem = $(this).parent();
		if(confirm("Kommentar wirklich löschen?")){
			$.ajax({
				url: "dashboard-calendar-action.php?deleteComment="+id,
				method: "POST",
				data: {"id": id},
				success: function () {
					elem.hide(200, function () {
						elem.remove();
					});
				},
				error: function (xhr, optionen, text) {
					alert("Beim Löschen des Kommentars ist ein Fehler aufgetreten. ("+xhr.status+": "+text+")");
				}
			});
		}
	})
});

var aeltesteID = -1;

function ladeNachrichten (von, anzahl) {
	$.ajax({
		url: "dashboard-calendar-action.php?chat&o="+von+"&n="+anzahl,
		method: "GET",
		beforeSend: function () {
			$("#kommentare-lademehr").hide();
		},
		success: function (data) {
			try {
				data = $.parseJSON(data);
			}
			catch (e) { // error

			}
			finally{ //wenn JSON
				$.each(data, function (key, val) { // neueste Nachricht wird zuerst verarbeitet
					if(typeof val["name"] != "undefined" && typeof val["text"] != "undefined" && typeof val["time"] != "undefined")
						NachrichtVorhaengen(val);
				});
			}
		},
		error: function () {

		}
	});
}

function NachrichtVorhaengen(data) { // Nachrichten laden
	var name = data["name"];
	var text = $('<div />').text(data["text"]).html(); // encode htmlentitites
	var zeit = data["time"];
	var id = data["id"];
	if(id < aeltesteID || aeltesteID < 0)aeltesteID=id;
	$("#kommentare-benutzer").append("<div class=\"kommentar\" data-id=\""+id+"\"><h5>" + name + "</h5><p class=\"small text-muted\"><i class=\"fa fa-clock-o\"></i> " + zeit + "</p><pre>" + text +"</pre></div>");
	if(data["deletable"]){$("<i class=\"loeschen fa fa-minus-square-o\"></i>").insertAfter($(".kommentar[data-id='"+id+"'] h5"));}
	$(".kommentar[data-id='"+id+"']").hide().fadeIn(400);

	if(aeltesteID == 1) {
		$("#kommentare-lademehr").hide();
	}else{
		$("#kommentare-lademehr").show();
	}

}
function NachrichtAnhaengen(data) { // Geschriebene Nachricht
	var name = data["name"];
	var text = $('<div />').text(data["text"]).html(); // encode htmlentitites
	var zeit = data["time"];
	var id = data["id"];
	$("#kommentare-benutzer").prepend("<div class=\"kommentar\" data-id=\""+id+"\"><h5>" + name + "</h5><p class=\"small text-muted\"><i class=\"fa fa-clock-o\"></i> " + zeit + "</p><pre>" + text +"</pre></div>");
	if(data["deletable"]){$("<i class=\"loeschen fa fa-minus-square-o\"></i>").insertAfter($(".kommentar[data-id='"+id+"'] h5"));}
	$(".kommentar[data-id='"+id+"']").hide(0).fadeIn(400);

}

function aeltereNachrichten () { // ! Wenn in der Zwischenzeit eine Nachricht dazu kam, wird eine ältere Nachricht ausgelassen und die neuste Nachricht wird nicht angezeigt!!!
	ladeNachrichten(aeltesteID-1, 20);
}
