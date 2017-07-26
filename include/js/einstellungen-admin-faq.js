/* FAQ */


var lastScrollPosition = 0;

function ladeFAQ () {
	$.ajax({
		url: "get.php?getFAQs",
		method: "GET",
		beforeSend: function () {
			$("#FAQ table tbody").animate({"opacity": 0}, 200);
		},
		success: function (data)  {
			try {
				data = $.parseJSON(data);
			}
			catch (e) { // error

			}
			finally{ //wenn JSON
				$("#FAQ table tbody").html("");
				$.each(data, function(index, faq) {
					$("#FAQ table tbody").append("<tr data-id=\""+faq["id"]+"\"><td data-info=\"folge\">"+(faq["folge"] != 0 ? faq["folge"] : "")+"</td><td data-info=\"frage\">"+faq["frage"]+"</td><td data-info=\"antwort\">"+faq["antwort"]+"</td></tr>\n");
				});
				$("#FAQ table tbody").stop(true, false).animate({"opacity": 1}, 200);
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

function back () {
	switchMode(0);
}

function switchMode (mode) {
	if(mode == 0) {
		$("#FAQbearbeiten").fadeOut(200, function () {
			$("#FAQ").fadeIn(200, function () {
				$(window).scrollTop(lastScrollPosition); //zurück zur Position
			});
		});
	}else if (mode == 1) {
		lastScrollPosition = $(window).scrollTop();
		$("#FAQ").fadeOut(200, function () {
			$("#FAQbearbeiten").fadeIn(200, function () {
				$(window).scrollTop($(".panel").has("#FAQbearbeiten").position().top); //zum Panel scrollen
			});
		});
	}
}

function neueAntwort () {

}


/* Events */
$(document).ready(function () {
	$('#FAQbearbeiten form').bind("keypress", function(e) {
		if (e.keyCode == 13) {
			e.preventDefault(); // Enter submit nicht annehmen
			return false;
		}
	});

	$("#FAQ table tbody").on('click', 'tr[data-id]', function () {
		var faq = $(this).attr('data-id');
		switchMode(1);

		alert("Diese Funktion steht noch nicht zur Verfügung");

	});
});
