$(document).ready(function () {
    getListStockBarang();

    $("#endDate").change(function () {
        list_stock_barang.ajax.reload();
    });

    $("#tb_list_stock_barang").on("click", ".ts_tambah", function () {
        var data = list_stock_barang.row($(this).parents("tr")).data();
        $("#ts_kode").val(data.item_cd);
        $("#ts_kode1").val(data.item_cd);
        get_ts();
        $("#modal_tambah_stock").modal("show");
    });

    $("#frm_ts").on("submit", function (e) {
        e.preventDefault();
        var datas = $(this).serialize();

        $.ajax({
            url: APP_BACKEND + "api/report/add_stock_barang",
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
                    $("#modal_tambah_stock").modal("toggle");
                    list_stock_barang.ajax.reload(null, false);
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

    $("#tb_stock").on("click", ".ts_minus", function () {
        infoFireAlert("warning", "Stock 0 ");
    });

    $("#tb_list_stock_barang").on("click", ".ts_kurang", function () {
        var data = list_stock_barang.row($(this).parents("tr")).data();
        $("#ks_kode").val(data.item_cd);
        $("#ks_kode1").val(data.item_cd);
        $("#ks_stock").val(data.stock);
        get_ks();
        $("#modal_kurang_stock").modal("show");
    });
});

var list_stock_barang;
function getListStockBarang() {
    if ($.fn.DataTable.isDataTable("#tb_list_stock_barang")) {
        list_stock_barang.ajax.reload();
    } else {
        list_stock_barang = $("#tb_list_stock_barang").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/report/list_stock_barang",
                type: "GET",
                beforeSend: function (xhr) {
                    $("#btn_reload").attr("disabled", true);
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                data: function (d) {
                    d.endDate = $("#endDate").val();
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
                    visible: true,
                    searchable: true,
                },
                {
                    targets: [6],
                    data: null,
                    //defaultContent: "<button class='btn btn-success'>Complited</button>"
                    render: function (data, type, row, meta) {
                        if (data.stock <= 0) {
                            return "<a href = '#' style='font-size:14px' class = 'ts_tambah'> Tambah </a> || <a href = '#' style='font-size:14px' class = 'ts_minus' enabled> Kurang </a>";
                        } else {
                            return "<a href = '#' style='font-size:14px' class = 'ts_tambah'> Tambah </a> || <a href = '#' style='font-size:14px' class = 'ts_kurang'> Kurang </a>";
                        }
                    },
                },
            ],

            columns: [
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
                // {
                //     data: 'supplier',
                //     name: 'supplier'
                // },
                {
                    data: "qty_in",
                    name: "qty_in",
                    /*render: function(data, type, row, meta) {
                            if (data > 0) {
                                return "<a href='' class='detailIn'>" + data + "</a>";
                            } else {
                                return data;
                            }
                        }*/
                },
                {
                    data: "qty_out",
                    name: "qty_out",
                },
                {
                    data: "stock",
                    name: "stock",
                },
            ],
        });
    }
}

function get_ts() {
    $("#ts_tglmsk").val("");
    $("#ts_qty").val("");
}

function get_ks() {
    $("#ks_tglmsk").val("");
    $("#ks_qty").val("");
}
