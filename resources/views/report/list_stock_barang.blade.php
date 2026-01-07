@extends('layouts.app')
@section('plugins.Datatables', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'List Stock Barang')

@section('content_header')
    <h3>List Stock Barang</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-content">
            <div class="card-header bg-secondary">
                <i class="fas fa-shopping-cart float-left"> </i>
                <h3 class="card-title" style="font-weight: bold; margin-left:1%"> List Stock Barang
                </h3>
            </div>

            <div class="modal-body">
                <div class="row">
                    <label style="margin-left: 10px">Tanggal End Stock : </label>
                    <input type="date" name="endDate" id="endDate" value="{{ date('Y-m-d') }}"
                        class="form-control col-md-2 rounded-0 ml-2">
                </div>
                <hr>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped table-hover text-nowrap table-sm"
                        id="tb_list_stock_barang">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Spesifikasi</th>
                                <!-- <th>Supplier</th> -->
                                <th>In</th>
                                <th>Out</th>
                                <th>Stock Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button id="btn_bm" name="btn_bm" class="btn btn-outline btn-default col-md-2 btn-flat">Detail Barang
                    Masuk</button>
                <button id="btn_bk" name="btn_bk" class="btn btn-outline btn-default col-md-2 btn-flat">Detail Barang
                    Keluar</button>
                <button id="btn_excel" name="btn_excel" class="btn btn-outline btn-success col-md-2 btn-flat">Excel</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Stock (TS) -->
    <div class="modal fade" id="modal_tambah_stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-plus"> Tambah
                                Stock</i></b> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frm_ts">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-12">
                                <strong> Tanggal Masuk</strong>
                                <input type="date" name="ts_tglmsk" id="ts_tglmsk" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Barang ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-9">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                                <strong> Kode Barang</strong>
                                <input type="hidden" id="ts_kode" name="ts_kode" class="form-control rounded-0"
                                    placeholder="Masukkan Kode Barang ." required>
                                <input type="text" id="ts_kode1" name="ts_kode1" class="form-control rounded-0"
                                    placeholder="Masukkan Kode Barang ." required disabled>
                            </div>
                            <div class="col col-md-3">
                                <strong> Qty In</strong>
                                <input type="text" id="ts_qty" name="ts_qty" class="form-control rounded-0"
                                    placeholder="Qty In ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat" id="btn_simpan_ts">Simpan</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Kurang Stock (KS) -->
    <div class="modal fade" id="modal_kurang_stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-minus"> Kurang
                                Stock</i></b> </h5>
                </div>
                <form id="frm_ks">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-12">
                                <input type="hidden" id="ks_stock" name="ks_stock">
                                <strong> Tanggal Keluar</strong>
                                <input type="date" name="ks_tglklr" id="ks_tglklr" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Barang ." required>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-9">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                                <strong> Kode Barang</strong>
                                <input type="hidden" id="ks_kode" name="ks_kode" class="form-control rounded-0"
                                    placeholder="Masukkan Kode Barang ." required>
                                <input type="text" id="ks_kode1" name="ks_kode1" class="form-control rounded-0"
                                    placeholder="Masukkan Kode Barang ." required disabled>
                            </div>
                            <div class="col col-md-3">
                                <strong> Qty Out</strong>
                                <input type="text" id="ks_qty" name="ks_qty" class="form-control rounded-0"
                                    placeholder="Qty Out ." required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_simpan_ks">Simpan</button>
                    </div>
                </form>
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
    <script src="{{ asset('js/list_stock_barang.js') }}"></script>
@endpush
