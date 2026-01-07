@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'List Anggota')

@section('content_header')
    <h3>List Anggota</h3>
@endsection

@section('content')


    <div class="card">
        <div class="card-content">
            <div class="card-header">
                <h3 class="card-title"><u>List Anggota</u></h3>
            </div>
            <div class="card-body">
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">

                    <table class="table table-bordered table-striped table-hover text-nowrap" id="tb_list_anggota">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>No Barcode</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>No KTP</th>
                                <th>Alamat</th>
                                <th>No Telp</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary btn-flat" id="btn_tambah" name="btn_tambah">
                    <i class="fas fa-user-plus">
                        Tambah
                        Anggota</i>
                </button>
                <button type="button" class="btn btn-primary btn-flat" id="btn_print" name="btn_print">
                    <i class="fa fa-qrcode"> Print QR</i>
                </button>
            </div>
        </div>
        <!-- /.card -->
    </div>

    <!-- Modal Tambah Anggota (TA) -->
    <div class="modal fade" id="modal_tambah_anggota" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-user-plus"> Tambah
                                Anggota</i></b> </h5>
                </div>
                <form id="frm_ta">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-file-prescription"> NIK</i></strong>
                                <input type="text" id="ta_nik" name="ta_nik" class="form-control rounded-0"
                                    placeholder="NIK Perusahaan" required>
                            </div>
                            <div class="col col-md-6">
                                <strong padding-top="20%"><i class="fas fa-file-signature"> No KTP</i>
                                </strong>
                                <input type="text" id="ta_noktp" name="ta_noktp"
                                    class="form-control rounded-0 col-md-12" placeholder="Masukkan Nomer KTP" required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-quote-left"> Nama</i></strong>
                                <input type="text" id="ta_nama" name="ta_nama" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Anda ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-location-arrow"> alamat</i></strong>
                                <textarea name="ta_alamat" id="ta_alamat" class="form-control rounded-0" cols="30" rows="2" required></textarea>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-phone"> No Telp</i></strong>
                                <input type="tel" id="ta_notelp" name="ta_notelp" class="form-control rounded-0"
                                    placeholder="0811-2453-6789" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_save_ta">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Anggota (EA) -->
    <div class="modal fade" id="modal_edit_anggota" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-user-edit"> Edit Data
                                Anggota</i></b> </h5>
                </div>
                <form id="frm_ea">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-file-prescription"> NIK</i></strong>
                                <input type="text" id="ea_nik" name="ea_nik" class="form-control rounded-0"
                                    placeholder="NIK Perusahaan" required>
                                <input type="hidden" id="ea_id_anggota" name="ea_id_anggota">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                            </div>
                            <div class="col col-md-6">
                                <strong padding-top="20%"><i class="fas fa-file-signature"> No KTP</i>
                                </strong>
                                <input type="text" id="ea_noktp" name="ea_noktp"
                                    class="form-control rounded-0 col-md-12" placeholder="Masukkan Nomer KTP" required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-quote-left"> Nama</i></strong>
                                <input type="text" id="ea_nama" name="ea_nama" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Anda ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-location-arrow"> alamat</i></strong>
                                <textarea name="ea_alamat" id="ea_alamat" class="form-control rounded-0" cols="30" rows="2" required></textarea>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-phone"> No Telp</i></strong>
                                <input type="tel" id="ea_notelp" name="ea_notelp" class="form-control rounded-0"
                                    placeholder="0811-2453-6789" required>
                            </div>
                            <div class="col col-md-6">
                                <strong><i class="fas fa-adjust"> Status</i></strong>
                                <select name="ea_status" id="ea_status" class="form-control rounded-0" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Off">Off </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_save_ea">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Print QR (PQ) -->
    <div class="modal fade" id="modal_print_qr" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fa fa-qrcode"> Source</i></b> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_pq" method="post" action="{{ url('master/frm_printQR') }}" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="col col-md-12">
                                    <h10>Select multiple Member</h10>
                                </div>
                                <div class="form-group">
                                    <div class="select2-purple">
                                        <select class="select2 select2-hidden-accessible" multiple=""
                                            data-placeholder="Select a Member" data-dropdown-css-class="select2-purple"
                                            style="width: 100%;" data-select2-id="15" tabindex="-1" aria-hidden="true"
                                            name="pq_anggota[]" id="pq_anggota[]" required>
                                            {{-- @foreach ($anggota as $agg)
                                                <option value="{{ $agg->id_anggota }}">{{ $agg->nama }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p></p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat" id="btn_save_tp">Print</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Push extra CSS --}}
@push('css')
    {{-- <link href="{{ asset('css/select2.css') }}" rel="stylesheet"> --}}
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script src="{{ asset('js/list_anggota.js') }}"></script>
@endpush
