@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'Delivery List')

@section('content_header')
    <h3>Delivery List</h3>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-header bg-secondary">
                        <i class="fas fa-shopping-cart float-left"> </i>
                        <h3 class="card-title" style="font-weight: bold; margin-left:1%"> List PO
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-end g-3">

                            <!-- Nomor Dokumen -->
                            <div class="col-md-3">
                                <strong id="lastNumber">
                                    No Terakhir hari ini :
                                    <b class="text-danger">last number</b>
                                </strong>

                                <input type="text" name="l_noDok" id="l_noDok" class="form-control mt-1"
                                    placeholder="Nomor Dokumen">

                                <input type="hidden" id="l_getNoDok" name="l_getNoDok">
                            </div>

                            <!-- Ambil Nomor -->
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100 btn-flat" id="btn_ambilNomor">
                                    <i class="fas fa-hashtag"></i> Ambil Nomor
                                </button>
                            </div>

                            <!-- Cetak Dokumen -->
                            <div class="col-md-2">
                                <button class="btn btn-danger w-100 btn-flat" id="btn_cetak">
                                    <i class="fas fa-print"></i> Cetak Dokumen
                                </button>
                            </div>

                            <!-- PEMISAH -->
                            <div class="col-md-2 d-none d-md-flex justify-content-center">
                                <div class="vertical-divider"></div>
                            </div>

                            <!-- Filter -->
                            <div class="col-md-2">
                                <div class="row g-1">
                                    {{-- <div class="col-8">
                                        <input type="date" name="l_tgl" id="l_tgl" class="form-control">
                                    </div> --}}
                                    <div class="col-12">
                                        <select name="l_statusPO" id="l_statusPO" class="form-control">
                                            <option value="Open">Open</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <!-- Reload -->
                            <div class="col-md-1">
                                <button class="btn btn-primary w-100 btn-flat" id="btn_reload">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped table-hover text-nowrap table-sm"
                                width="100%" id="tb_list_po_open">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th style="width: min-content;"><input type="checkbox" id="selectAll"></th>
                                        <th>PO No</th>
                                        <th>Item Cd</th>
                                        <th>Nama</th>
                                        <th>Spesifikasi</th>
                                        <th>Qty In</th>
                                        <th>Qty Out</th>
                                        <th>Plan Qty</th>
                                        <th>Stock</th>
                                        <th>Satuan</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Nouki</th>
                                        <th>Status Send</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex align-items-center">

                        <!-- KIRI -->
                        <button type="button" class="btn btn-primary btn-flat" id="btn_proKirim" name="btn_proKirim">
                            <u style="color: white">Proses Kirim</u>
                        </button>

                        <!-- SPACER -->
                        <div class="ml-auto d-flex align-items-center gap-2">
                            <select class="form-control select2" name="select_chat_id" id="select_chat_id"
                                style="width: 200px;">
                                <option value="">Select Nama...</option>
                                @foreach ($chat_id as $c)
                                    <option value="{{ $c->chat_id }}">{{ $c->nama }}</option>
                                @endforeach
                            </select>

                            <button type="button" class="btn btn-primary btn-flat" id="btn_send_telegram"
                                name="btn_send_telegram">
                                <u style="color: white">Send Telegram</u>
                            </button>
                        </div>

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
    <script src="{{ asset('js/po_open.js') }}"></script>
@endpush
