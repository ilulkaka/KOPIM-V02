$(document).ready(function () {
    easyNumberSeparator({
        selector: ".number-separator",
        separator: ",",
    });

    $("#tmpo_harga1").on("input", function () {
        $("#tmpo_harga").val(this.value.replace(/,/g, ""));
    });

    getListMasterItemB2b();

    $("#btn_tambah").click(function () {
        $("#modal_tmpo").modal("show");
        $("#frm_tmpo").trigger("reset");
        $("#tmpo_itemCd").focus();
    });

    $("#frm_tmpo").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/b2b/add_master_item",
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
                    $("#modal_tmpo").modal("toggle");
                    list_master_item_b2b.ajax.reload(null, false);
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

    $("#tb_master_item_b2b").on("click", ".edtMasterItem", function () {
        var data = list_master_item_b2b.row($(this).parents("tr")).data();
        $("#empo_id").val(data.id_master_po);
        $("#empo_itemCd").val(data.item_cd);
        $("#empo_nama").val(data.nama);
        $("#empo_spesifikasi").val(data.spesifikasi);
        $("#empo_satuan").val(data.satuan);
        let harga = Number(data.harga).toLocaleString("en-US");
        $("#empo_harga1").val(harga);
        $("#empo_level").val(data.role);
        $("#modal_empo").modal("show");
    });

    $("#empo_harga1").on("input", function () {
        $("#empo_harga").val(this.value.replace(/,/g, ""));
    });

    $("#frm_empo").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/b2b/edt_master_item",
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
                    $("#modal_empo").modal("toggle");
                    list_master_item_b2b.ajax.reload(null, false);
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

var list_master_item_b2b;
function getListMasterItemB2b() {
    if ($.fn.DataTable.isDataTable("#tb_master_item_b2b")) {
        list_master_item_b2b.ajax.reload();
    } else {
        list_master_item_b2b = $("#tb_master_item_b2b").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/b2b/list_master_item",
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
                    targets: [6],
                    data: null,
                    //defaultContent: "<button class='btn btn-success'>Complited</button>"
                    render: function (data, type, row, meta) {
                        return "<a href = '#' style='font-size:14px' class = 'edtMasterItem'> Edit </a>";
                    },
                },
            ],

            columns: [
                {
                    data: "id_master_po",
                    name: "id_master_po",
                },
                {
                    data: "item_cd",
                    name: "item_cd",
                },
                {
                    data: "nama",
                    name: "nama",
                },
                {
                    data: "spesifikasi",
                    name: "spesifikasi",
                },
                {
                    data: "harga",
                    name: "harga",
                    className: "text-right",
                    render: function (data) {
                        let nilai = parseInt(data) || 0;
                        return "Rp " + nilai.toLocaleString("id-ID");
                    },
                },
                {
                    data: "satuan",
                    name: "satuan",
                },
            ],
        });
    }
}
