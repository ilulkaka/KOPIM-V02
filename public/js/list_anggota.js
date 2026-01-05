$(document).ready(function () {
    getListAnggota();
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

            columnDefs: [{
                        targets: [0],
                        visible: false,
                        searchable: false
                    },
                    {
                        targets: [8],
                        data: null,
                        //defaultContent: "<button class='btn btn-success'>Complited</button>"
                        render: function(data, type, row, meta) {
                            return "<a href = '#' style='font-size:14px' class = 'ta_edit'> Edit </a>";
                        }
                    }
                ],

                columns: [{
                        data: 'id_anggota',
                        name: 'id_anggota'
                    },
                    {
                        data: 'no_barcode',
                        name: 'no_barcode'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'no_ktp',
                        name: 'no_ktp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
}
}