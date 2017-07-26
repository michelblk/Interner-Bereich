function ladeDaten (typ, sortBy, sortOrder, filter) {
	$.ajax({
		url: "statistic-get.php?detail=einsaetze&typ="+typ+"&sortBy="+sortBy+"&sortOrder="+sortOrder+"&filter="+filter,
		method: "GET",
		beforeSend: function () {
			$("#einsaetze table tbody").animate({"opacity": 0}, 200);
		},
		success: function (data) {
			$("#einsaetze th[data-sort]").attr("data-sort", "");
			$("#einsaetze th[data-column='"+sortBy+"']").attr("data-sort", sortOrder);
			$("#einsaetze").attr('data-filter', filter);
			$("#einsaetze").attr('data-typ', typ);

			$("#einsaetze table tbody").html("");
			$.each(data, function(index, einsatz) {
				$("#einsaetze table tbody").append("<tr data-einsatz=\""+einsatz["einsatz_id"]+"\" data-num=\""+einsatz["num"]+"\" data-jahr=\""+einsatz["jahr"]+"\">\
				<td data-info=\"nr\">"+einsatz["assNum"]+"</td>\
				<td data-info=\"datum\">"+einsatz["datum"]+"</td>\
				<td data-info=\"zeit\">"+einsatz["zeit"]+"</td>\
				<td data-info=\"name\">"+einsatz["name"]+"</td>\
				<td data-info=\"anwesende\">"+(einsatz["anwesende"] == "0" ? "k. A.":einsatz["anwesende"])+"</td>\
				<td data-info=\"dauer\">"+einsatz["dauer"]+"</td>\
				</tr>\n");
			});
			$("#einsaetze table tbody").stop(true, false).animate({"opacity": 1}, 200);
		},
		error: function (xhr, optionen, text) {
			alert("Ein Fehler ist aufgetreten. ("+xhr.status+": "+text+")");
		}
	});
}
$(document).ready(function () {
	$("#einsaetze th[data-sort]").click(function () {
		var newOrder = "ASC";
		if($(this).attr("data-sort") == "" || $(this).attr("data-sort") == "DESC") {
			newOrder = "ASC";
		}else
		if($(this).attr("data-sort") == "ASC") {
			newOrder = "DESC";
		}
		$("#einsaetze th[data-sort]").attr("data-sort", "");
		$(this).attr("data-sort", newOrder);

		ladeDaten($("#einsaetze").attr('data-typ'),$(this).attr('data-column'),newOrder,$("#einsaetze").attr('data-filter'));
	});

	$("#einsaetze").on("click", "table tr[data-num]", function () {
		console.log("OK");
		window.open('http://domain.de/einsaetze/einsatz.php?num='+$(this).attr('data-num')+'&year='+$(this).attr('data-jahr'));
	});
});
