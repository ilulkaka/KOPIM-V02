$(document).ready(function () {
    getListPoOpen();

    $("#btn_ambilNomor").click(function () {
        getNomorDokumen();
    });

    $("#btn_reload").click(function () {
        list_po_open.ajax.reload();
    });

    var newPlan = {};
    $("#tb_list_po_open").on(
        "blur",
        '.plan-input[contenteditable="true"]',
        function () {
            var $this = $(this);
            var id = $this.data("id"); // Ambil ID
            var maxQty = parseFloat($this.data("qty")); // Ambil nilai maksimal dari data-qty
            var newPlanQty = parseFloat($this.text().trim()); // Ambil nilai input

            if (!id) {
                console.error("ID tidak ditemukan untuk elemen ini.");
                return;
            }

            list_po_open
                .rows()
                .data()
                .each(function (row) {
                    if (!newPlan[row.id_po]) {
                        newPlan[row.id_po] = {
                            temp_plan: row.temp_plan || row.qty || 0, // Ambil `temp_plan` atau default `qty`
                        };
                    }
                });

            // Validasi nilai
            if (isNaN(newPlanQty) || newPlanQty < 0) {
                infoFireAlert("warning", "Nilai tidak boleh kurang dari 0.");
                $this.text(0); // Reset ke 0 jika invalid
                newPlanQty = 0;
            } else if (newPlanQty > maxQty) {
                infoFireAlert(
                    "warning",
                    "Nilai tidak boleh lebih besar dari qty (" + maxQty + ")."
                );
                $this.text(maxQty); // Reset ke nilai maksimal
                newPlanQty = maxQty;
            }

            // Perbarui objek `newPlan`
            if (!newPlan[id]) {
                newPlan[id] = {};
            }

            newPlan[id].temp_plan = newPlanQty;
        }
    );

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

    $("#btn_proKirim").on("click", function () {
        var noDok = $("#l_noDok").val();

        if (noDok == null || noDok == "") {
            infoFireAlert("warning", "Ambil Nomor Dokumen terlebih dahulu .");
        } else {
            var selectedRows = list_po_open
                .rows({ selected: true })
                .data()
                .toArray();

            var selectedIDs = selectedRows.map(function (row) {
                // Konversi nilai ke angka
                var planQty = Number(
                    newPlan[row.id_po] && newPlan[row.id_po].temp_plan
                        ? newPlan[row.id_po].temp_plan
                        : row.temp_plan
                ); // Gunakan nilai asli jika belum diperbarui

                var qtyOut = Number(row.qty_out); // Konversi ke angka

                var qtyOutTotal = qtyOut + planQty; // Penjumlahan angka
                var sisa = row.qty - qtyOutTotal;

                return {
                    id: row.id_po,
                    plan_qty: planQty,
                    sisa: sisa,
                    status: row.status_po,
                };
            });

            if (selectedIDs.length === 0) {
                infoFireAlert("warning", "Record tidak ada yang dipilih.");
                return;
            }

            // Periksa apakah semua baris yang dipilih memiliki status "Open"
            var allStatus = selectedIDs.every(function (row) {
                return row.status === "Open";
            });

            if (!allStatus) {
                infoFireAlert("error", "Status PO Harus Open.");
                return;
            }

            $.ajax({
                url: APP_BACKEND + "api/b2b/upd_kirim_po",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + key);
                },
                type: "patch",
                dataType: "json",
                data: {
                    selectedIDs: selectedIDs,
                    noDokumen: noDok,
                },
            })
                .done(function (resp) {
                    if (resp.success) {
                        fireAlert("success", resp.message);
                        list_po_open.ajax.reload(null, false); // Refresh DataTable
                        $("#l_noDok").val("");
                    } else {
                        infoFireAlert("error", resp.message);
                    }
                })
                .fail(function (xhr, status, error) {
                    infoFireAlert(
                        "error",
                        "Terjadi kesalahan saat mengirim data."
                    );
                });
        }
    });

    $("#btn_send_telegram").on("click", function () {
        var chatId = $("#select_chat_id").val();
        var selectedRows = list_po_open
            .rows({ selected: true })
            .data()
            .toArray();

        var selectedIDs = selectedRows.map(function (row) {
            // Konversi nilai ke angka
            var planQty = Number(
                newPlan[row.id_po] && newPlan[row.id_po].temp_plan
                    ? newPlan[row.id_po].temp_plan
                    : row.qty
            ); // Gunakan nilai asli jika belum diperbarui

            return {
                id: row.id_po,
                plan_qty: planQty,
                status: row.status_po,
                send_to: row.send_to,
            };
        });

        if (selectedIDs.length === 0) {
            infoFireAlert("warning", "Record tidak ada yang dipilih.");
            return;
        }

        if (chatId == null || chatId == "") {
            infoFireAlert("warning", "Pilih Nama.");
            return;
        }

        // Periksa apakah semua baris yang dipilih memiliki status chat "Null"
        var sudahDikirim = selectedIDs.some(function (row) {
            return row.send_to !== null && row.send_to !== "";
        });

        if (sudahDikirim) {
            infoFireAlert(
                "warning",
                "Data sudah pernah dikirim, tidak boleh kirim ulang!"
            );
            return;
        }

        $.ajax({
            url: APP_BACKEND + "api/b2b/krm_po_telegram",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + key);
            },
            type: "post",
            dataType: "json",
            data: {
                selectedIDs: selectedIDs,
                chatId: chatId,
            },
        })
            .done(function (resp) {
                if (resp.success) {
                    fireAlert("success", resp.message);
                    list_po_open.ajax.reload(null, false); // Refresh DataTable
                    $("#select_chat_id").val("");
                } else {
                    infoFireAlert("error", resp.message);
                }
            })
            .fail(function (xhr, status, error) {
                infoFireAlert("error", "Terjadi kesalahan saat mengirim data.");
            });
    });

    $("#btn_cetak").click(function () {
        var noDok = $("#l_noDok").val();

        if (!noDok) {
            getNomorDokumen().then(function (res) {
                $("#l_noDok").val(res.noDok);
            });
        } else {
            alert(noDok);
        }
        // Membuka kedua tab secara langsung menggunakan variabel
        var tab1 = window.open(APP_URL + "/b2b/cetak_inv/" + noDok, "_blank");
        var tab2 = window.open(APP_URL + "/b2b/cetak_sj/" + noDok, "_blank");

        // Pastikan tab tidak null (dibuka)
        if (!tab1 || !tab2) {
            infoFireAlert(
                "error",
                "Pop-up blocker terdeteksi! Harap izinkan pop-up di browser Anda."
            );
        }
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
                    targets: [15],
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
                {
                    data: "chat_name",
                    name: "chat_name",
                },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.send_to != null && data.send_to != "") {
                    $(row).css("color", "blue");
                }
            },
        });
    }
}

function getNomorDokumen() {
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
}
