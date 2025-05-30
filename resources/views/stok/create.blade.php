@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('penjualan') }}" class="form-horizontal">
                @csrf
                <!-- Input Pembeli -->
                <div class="form-group row">
                    <label class="col-2 control-label col-form-label">Pembeli</label>
                    <div class="col-10">
                        <input type="text" class="form-control" name="pembeli"
                               value="{{ old('pembeli') }}" placeholder="Nama pembeli" required>
                        @error('pembeli')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <!-- Input Kode Penjualan -->
                <div class="form-group row">
                    <label class="col-2 control-label col-form-label">Kode Penjualan</label>
                    <div class="col-10">
                        <input type="text" class="form-control" name="penjualan_kode"
                               value="{{ old('penjualan_kode') }}" placeholder="Kode penjualan" required>
                        @error('penjualan_kode')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <!-- Input Tanggal Penjualan -->
                <div class="form-group row">
                    <label class="col-2 control-label col-form-label">Tanggal Penjualan</label>
                    <div class="col-10">
                        <input type="date" class="form-control" name="penjualan_tanggal"
                               value="{{ old('penjualan_tanggal') }}" required>
                        @error('penjualan_tanggal')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <!-- Input Total Harga -->
                <div class="form-group row">
                    <label class="col-2 control-label col-form-label">Total Harga</label>
                    <div class="col-10">
                        <input type="number" class="form-control" name="total_harga"
                               value="{{ old('total_harga') }}" placeholder="Total harga" required>
                        @error('total_harga')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <!-- Tombol Simpan -->
                <div class="form-group row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        <a href="{{ url('penjualan') }}" class="btn btn-sm btn-default ml-1">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush