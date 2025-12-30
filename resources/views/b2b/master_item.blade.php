@extends('layouts.app')
@section('plugins.Datatables', true)
{{-- @extends('adminlte::page') --}}

@section('title', 'Master Item')

@section('content_header')
    <h3>Master Item</h3>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title" id="exampleModalLongTitle"><i class="fab fa-medrt"></i> List Master Item
                        </h5>
                    </div>
                    {{-- <div class="card-body">
                        <div class="row" style="margin-top: -1%; margin-left: 3px">
                            <div class="col col-md-2 g-2">
                                <strong>Start Tgl Laporan</strong>
                                <input type="date" name="tgl_awal" id="tgl_awal" value="{{ date('Y-m') . '-01' }}"
                                    class="form-control rounded-0">
                                </select>
                            </div>
                            <div class="col col-md-2 g-2">
                                <strong>End Tgl Laporan</strong>
                                <input type="date" name="tgl_akhir" id="tgl_akhir" value="{{ date('Y-m-d') }}"
                                    class="form-control rounded-0">
                                </select>
                            </div>
                            <div class="col col-md-2 g-2">
                                <strong>Status :</strong>
                                <select name="status_hk" id="status_hk" class="form-control rounded-0 select2" required>
                                    <option value="All">All</option>
                                    <option value="Open">Open</option>
                                    <option value="Close">Close</option>
                                </select>
                            </div>
                            <div class="col col-md-2 g-2">
                                <strong>Jenis :</strong>
                                <select name="jenis_hk" id="jenis_hk" class="form-control rounded-0 select2" required>
                                    <option value="All">All</option>
                                    <option value="HH">Hiyari Hatto</option>
                                    <option value="KY">Kiken Youchi</option>
                                    <option value="AP">Anzen Patrol</option>
                                    <option value="SM">Small Meeting</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end g-2">
                                <button class="btn btn-cari w-100" id="btn_reload" name="btn_reload">
                                    <i class="fa fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div> 
                    <hr style="margin-top: -5px"> --}}
                    <div class="card-body" style="margin-top: -10px">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped table-hover text-nowrap table-sm"
                                id="tb_master_item_b2b">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Item Cd</th>
                                        <th>Nama</th>
                                        <th>Spesifikasi</th>
                                        <th>Harga</th>
                                        <th>Uom</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id="btn_tambah"
                                name="btn_tambah">
                                <b>Tambah Master Item</b>
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id=""
                                name="">
                                <b></b>
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id=""
                                name="">
                                <b></b>
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id=""
                                name="">
                                <b></b>
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id=""
                                name="">
                                <b></b>
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-0 flex-fill" id=""
                                name="">
                                <b></b>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Master PO (TMPO) -->
    <div class="modal fade" id="modal_tmpo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fab fa-opencart"> Tambah
                                Master PO</i></b> </h5>
                </div>
                <form id="frm_tmpo">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                                <strong> Item Cd</strong>
                                <input type="text" id="tmpo_itemCd" name="tmpo_itemCd" style="text-transform: uppercase;"
                                    class="form-control rounded-0" placeholder="Masukkan Kode Barang ." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong> Nama Barang</strong>
                                <input name="tmpo_nama" id="tmpo_nama" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Barang ." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong> Spesifikasi</strong>
                                <input type="type" id="tmpo_spesifikasi" name="tmpo_spesifikasi"
                                    class="form-control rounded-0" placeholder="Masukkan Spesifikasi Barang .">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-8">
                                <strong> Harga</strong>
                                <input type="type" id="tmpo_harga1" name="tmpo_harga1"
                                    style="font-size: 18px; color:blue; font-weight:bold"
                                    class="number-separator form-control rounded-0" placeholder="Masukkan Harga Barang ."
                                    required>
                                <input type="hidden" name="tmpo_harga" id="tmpo_harga">
                            </div>
                            <div class="col col-md-4">
                                <strong> Uom</strong>
                                <select name="tmpo_uom" id="tmpo_uom" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Psg">Pasang</option>
                                    <option value="Set">Set</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Btl">Btl</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_save_tp">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Master PO (EMPO) -->
    <div class="modal fade" id="modal_empo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="exampleModalLongTitle"><b><i class="fas fa-user-edit"> Edit Master
                                PO</i></b> </h5>
                </div>
                <form id="frm_empo">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <input type="hidden" id="empo_id" name="empo_id">
                                <input type="hidden" id="role" name="role" value="{{ Auth::user()->role }}">
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-6">
                                <strong><i class="fas fa-quote-left"> Item Cd</i></strong>
                                <input type="text" id="empo_itemCd" name="empo_itemCd" class="form-control rounded-0"
                                    placeholder="Masukkan Nama Anda ." required disabled>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-low-vision"> Nama Barang</i></strong>
                                <input type="text" id="empo_nama" name="empo_nama" class="form-control rounded-0"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12">
                                <strong><i class="fas fa-low-vision"> Spesifikasi</i></strong>
                                <input type="text" id="empo_spesifikasi" name="empo_spesifikasi"
                                    class="form-control rounded-0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-8">
                                <strong><i class="fas fa-low-vision"> Harga</i></strong>
                                <input type="text" id="empo_harga1" name="empo_harga1"
                                    class="number-separator form-control rounded-0" required>
                                <input type="hidden" id="empo_harga" name="empo_harga" class="form-control rounded-0"
                                    required>
                            </div>
                            <div class="col col-md-4">
                                <strong><i class="fas fa-low-vision"> Satuan</i></strong>
                                <select name="empo_satuan" id="empo_satuan" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Psg">Pasang</option>
                                    <option value="Set">Set</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Btl">Btl</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" id="btn_save_ep">Simpan</button>
                    </div>
                </form>
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
    <script src="{{ asset('js/master_item.js') }}"></script>
    <script src="{{ asset('js/easy-number-separator.js') }}"></script>
@endpush
