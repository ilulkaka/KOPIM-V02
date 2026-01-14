$(document).ready(function () {
    getLoad();
    hasilTrxToday();
    $("#trx_no_barcode").keypress(function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            let no_barcode = $(this).val().trim();
            $("#trx_nominal").val("");

            if (no_barcode === null || no_barcode === "") {
                infoFireAlert("warning", "Masukkan Nomer Barcode .");
                return false;
            }

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
    });

    easyNumberSeparator({
        selector: ".number-separator",
        separator: ",",
        // resultInput: "#trx_nominal",
    });

    $("#btn_cancel_trx").click(function () {
        getLoad();
    });

    $("#btn_detail_trx").click(function () {
        $("#modal_detail_trx").modal("show");
        getListDetailTrx();
    });

    $("#btn_download_trx").click(function () {
        $("#modal_download_trx").modal("show");
    });

    $("#btn_reload").on("click", function () {
        list_detail_trx.ajax.reload();
    });

    let isSubmitting = false;
    $("#frm_trx").on("submit", function (e) {
        e.preventDefault();

        // if (isSubmitting) {
        //     return false; // STOP submit ke-2
        // }

        let barcode = $("#trx_no_barcode").val().trim();
        if (barcode === "") {
            infoFireAlert("warning", "Barcode tidak boleh kosong");
            return false;
        }

        // isSubmitting = true; // LOCK

        var datas = $(this).serialize();

        $("#btn_submit").prop("disabled", true).text("Processing...");

        Swal.fire({
            title: "Konfirmasi",
            text: "Yakin akan menyimpan data transaksi ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Simpan",
            cancelButtonText: "Batal",
            // reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // aksi simpan data
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
                            getLoad();
                            hasilTrxToday();
                            // $("#modal_penyelenggara").modal("toggle");
                            // list_penyelenggara.ajax.reload(null, false);
                            $("#btn_submit")
                                .prop("disabled", false)
                                .text("Simpan");
                        } else {
                            infoFireAlert("error", resp.message);
                        }
                        // isSubmitting = false; // UNLOCK
                    })
                    .fail(function () {
                        $("#error").html(
                            "<div class='alert alert-danger'><div>Tidak dapat terhubung ke server !!!</div></div>"
                        );
                    });
            }

            // ❌ JIKA KLIK BATAL / CLOSE
            if (result.isDismissed) {
                $("#btn_submit").prop("disabled", false).text("Simpan");
            }
        });
    });

    $("#tb_detail_trx").on("click", ".edtTrx", function (e) {
        e.preventDefault();
        var datas = list_detail_trx.row($(this).parents("tr")).data();
        $("#et_id_anggota").val(datas.id_anggota);
        $("#et_id").val(datas.id_trx_belanja);
        $("#et_tgl").val(datas.tgl_trx);
        $("#et_no").val(datas.no_barcode);
        $("#et_nama").val(datas.nama);
        $("#et_nominal").val(datas.nominal);

        $("#modal_edit_trx").modal("show");
    });

    $("#frm_edt_trx").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/trx/edt_transaksi",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "patch",
            dataType: "json",
            data: datas,
        })
            .done(function (resp) {
                if (resp.success) {
                    fireAlert("success", resp.message);
                    $("#modal_edit_trx").modal("toggle");
                    list_detail_trx.ajax.reload(null, false);
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

    $("#btn_download").on("click", function () {
        var format = $("#dot_format").val();
        var tgl_awal = $("#tgl_awal1").val();
        var tgl_akhir = $("#tgl_akhir1").val();

        if (format == "" || tgl_awal == "" || tgl_akhir == "") {
            infoFireAlert("warning", "Kolom Harus terisi semua .");
        } else if (format == "Pdf") {
            infoFireAlert("warning", "Format PDF Belum tersedia .");
        } else {
            $.ajax({
                url: APP_BACKEND + "api/trx/download_transaksi",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                type: "get",
                dataType: "json",
                data: {
                    format: format,
                    tgl_awal: tgl_awal,
                    tgl_akhir: tgl_akhir,
                },
                success: function (response) {
                    if (response.file) {
                        var fpath = response.file;
                        window.open(fpath, "_blank");
                        $("#modal_download_trx").modal("hide");

                        $("#modal_sending_mail").modal("show");
                        var m_tgl_awal = $("#m_tgl_awal").val(tgl_awal);
                        var m_tgl_akhir = $("#m_tgl_akhir").val(tgl_akhir);
                        //location.reload();
                    } else {
                        infoFireAlert("warning", response.message);
                    }
                },
            });
        }
    });

    $("#frm_sm").on("submit", function (e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/trx/send_mail",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "get",
            dataType: "json",
            data: data,
        }).done(function (resp) {
            if (resp.success) {
                alert(resp.message);
                location.reload();
                //$('#modal_sending_mail').modal('toggle');
                // list_detail_trx.ajax.reload(null, false);
            } else {
                alert(resp.message);
            }
        });
    });
});

var list_detail_trx;
function getListDetailTrx() {
    if ($.fn.DataTable.isDataTable("#tb_detail_trx")) {
        list_detail_trx.ajax.reload();
    } else {
        list_detail_trx = $("#tb_detail_trx").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/trx/list_detail_trx",
                type: "GET",
                beforeSend: function (xhr) {
                    $("#btn_reload").attr("disabled", true);
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                data: function (d) {
                    d.tgl_awal = $("#tgl_awal").val();
                    d.tgl_akhir = $("#tgl_akhir").val();
                },
                dataType: "json",
                complete: function () {
                    $("#btn_reload").attr("disabled", false);
                },
                error: function () {
                    $("#btn_reload").attr("disabled", false);
                },
            },

            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false,
                },
                {
                    targets: [5],
                    data: null,
                    // width: '10%',
                    render: function (data, type, row, meta) {
                        return "<a href = '#' style='font-size:14px' class = 'edtTrx'> Edit </a> || <a href = '#' style='font-size:14px' class ='delTrx' > Deleted </a>";
                    },
                },
            ],

            columns: [
                {
                    data: "id_trx_belanja",
                    name: "id_trx_belanja",
                },
                {
                    data: "tgl_trx",
                    name: "tgl_trx",
                },
                {
                    data: "no_barcode",
                    name: "no_barcode",
                },
                {
                    data: "nama",
                    name: "nama",
                    width: "40%",
                },
                {
                    data: "nominal",
                    name: "nominal",
                    render: $.fn.dataTable.render.number(",", ".", 0, ""),
                },
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === "string"
                        ? i.replace(/[\$,]/g, "") * 1
                        : typeof i === "number"
                        ? i
                        : 0;
                };

                // Total over all pages
                total = api
                    .column(1)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                TotalNominal = api
                    .column(4, {
                        page: "current",
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html(
                    TotalNominal.toLocaleString("en-US")
                );
            },
        });
    }
}

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
    $("#trx_no_barcode").val("");
    $("#trx_nama").val("");
    $("#trx_nik").val("");
    $("#trx_kategori").val("Anggota");
    $("#trx_nominal").val("");
    $("#trx_no_barcode").focus();
}
