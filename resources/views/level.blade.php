@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Level</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($levels as $key => $level)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $level->nama }}</td>
                    <td>
                        <a href="{{ url('level/edit/'.$level->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ url('level/hapus/'.$level->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Hapus level ini?')">Hapus</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
