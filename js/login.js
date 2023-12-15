$(document).ready(function () {
    $("#formLogin").submit(function (e) {
        e.preventDefault();
        url = "/chat/services/login/authentication.php";
        username = $.trim($('#username').val());
        password = $.trim($('#password').val());

        console.log("Username: ", username);
        console.log("Password: ", password);

        GetStatusbtnLogin(username, password);

        $.ajax({
            url: url,
            data: {
                "username": username,
                "password": password
            },
            type: 'POST',
            dataType: 'json'
        }).done(function (response) {
            console.log("Response: ", response);

            if (response == 'OK') {
                $("#formLogin").trigger("reset");
                window.location.href = '/chat/';
            } else {
                swal.fire("Error!", response, "error");
                $("#btnLogin").html("Iniciar sesión");
                $('#btnLogin').attr('disabled', false);
            }
        }).fail(function (response) {
            console.log("Response: ", response);
        });
    });
});

function GetStatusbtnLogin(username, password) {
    if (username != "" && password != "") {
        console.log("Click");

        $('#btnLogin').attr('disabled', true);

        $("#btnLogin").html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Cargando...
        `);
    }
}