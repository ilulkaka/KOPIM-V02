@extends('layouts.app')
@section('plugins.Datatables', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'List Inv Sj')

@section('content_header')
    <h3>List Inv Sj</h3>
@endsection

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-content">
                <div class="card-header bg-secondary">
                    <i class="fas fa-shopping-cart float-left"> </i>
                    <h3 class="card-title" style="font-weight: bold; margin-left:1%"> List No Dokumen
                    </h3>
                </div>

                <div class="modal-body">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-striped table-hover text-nowrap table-sm" width="100%"
                            id="tb_list_inv_sj">
                            <thead>
                                <tr>
                                    <th>Tanggal Kirim</th>
                                    <th>No Dokumen</th>
                                    <th>Cetak</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
    <script src="{{ asset('js/list_inv_sj.js') }}"></script>
@endpush
