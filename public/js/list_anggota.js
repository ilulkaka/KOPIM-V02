$(document).ready(function () {
    getListAnggota();

    $("#tb_list_anggota").on("click", ".edtAnggota", function () {
        var data = list_anggota.row($(this).parents("tr")).data();
        $("#ea_id_anggota").val(data.id_anggota);
        $("#ea_nama").val(data.nama);
        $("#ea_nik").val(data.nik);
        $("#ea_noktp").val(data.no_ktp);
        $("#ea_alamat").val(data.alamat);
        $("#ea_notelp").val(data.no_telp);
        $("#ea_status").val(data.status);
        $("#modal_edit_anggota").modal("show");
    });

    $("#frm_ea").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/anggota/edt_anggota",
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
                    $("#modal_edit_anggota").modal("toggle");
                    list_anggota.ajax.reload(null, false);
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

    $("#btn_tambah").click(function () {
        $("#frm_ta")[0].reset();
        $("#modal_tambah_anggota").modal("show");
    });

    $("#frm_ta").submit(function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/anggota/add_anggota",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "post",
            dataType: "json",
            data: datas,
        }).done(function (resp) {
            if (resp.success) {
                alert(resp.message);
                location.reload();
            } else {
                alert(resp.message);
            }
        });
    });

    $("#btn_print").click(function () {
        $("#modal_print_qr").modal("show");
    });
});

var list_anggota;
function getListAnggota() {
    if ($.fn.DataTable.isDataTable("#tb_list_anggota")) {
        list_anggota.ajax.reload();
    } else {
        list_anggota = $("#tb_list_anggota").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/anggota/list_anggota",
                type: "GET",
                beforeSend: function (xhr) {
                    $("#btn_reload").attr("disabled", true);
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
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
                    targets: [8],
                    data: null,
                    //defaultContent: "<button class='btn btn-success'>Complited</button>"
                    render: function (data, type, row, meta) {
                        return "<a href = '#' style='font-size:14px' class = 'edtAnggota'> Edit </a>";
                    },
                },
            ],

            columns: [
                {
                    data: "id_anggota",
                    name: "id_anggota",
                },
                {
                    data: "no_barcode",
                    name: "no_barcode",
                },
                {
                    data: "nik",
                    name: "nik",
                },
                {
                    data: "nama",
                    name: "nama",
                },
                {
                    data: "no_ktp",
                    name: "no_ktp",
                },
                {
                    data: "alamat",
                    name: "alamat",
                },
                {
                    data: "no_telp",
                    name: "no_telp",
                },
                {
                    data: "status",
                    name: "status",
                },
            ],
        });
    }
}
