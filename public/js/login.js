$(document).ready(function () {
    var APP_URL = window.location.origin;

    $("#login").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();
        var btn = $("#btn-login");
        btn.html("Sign In");
        btn.attr("disabled", true);
        $.ajax({
            url: APP_URL + "/login",
            type: "POST",
            dataType: "json",
            data: data,
        })
            .done(function (resp) {
                if (resp.success) {
                    localStorage.setItem("kopim_token", resp.token);
                    window.location.href = resp.dashboard_url;
                } else {
                    $("#error").html(
                        "<div class='alert alert-danger'><div>" +
                            resp.message +
                            "</div></div>"
                    );
                }
            })
            .fail(function () {
                $("#error").html(
                    "<div class='alert alert-danger'><div>Tidak dapat terhubung ke server !!!</div></div>"
                );
                //toastr['warning']('Tidak dapat terhubung ke server !!!');
            })
            .always(function () {
                btn.html("Login");
                btn.attr("disabled", false);
            });

        return false;
    });
});
