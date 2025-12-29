$(document).ready(function () {
    getLoad();
    hasilTrxToday();
    $("#trx_no_barcode").keypress(function (event) {
        var no_barcode = $("#trx_no_barcode").val();
        $("#trx_nominal").val("");
        if (event.keyCode === 13) {
            if (no_barcode == null || no_barcode == "") {
                infoFireAlert("warning", "Masukkan Nomer Barcode .");
                return false;
            } else {
                $.ajax({
                    url: APP_BACKEND + "api/trx/get_barcode",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer " + key);
                    },
                    type: "get",
                    dataType: "json",
                    data: {
                        no_barcode: no_barcode,
                    },
                }).done(function (resp) {
                    if (resp.success) {
                        $("#trx_nama").val(resp.nama);
                        $("#trx_nik").val(resp.nik);
                        $("#trx_nominal").val("");
                        $("#trx_no_barcode").prop("readonly", true);
                        $("#trx_nominal").focus();
                    } else {
                        $("#trx_nama").val("");
                        $("#trx_no_barcode").val("");
                        $("#trx_no_barcode").focus();
                        infoFireAlert("error", resp.message);
                    }
                });
            }
        }
    });

    easyNumberSeparator({
        selector: ".number-separator",
        separator: ",",
        // resultInput: "#trx_nominal",
    });

    $("#btn_cancel_trx").click(function () {
        getLoad();
    });

    $("#frm_trx").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/trx/ins_transaksi",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "post",
            dataType: "json",
            data: datas,
        })
            .done(function (resp) {
                if (resp.success) {
                    fireAlert("success", resp.message);
                    hasilTrxToday();
                    getLoad();
                    // $("#modal_penyelenggara").modal("toggle");
                    // list_penyelenggara.ajax.reload(null, false);
                } else {
                    infoFireAlert("error", resp.message);
                }
            })
            .fail(function () {
                $("#error").html(
                    "<div class='alert alert-danger'><div>Tidak dapat terhubung ke server !!!</div></div>"
                );
            });
    });
});

function hasilTrxToday() {
    $.ajax({
        url: APP_BACKEND + "api/trx/hasil_trx_today",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Bearer " + key);
        },
        type: "get",
        dataType: "json",
    })
        .done(function (resp) {
            if (resp.success) {
                $("#hasil_anggota").html(
                    "Rp " +
                        new Intl.NumberFormat("id-ID").format(
                            resp.hasil_anggota
                        )
                );
                $("#hasil_umum").html(
                    "Rp " +
                        new Intl.NumberFormat("id-ID").format(resp.hasil_umum)
                );
                $("#hasil_total").html(
                    "Rp " +
                        new Intl.NumberFormat("id-ID").format(resp.hasil_total)
                );
                // list_penyelenggara.ajax.reload(null, false);
            } else {
                infoFireAlert("error", resp.message);
            }
        })
        .fail(function () {
            $("#error").html(
                "<div class='alert alert-danger'><div>Tidak dapat terhubung ke server !!!</div></div>"
            );
        });
}

function getLoad() {
    $("#r_ang").is(":checked");
    $("#trx_no_barcode").prop("readonly", false);
    $("#trx_nama").val("");
    $("#trx_nik").val("");
    $("#trx_no_barcode").val("");
    $("#trx_kategori").val("Anggota");
    $("#trx_nominal").val("");
    $("#trx_no_barcode").focus();
}
