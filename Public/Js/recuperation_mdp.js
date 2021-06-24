function EnvoyerEmail(email, code_recuperation, redirect){

    Email.send({
        Host : "smtp.gmail.com",
        Username : "helpdesk.utc.toulouse@gmail.com",
        Password : "fzhlxbmdxyjfoxnc",
        To : `${email}`,
        From : "helpdesk.utc.toulouse@gmail.com",
        Subject : `Votre code est : ${code_recuperation}`,
        Body : `
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                <html xmlns:v="urn:schemas-microsoft-com:vml">
                <head>
                    <meta http-equiv="content-type" content="text/html" charset="utf-8"> 
                    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
                    <title>Document</title>
                    <link rel="preconnect" href="https://fonts.gstatic.com">
                    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet">
                </head>
                <body leftmargin="0" topmargin="0" >

                    <div style="max-width: 700px; margin: auto;">
                        <table bgcolor="" width="100%">
                            <tr>
                                <td height="50" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="50" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" style="font-family: 'Open Sans', sans-serif;">Votre code est : <b style="color: #2e4884; letter-spacing: 3px; font-size: 20px; margin-left: 10px;">${code_recuperation}</b></td>
                            </tr>

                            <tr>
                                <td style="font-family: 'Open Sans', sans-serif; padding: 40px; font-weight: 200;" align="center">Votre demande de changement de mot de passe a bien été prise en compte. Ce code est à confirmer sur l'application HELPDESK</td>
                            </tr>
                            <tr>
                                <td height="50" >&nbsp;</td>
                            </tr>
                        </table>

                        <table bgcolor="#F9F9F9" width="100%">
                            <tr>
                                <td height="30" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" style="font-family: 'Open Sans', sans-serif; font-size: 30px; font-weight: 400; letter-spacing: 7px; color: #ababab;">HELP DESK</td>
                            </tr>
                            <tr>
                                <td height="30" >&nbsp;</td>
                            </tr>
                        
                        </table>
                    </div>
                </div>
                </body>
                </html>
                `
    }).then(function(message) {
        window.location.href = redirect;
    });

}

document.getElementById("form-recup").addEventListener("submit", function(e) {

    e.preventDefault();

    var data = new FormData(this)
    var xhr = new XMLHttpRequest()

    xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
           
            document.getElementById("email").innerHTML = ""

            console.log(this.response)
            var erreurs = this.response.erreurs
            var success = this.response.success

            if (success)
            {
                var email_recuperation = this.response.email_recuperation
                var code_recuperation = this.response.code_recuperation

                EnvoyerEmail(email_recuperation, code_recuperation, 'MotDePasseOublie?section=code')

            } else {
                document.getElementById("email").innerHTML = erreurs
            }

		} else if (this.readyState == 4) {
			alert("Une erreur est survenue...");
		}
	};

    xhr.open("POST", "App/Ajax/recuperation_mdp.php", true);
	xhr.responseType = "json";
	xhr.send(data);

	return false;
})