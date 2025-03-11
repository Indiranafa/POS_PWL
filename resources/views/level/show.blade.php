@extends('layouts.template')

@section('content')
<div class="container">
    <h2>Detail Level</h2>
    <table class="table">
        <tr>
            <th>Kode Level</th>
            <td>{{ $level->level_kode }}</td>
        </tr>
        <tr>
            <th>Nama Level</th>
            <td>{{ $level->level_nama }}</td>
        </tr>
    </table>
    <a href="{{ route('level.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
