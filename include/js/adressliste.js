$(document).ready(function () {
	$("#users tbody tr").on("click", function (e) {
		var user = $(this).attr('data-userid');

		$('html, body').animate({
			scrollTop: $(".panel").has("#userinfo").offset().top - 50
		}, 300);

		$("#users tbody tr").removeClass("active");
		$(this).addClass("active");

		$.ajax({
			url: "adressliste-action.php?getUserData&user="+user,
			method: "GET",
			beforeSend: function () {
				$("#userinfo").fadeOut(200);
				$("#userSelectionNote").text("Lädt");
			},
			success: function (data) {
				try {
					data = $.parseJSON(data);
				}
				catch (e) { // error

				}
				finally{ //wenn JSON
					$("#userinfo td[data-info]").text("");
					$("#userSelectionNote").hide();
					$("#userinfo").attr("data-userid", data["userid"]);
					$("#userinfo td[data-info='email']").html("<a href='mailto:"+data["Mail"]+"'>"+data["Mail"]+"</a>");
					$("#userinfo td[data-info='vorname']").text(data["Vorname"]);
					$("#userinfo td[data-info='nachname']").text(data["Nachname"]);
					$("#userinfo td[data-info='strasse']").html("<a href='http://maps.google.com/?q="+data["Strasse"]+", "+data["PLZ"]+" "+data["Wohnort"]+"' target=\"_blank\">"+data["Strasse"]+"</a>");
					$("#userinfo td[data-info='wohnort']").text(data["PLZ"]+" "+data["Wohnort"]);
					$("#userinfo td[data-info='telefon']").text(data["Telefon"]);
					$("#userinfo td[data-info='mobil']").text(data["Mobil"]);
					$("#userImage").css("background-image", "url('adressliste-action.php?getUserImage&user="+user+"')");
					var maxPrioritaet = data["maxPrioritaet"];
					$.each(data["gruppen"], function (index, value) {
						$("#userinfo td[data-info='gruppen']").append("<div class=\"userinfo-groups\" data-groupid=\""+value["id"]+"\" data-important=\""+(value["Prioritaet"] == maxPrioritaet && maxPrioritaet != 0 ? "true":"false")+"\">"+value["beschreibung"]+"</div>");
					});

					$("#userinfo").stop().fadeIn(200);
				}
			},
			error: function (e) {

			},
			complete: function () {

			}
		});
	});

	$("#users th[data-sort]").click(function () {
		var newOrder = "ASC";
		if($(this).attr("data-sort") == "" || $(this).attr("data-sort") == "DESC") {
			newOrder = "ASC";
		}else
		if($(this).attr("data-sort") == "ASC") {
			newOrder = "DESC";
		}
		$("#einsaetze th[data-sort]").attr("data-sort", "");
		$(this).attr("data-sort", newOrder);

		location.href="?sortBy="+$(this).attr('data-column')+"&sortOrder="+newOrder;
	});
});
