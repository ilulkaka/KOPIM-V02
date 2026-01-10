$(document).ready(function () {
    getListInvSj();

    $("#tb_list_inv_sj").on("click", ".inq_sj", function () {
        var data = list_inv_sj.row($(this).parents("tr")).data();
        window.open(APP_URL + "/b2b/cetak_sj/" + data.no_dokumen, "_blank");
    });

    $("#tb_list_inv_sj").on("click", ".inq_inv", function () {
        var data = list_inv_sj.row($(this).parents("tr")).data();
        window.open(APP_URL + "/b2b/cetak_inv/" + data.no_dokumen, "_blank");
    });
});

var list_inv_sj;
function getListInvSj() {
    if ($.fn.DataTable.isDataTable("#tb_list_inv_sj")) {
        list_inv_sj.ajax.reload();
    } else {
        list_inv_sj = $("#tb_list_inv_sj").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/b2b/list_inv_sj",
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
                    targets: [2],
                    data: null,
                    render: function (data, type, row, meta) {
                        return "<a href = '#' style='font-size:14px' class = 'inq_sj'> Surat Jalan </a>  ||  <a href = '#' style='font-size:14px' class = 'inq_inv'> Invoice </a>";
                    },
                },
            ],

            columns: [
                {
                    data: "tgl_kirim",
                    name: "tgl_kirim",
                },
                {
                    data: "no_dokumen",
                    name: "no_dokumen",
                },
            ],
        });
    }
}
