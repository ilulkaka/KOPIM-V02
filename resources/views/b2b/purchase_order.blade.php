@extends('layouts.app')
@section('plugins.Datatables', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'PO')

@section('content_header')
    <h3>Purchase Order</h3>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h4><b><i class="fas fa-cart-plus"> Purchase Order</i></b>
                    </h4>
                </div>
                <form id="frm_tdpo">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-caret-square-down"> Nomor PO</i></strong>
                                <input type="text" id="tdpo_nopo" name="tdpo_nopo" class="form-control rounded-0"
                                    required>
                            </div>
                        </div>
                        <input type="hidden" id="role" name="role" value="{{ Auth::user()->name }}">
                        <input type="hidden" id="role1" name="role1" value="{{ Auth::user()->role }}">
                        <hr>
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-caret-square-down"> Item Cd</i></strong>
                                <input type="text" id="tdpo_itemCd" name="tdpo_itemCd" class="form-control rounded-0"
                                    required>
                            </div>
                        </div>
                        <p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-caret-square-down"> Nama Barang</i></strong>
                                <input type="text" id="tdpo_nama" name="tdpo_nama" class="form-control rounded-0"
                                    readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-caret-square-down"> Spesifikasi</i></strong>
                                <input type="text" id="tdpo_spesifikasi" name="tdpo_spesifikasi"
                                    class="form-control rounded-0" readonly>
                            </div>
                        </div>
                        <p>
                        <div class="row">
                            <div class="col col-md-3">
                                <strong><i class="fas fa-caret-square-down"> Qty</i></strong>
                                <input type="number" id="tdpo_qty" name="tdpo_qty" class="form-control rounded-0"
                                    required>
                                <input type="hidden" name="tdpo_satuan" id="tdpo_satuan">
                            </div>
                            <div class="col col-md-4">
                                <strong><i class="fas fa-caret-square-down"> Harga</i></strong>
                                <input type="text" id="tdpo_harga" name="tdpo_harga" class="form-control rounded-0"
                                    readonly>
                            </div>
                            <div class="col col-md-5">
                                <strong><i class="fas fa-caret-square-down"> Nouki</i></strong>
                                <input type="date" id="tdpo_nouki" name="tdpo_nouki" class="form-control rounded-0"
                                    required>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="card-footer text-muted d-flex justify-content-end">
                        <button type="submit" id="btn_updPO"
                            class="btn btn-outline-primary rounded-0 flex-fill">Update</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="col-md-8">
            <div class="card">
                <div class="card-content">
                    <div class="card-header bg-secondary">
                        <i class="fas fa-shopping-cart float-left"> </i>
                        <h3 class="card-title" style="font-weight: bold; margin-left:1%"> List PO
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-straped table-hover text-nowrap tb-sm" width="100%"
                                id="tb_list_add_po">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>PO No</th>
                                        <th>Item Cd</th>
                                        <th>Nama</th>
                                        <th>Spesifikasi</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Harga</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="btn_excel" name="btn_excel"
                            class="form-control btn-success rounded-pill col-md-1">Excel</button>
                    </div>
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
    <script src="{{ asset('js/purchase_order.js') }}"></script>
@endpush
