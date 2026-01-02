$(document).ready(function () {
    $("#tdpo_nopo").focus();
    getListAddPo();

    $("#tdpo_nopo").keypress(function (event) {
        var nopo = $("#tdpo_nopo").val();
        if (event.keyCode === 13) {
            if (nopo == "" || nopo == null) {
                infoFireAlert("warning", "Masukkan Nomor PO .");
                return false;
            } else {
                $("#tdpo_itemCd").focus();
                list_add_po.ajax.reload();
            }
        }
    });

    $("#tdpo_itemCd").keypress(function (event) {
        var itemCd = $("#tdpo_itemCd").val();
        if (event.keyCode === 13) {
            event.preventDefault();
            if (itemCd == null || itemCd == "") {
                infoFireAlert("warning", "Masukkan Item Cd .");
                return false;
            } else {
                $.ajax({
                    url: APP_BACKEND + "api/b2b/get_data_master_item/" + itemCd,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer " + key);
                    },
                    type: "get",
                    dataType: "json",
                }).done(function (resp) {
                    if (resp.success) {
                        $("#tdpo_nama").val(resp.datas.nama);
                        $("#tdpo_spesifikasi").val(resp.datas.spesifikasi);
                        $("#tdpo_satuan").val(resp.datas.satuan);

                        let harga = Number(resp.datas.harga).toLocaleString(
                            "en-US"
                        );
                        $("#tdpo_harga").val(harga);

                        $("#tdpo_qty").focus();
                    } else {
                        infoFireAlert("error", resp.message);
                        return false;
                        $("#tdpo_itemCd").focus();
                    }
                });
            }
        }
    });

    $("#tdpo_qty").keypress(function (event) {
        var qty = $("#tdpo_qty").val();
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();
        var sekarang = yyyy + "-" + mm + "-" + dd;

        if (event.keyCode === 13) {
            event.preventDefault();
            if (qty == "" || qty == null) {
                infoFireAlert("warning", "Masukkan Qty .");
                return false;
            } else {
                $("#tdpo_nouki").focus();
                $("#tdpo_nouki").val(sekarang);
            }
        }
    });

    $("#frm_tdpo").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/b2b/add_po",
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
                    list_add_po.ajax.reload(null, false);
                    onSubmit();
                    $("#tdpo_nopo").focus();
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

var list_add_po;
function getListAddPo() {
    if ($.fn.DataTable.isDataTable("#tb_list_add_po")) {
        list_add_po.ajax.reload();
    } else {
        list_add_po = $("#tb_list_add_po").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/b2b/list_add_po",
                type: "GET",
                beforeSend: function (xhr) {
                    $("#btn_reload").attr("disabled", true);
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                data: function (d) {
                    d.no_po = $("#tdpo_nopo").val();
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
                    render: function (data, type, row, meta) {
                        return "<a href = '#' style='font-size:14px' class = 'edtPO'> Edit </a>";
                    },
                },
            ],

            columns: [
                {
                    data: "id_po",
                    name: "id_po",
                },
                {
                    data: "nomor_po",
                    name: "nomor_po",
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
                    data: "qty",
                    name: "qty",
                },
                {
                    data: "satuan",
                    name: "satuan",
                },
                {
                    data: "harga",
                    name: "harga",
                    className: "text-right",
                    render: function (data) {
                        let nilai = parseInt(data) || 0;
                        return nilai.toLocaleString("id-ID");
                    },
                },
            ],
        });
    }
}

function onSubmit (){
    $("#tdpo_itemCd").val("");
    $("#tdpo_nama").val("");
    $("#tdpo_spesifikasi").val("");
    $("#tdpo_qty").val("");
    $("#tdpo_satuan").val("");
    $("#tdpo_harga").val("");
    $("#tdpo_nouki").val("");
}
