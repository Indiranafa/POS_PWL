@extends('layouts.template')

@section('content')
<div class="container">
    <h2>Tambah Level</h2>
    <form action="{{ route('level.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="level_kode" class="form-label">Kode Level</label>
            <input type="text" class="form-control" id="level_kode" name="level_kode" required>
        </div>
        <div class="mb-3">
            <label for="level_nama" class="form-label">Nama Level</label>
            <input type="text" class="form-control" id="level_nama" name="level_nama" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('level.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
