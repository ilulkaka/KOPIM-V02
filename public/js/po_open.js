$(document).ready(function () {
    getListPoOpen();

    $("#btn_ambilNomor").click(function () {
        $.ajax({
            url: APP_BACKEND + "api/b2b/get_no_dokumen",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "get",
            dataType: "json",
        })
            .done(function (resp) {
                if (resp.success) {
                    $("#l_noDok").val(resp.new_dok_nomor);
                } else {
                    infoFireAlert(resp.message);
                }
            })
            .fail(function (xhr, status, error) {
                alert("Terjadi kesalahan saat mengirim data.");
            });
    });

    // Event listener untuk pemilihan dan pembatalan pemilihan baris
    $("#tb_list_po_open").on("select.dt", function (e, dt, type, indexes) {
        var row = dt.rows(indexes).nodes().to$();
        row.addClass("selected-row");
    });

    $("#tb_list_po_open").on("deselect.dt", function (e, dt, type, indexes) {
        var row = dt.rows(indexes).nodes().to$();
        row.removeClass("selected-row");
    });

    // Event listener untuk checkbox di header
    $("#selectAll").change(function () {
        var checked = this.checked;
        $(".row-select-checkbox").prop("checked", checked);
        list_po_open.rows().select(checked);
    });

    // Event listener untuk checkbox di setiap baris
    $("#tb_selectRow").on("change", ".row-select-checkbox", function () {
        var allChecked =
            $(".row-select-checkbox:checked").length ===
            list_po_open.rows().count();
        $("#selectAll").prop("checked", allChecked);
    });
});

var list_po_open;
function getListPoOpen() {
    if ($.fn.DataTable.isDataTable("#tb_list_po_open")) {
        list_po_open.ajax.reload();
    } else {
        list_po_open = $("#tb_list_po_open").DataTable({
            // destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            autoWidth: false,
            ajax: {
                url: APP_BACKEND + "api/b2b/list_po_open",
                type: "GET",
                beforeSend: function (xhr) {
                    $("#btn_reload").attr("disabled", true);
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                data: function (d) {
                    d.statusPO = $("#l_statusPO").val();
                    d.f_tgl = $("#l_tgl").val();
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
                    targets: [14],
                    data: null,
                    //defaultContent: "<button class='btn btn-success'>Complited</button>"
                    render: function (data, type, row, meta) {
                        if (data.status_po == "Closed") {
                            return "<a href = '#' style='font-size:14px' class = 'inq_detail'> Detail </a>";
                        } else {
                            return "";
                        }
                    },
                },
                {
                    targets: 6, // Index kolom qty_plan
                    width: "50px", // Sesuaikan lebar sesuai kebutuhan
                },
                {
                    targets: 7,
                    width: "50px",
                },
                {
                    targets: 8,
                    width: "50px",
                },
                {
                    targets: 9,
                    width: "50px",
                },
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets: 1,
                },
            ],

            select: {
                style: "multi",
                selector: "td:first-child",
            },
            order: [[1, "asc"]],

            columns: [
                {
                    data: "id_po",
                    name: "id_po",
                },
                {
                    data: null,
                    defaultContent: "",
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
                    data: "qty_out",
                    name: "qty_out",
                    render: function (data, type, row) {
                        return data ? data : 0; // Mengatur default menjadi 0 jika null/undefined
                    },
                },
                {
                    data: "temp_plan",
                    name: "temp_plan",
                    render: function (data, type, row, meta) {
                        if (type === "display") {
                            return (
                                '<div contenteditable="true" class="form-control plan-input" data-id="' +
                                (row.id_po || "") +
                                '" data-qty="' +
                                (row.qty || 0) +
                                '">' +
                                (data || row.qty || 0) +
                                "</div>"
                            );
                        }
                        return data;
                    },
                },
                {
                    data: "stock",
                    name: "stock",
                },
                {
                    data: "satuan",
                    name: "satuan",
                },
                {
                    data: "harga",
                    name: "harga",
                    render: function (data, type, row) {
                        if (!data) return 0; // Mengatur default menjadi 0 jika null/undefined
                        return new Intl.NumberFormat("id-ID", {
                            style: "decimal",
                            minimumFractionDigits: 0,
                        }).format(data);
                    },
                },
                {
                    data: "total",
                    name: "total",
                    render: function (data, type, row) {
                        if (!data) return 0; // Mengatur default menjadi 0 jika null/undefined
                        return new Intl.NumberFormat("id-ID", {
                            style: "decimal",
                            minimumFractionDigits: 0,
                        }).format(data);
                    },
                },
                {
                    data: "nouki",
                    name: "nouki",
                    render: function (data, type, row) {
                        if (data) {
                            var date = new Date(data);
                            var day = date
                                .getDate()
                                .toString()
                                .padStart(2, "0"); // Tanggal (2 digit)
                            var month = (date.getMonth() + 1)
                                .toString()
                                .padStart(2, "0"); // Bulan (2 digit)
                            var year = date.getFullYear(); // Tahun (4 digit)
                            return day + "-" + month + "-" + year;
                        }
                        return ""; // Jika data kosong
                    },
                },
            ],
        });
    }
}
