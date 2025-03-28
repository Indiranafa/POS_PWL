@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ isset($detail) ? 'Edit Detail Penjualan' : 'Tambah Detail Penjualan' }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($detail) ? url('penjualan/'.$penjualan->penjualan_id.'/detail/'.$detail->detail_id) : url('penjualan/'.$penjualan->penjualan_id.'/detail') }}"
                  method="POST">
                @csrf
                @if(isset($detail))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="barang_id">Barang</label>
                    <select name="barang_id" id="barang_id" class="form-control" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->barang_id }}"
                                {{ isset($detail) && $detail->barang_id == $barang->barang_id ? 'selected' : '' }}>
                                {{ $barang->barang_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control"
                           value="{{ isset($detail) ? $detail->harga : old('harga') }}" required>
                </div>

                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control"
                           value="{{ isset($detail) ? $detail->jumlah : old('jumlah') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">{{ isset($detail) ? 'Update' : 'Simpan' }}</button>
                <a href="{{ url('penjualan/'.$penjualan->penjualan_id) }}" class="btn btn-default">Batal</a>
            </form>
        </div>
    </div>
@endsection