<style>
    .container {
        display: flex;
        flex-wrap: wrap;
    }

    .item {
        border: 1px solid #000;
        margin: 5px;
        padding: 5px;
        width: 75px;
        /* Tentukan lebar tetap untuk setiap item */
        text-align: center;
        /* Pusatkan konten dalam setiap item */
    }

    .qr-code {
        margin-bottom: 3px;
        /* Tambahkan jarak antara QR code dan informasi anggota */
    }

    .info p {
        margin: 0;
        /* Hapus margin default untuk paragraf */
    }
</style>

<div class="container">
    @foreach ($anggota as $index => $pq)
        <div class="item">
            <div class="qr-code"> {!! $qrCodes[$index] !!}</div>
            <div class="info">
                <p style="font-size: 12px; font-weight:bold">{{ $pq->no_barcode }}</p>
            </div>
        </div>
    @endforeach
</div>
