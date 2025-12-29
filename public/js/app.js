var APP_URL = window.location.origin;
var APP_BACKEND = APP_URL + "/";
var key = localStorage.getItem("kopim_token");

function fireAlert(jenis, msg) {
    return Swal.fire({
        position: "center",
        icon: jenis,
        title: msg,
        showConfirmButton: false,
        timer: 1500,
    });
}

function infoFireAlert(jenis, msg) {
    return Swal.fire({
        position: "center",
        icon: jenis, // ✅ gunakan 'icon' (seperti 'info', 'success', 'warning', 'error')
        title:
            "<span style='font-family: Arial; font-size: 20px; font-weight: bold; '>" +
            msg +
            "</span>",
        showConfirmButton: true,
        focusConfirm: true,
    });
}
