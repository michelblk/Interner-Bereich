var lastScrollPosition = 0;

/* ---------------------- Einsätze bearbeiten -------------------------- */

$(document).ready(function () {
	$("#news").on("click", "tbody tr[data-newsid]", function (e) {
		var news = $(this).attr('data-newsid');
		switchMode(1);

		ladeNews(news);
	});

	function ladeNews (id) {
		console.log(id);
	}

});

var offset = -1;
function loadNews(mode) {
	if(mode == 0) {offset = -1;}
	$.ajax({
		url: "get.php?getNews&o="+offset,
		method: "GET",
		beforeSend: function () {
			if(mode == 0)$("#news table tbody").animate({"opacity": 0}, 200);
			$("#news-lademehr").hide();
		},
		success: function (data)  {
			try {
				if(mode == 0)$("#news table tbody").html("");
				$.each(data["news"], function(index, news) {
					$("#news table tbody").append("<tr data-newsid=\""+news["id"]+"\"><td data-info=\"newsid\">"+news["id"]+"</td><td data-info=\"datum\">"+news["datum"]+"</td><td data-info=\"kategorie\">"+news["kategorie"]+"</td><td data-info=\"titel\">"+news["titel"]+"</td></tr>\n");
				});
				if(mode == 0)$("#news table tbody").stop(true, false).animate({"opacity": 1}, 200);
				offset = data["offset"];
			}
			catch (e) { // error
				alert("Fehler beim Datenverarbeiten");
				return;
			}
			finally {
				$("#news-lademehr").show();
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


//Hilfsfunktionen
function switchMode(mode) {
	if(mode == 0) {
		$("#newsBearbeiten").fadeOut(200, function () {
			$("#news").fadeIn(200, function () {
				$(window).scrollTop(lastScrollPosition); //zurück zur Position
			});
		});
	}else if (mode == 1) {
		lastScrollPosition = $(window).scrollTop();
		$("#news").fadeOut(200, function () {
			$("#newsBearbeiten").fadeIn(200, function () {
				$(window).scrollTop($(".panel").has("#newsBearbeiten").position().top); //zum Panel scrollen
			});
		});
	}
}
