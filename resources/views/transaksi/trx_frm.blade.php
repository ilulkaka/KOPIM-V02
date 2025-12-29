@extends('layouts.app')
@section('plugins.Datatables', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'Transaksi')

@section('content_header')
    <h3>Transaksi Form</h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary">
                <div class="card-header">
                    <h4><b><i class="fas fa-cart-plus"> Transaksi</i></b>
                    </h4>
                </div>
                <form id="frm_trx" autocomplete="off">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="role" name="role" value="{{ Auth::user()->name }}">
                            <input type="hidden" id="role1" name="role1" value="{{ Auth::user()->role }}">

                            @if (Auth::user()->role == 'Administrator')
                                <div class="col col-md-6">
                                    <strong><i class="fas fa-caret-square-down"> Tgl Transaksi</i></strong>
                                    <input type="date" id="tgl_trx" name="tgl_trx" class="form-control rounded-0"
                                        required>
                                </div>
                            @endif
                        </div>
                        <p></p>
                        <!-- radio -->
                        <div class="form-group">
                            <label class="form-label fw-bold mb-2">Status Anggota:</label>
                            <div class="form-check d-flex align-items-center">
                                <div class="me-4">
                                    <input class="form-check-input" type="radio" name="r1" id="r_ang"
                                        value="Anggota" checked>
                                    <label class="form-check-label" for="r_ang">Anggota</label>
                                </div>
                                <div class="custom-margin" style="margin-left: 5%">
                                    <input class="form-check-input" type="radio" name="r1" id="r_non"
                                        value="Non Anggota">
                                    <label class="form-check-label" for="r_non">Non Anggota</label>
                                </div>
                            </div>
                            <input type="hidden" name="fil" id="fil" value="">
                        </div>
                        <hr>
                        <input type="hidden" name="trx_kategori" id="trx_kategori">
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-qrcode"> No Barcode</i></strong>
                                <input type="text" id="trx_no_barcode" name="trx_no_barcode"
                                    class="form-control rounded-0" placeholder="Masukkan No Barcode ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong disabled><i class="fas fa-quote-left"> Nama</i></strong>
                                <input type="text" id="trx_nama" name="trx_nama" class="form-control rounded-0"
                                    style="font-size: 24px; font-weight:bold" placeholder="Masukkan Nama Pengguna ."
                                    required readonly>
                                <input type="hidden" name="trx_nik" id="trx_nik">
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-dollar-sign"> Nominal</i></strong>
                                <input type="text" name="trx_nominal" id="trx_nominal"
                                    class="number-separator form-control form-control-lg rounded-0"
                                    placeholder="Masukkan Nominal..."
                                    style="font-size: 30px; color:blue; font-weight: bold " required>
                            </div>
                            <div class="col col-md-6">
                                <br>
                                <button type="button" class="btn btn-app btn-outline float-right" id="btn_cancel_trx">
                                    <i class="far fa-window-close"></i> Cancel</button>
                                <button type="submit" class="btn btn-app btn-success float-right" id="btn_simpan_trx">
                                    <i class="fas fa-save"></i> Save</button>
                                {{-- <button type="button" class="btn btn-danger btn-flat float-right"
                                    id="btn_cancel_trx">Cancel</button> --}}
                                {{-- <button type="button" class="btn btn-success btn-flat float-right mr-2"
                                    id="btn_simpan_trx">Simpan</button> --}}
                            </div>

                        </div>
                        <p></p>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline btn-flat float-right" id="btn_detail_trx"
                    style="color: blue"><u> Detail
                        Trx</u></button>
                <button type="button" class="btn btn-outline btn-flat float-right" id="btn_download_trx"
                    style="color: blue"><u>Download
                        Trx</u></button>
            </div>
        </div>

        <div class="col-md-5">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><u> Transaksi Hari ini</u></span>
                    <div class="row">
                        <div class="col col-md-4">
                            <span class="info-box-number">Anggota</span>
                            <span class="info-box-number" id="hasil_anggota">0</span>
                        </div>
                        <div class="col col-md-4">
                            <span class="info-box-number">Umum</span>
                            <span class="info-box-number" id="hasil_umum">0</span>
                        </div>
                    </div>
                </div>
                <!-- /.info-box-content d -->
            </div>
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><u> Total Transaksi</u></span>
                    <span class="info-box-number"><b id="hasil_total">0</b>
                        <!-- /.info-box-content -->
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi (DT) -->
    <div class="modal fade" id="modal_detail_trx" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-cart"> Detail
                                Transaksi</i></b> </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-md-5">
                            <strong> From</strong>
                            <input type="date" class="form-control rounded-0" id="tgl_awal"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col col-md-5">
                            <strong> End</strong>
                            <input type="date" class="form-control rounded-0" id="tgl_akhir"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col col-md-2">
                            <strong> Refs</strong>
                            <button class="btn btn-primary rounded-pill col-md-12 " id="btn_reload"><i
                                    class="fa fa-sync"></i></button>
                        </div>
                    </div>
                    <br>

                    <div class="table-responsive">
                        <table id="tb_detail_trx"
                            class="table table-bordered table-stripped table-hover text-nowrap table-sm dataTable"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Tanggal Trx</th>
                                    <th>No Barcode</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Act</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" style="text-align:center; ">TOTAL</th>
                                    <th style="text-align:center; font-size: large;">TOTAL</th>
                                    <th style="text-align:center; font-size: large;"></th>
                                    <th style="text-align:center; font-size: large;"></th>
                                    <th style="text-align:center; font-size: large;"></th>
                                    <th style="text-align:center; font-size: large;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Download Transaksi (DoT) -->
    <div class="modal fade" id="modal_download_trx" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-cart"> Download
                                Transaksi</i></b> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-md-4">
                            <strong><i class="fas fa-caret-square-down"> Format</i></strong>
                            <select id="dot_format" name="dot_format" class="form-control rounded-0" required>
                                <option value="">Kategori ...</option>
                                <option value="Excel">Excel</option>
                                <option value="Pdf">Pdf</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-md-6">
                            <strong><i class="fas fa-date"> Beginning</i></strong>
                            <input type="date" id="tgl_awal1" name="tgl_awal1" class="form-control rounded-0"
                                required>
                        </div>

                        <div class="col col-md-6">
                            <strong><i class="fas fa-date"> Ending</i></strong>
                            <input type="date" id="tgl_akhir1" name="tgl_akhir1" class="form-control rounded-0"
                                required>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="row">

                            <button type="button" id="btn_preview" name="btn_preview"
                                class="form-control col-md-4 rounded-pill">Preview</button>
                            <button type="button" id="btn_download" name="btn_download"
                                class="form-control col-md-4 rounded-pill"> Download </button>
                            <button type="button" data-dismiss="modal" class="form-control col-md-4 rounded-pill">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Transaksi (ET) -->
    <div class="modal fade" id="modal_edit_trx" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title" id="exampleModalLongTitle"><b>Edit Transaksi</b> </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frm_edt_trx">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-file-prescription"> Tgl Transaksi</i></strong>
                                <input type="hidden" name="et_id_anggota" id="et_id_anggota">
                                <input type="hidden" name="et_id" id="et_id">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                                <input type="text" id="et_tgl" name="et_tgl" class="form-control rounded-0"
                                    disabled>
                                <p>
                                </p>
                                <strong padding-top="20%"><i class="fas fa-file-signature"> No Barcode</i>
                                </strong>
                                <input type="text" id="et_no" name="et_no"
                                    class="form-control rounded-0 col-md-12" disabled>
                            </div>
                            <div class="col col-md-6">
                                <strong><i class="fas fa-location-arrow"> Nama</i></strong>
                                <input type="text" id="et_nama" name="et_nama" class="form-control rounded-0"
                                    disabled>
                                <p></p>
                                <strong padding-top="20%"><i class="fas fa-file-signature"> Nominal</i>
                                </strong>
                                <input type="number" id="et_nominal" name="et_nominal"
                                    class="form-control rounded-0 col-md-12"
                                    style="font-size: 24px; color:red; font-weight:bold">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_save_et">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sending Mail (SM) -->
    <div class="modal fade" id="modal_sending_mail" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fa fa-qrcode"> Sending
                                Mail</i></b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_sm">
                        @csrf
                        <input type="hidden" id="m_tgl_awal" name="m_tgl_awal" class="form-control rounded-0">
                        <input type="hidden" id="m_tgl_akhir" name="m_tgl_akhir" class="form-control rounded-0">
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="col col-md-12">
                                    <h10>Send To :</h10>
                                </div>
                                <div class="form-group">
                                    <input type="mail" id="sm_to" name="sm_to" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="col col-md-12">
                                    <h10>CC :</h10>
                                </div>
                                <div class="form-group">
                                    <input type="mail" id="sm_cc" name="sm_cc" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <p></p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat" id="btn_send">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Push extra CSS --}}
@push('css')
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet">
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script src="{{ asset('js/trx_frm.js') }}"></script>
    <script src="{{ asset('js/easy-number-separator.js') }}"></script>
@endpush
