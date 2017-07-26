$(document).ready(function () {
	function anmelden () {
		$.ajax({
			url: 'login-action.php?login',
			method: 'post',
			data: {
				email: $("#loginEmail").val(),
				pw: $("#loginPassword").val()
			},
			beforeSend: function () {
				$("#loginDialog").fadeOut(200);
			},
			success: function () {
				if(getUrlParameter('ref')){
					location.href=atob(getUrlParameter('ref')); //decode base64
				}else
					location.href="index.php";
			},
			error: function (jqXHR, textStatus, errorThrown) {
				if(jqXHR.status == 401) { //username or password wrong
					$("#loginError").text("Kombination von E-Mail und Passwort ungültig.").fadeIn(200);
				}else if (jqXHR.status == 400) { // bad input
					$("#loginError").text("Eingabefehler").fadeIn(200);
				}else{ // unknown error
					$("#loginError").text("Unbekannter Fehler " + jqXHR.status).fadeIn(200);
				}
			},
			complete: function () {
				setTimeout(function () {
					$("#loginDialog").fadeIn(200);
				}, 300);

			}
		});
	}

	function forgotPassword() {
		if($("#loginEmail").val().length > 4) {
			$("#forgotPassword").prop('disabled', true);
			$.ajax({
				url: "login-action.php?forgotPassword",
				method: "POST",
				data: {email: $("#loginEmail").val()},
				success: function () {
					$("#loginError").text("Es wurde eine E-Mail an dich verschickt").fadeIn(200);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					if(jqXHR.status == 401) {
						$("#loginError").text("Ein Benutzer mit dieser E-Mail existiert nicht").fadeIn(200);
					}else if(jqXHR.status == 500){
						$("#loginError").text("Interner Fehler. Bitte später versuchen.").fadeIn(200);
					}else if(jqXHR.status == 401){
						$("#loginError").text("Diese Funktion wurde für den Benutzer deaktiviert").fadeIn(200);
					}else if(jqXHR.status == 429){
						$("#loginError").text("Du hast bereits das Passwort zurückgesetzt. Bitte warte auf die E-Mail oder kontaktiere einen Admin.").fadeIn(200);
					}else{
						$("#loginError").text("Beim Zurücksetzen deines Passwortes ist etwas schief gelaufen").fadeIn(200);
					}

				}
			});
		}else{
			$("#loginError").text("Mit deiner E-Mail stimmt etwas nicht").fadeIn(200);
		}
	}

	$("#loginBtn").click(function () {
		anmelden();
	});
	$("#logininput").submit(function(e) {
		e.preventDefault();
		anmelden();
	});
	$("#forgotPassword").click(function () {
		forgotPassword();
	});

	$("#loginEmail").keyup(function () {
		$("#forgotPassword").prop('disabled', false);
	})
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
