@empty($penjualan)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle"></i> Kesalahan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Data penjualan tidak ditemukan.</h5>
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Detail Data Penjualan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="callout callout-info">
                    <h6><i class="fas fa-calendar-alt"></i> Tanggal Penjualan:</h6>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->translatedFormat('d F Y') }}</p>
                </div>
                <div class="callout callout-success">
                    <h6><i class="fas fa-user"></i> Nama Pembeli:</h6>
                    <p class="mb-0">{{ $penjualan->pembeli }}</p>
                </div>

                <hr>
                <h5 class="mb-3"><i class="fas fa-box-open"></i> Detail Barang</h5>

                @foreach($penjualan->detail as $detail)
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Nama Barang</strong></p>
                                <span class="badge badge-secondary">{{ $detail->barang->barang_nama }}</span>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Jumlah</strong></p>
                                <span class="badge badge-info">{{ $detail->jumlah }}</span>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Harga</strong></p>
                                <span class="badge badge-success">
                                    Rp {{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}
                                </span>
                                
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
@endempty
