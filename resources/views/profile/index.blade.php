@extends('layouts.template')
@section('content')
<div class="container mt-4">    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mx-auto" style="max-width: 400px">
        <div class="card-body text-center">
            <img src="{{ $user->foto ? asset('storage/foto/'.$user->foto) : asset('adminlte/dist/img/user2-160x160.jpg') }}"
                class="img-circle elevation-2 mb-3" width="120" height="120" alt="User profile picture">

            <form action="{{ route('profile.update-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-2">
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Update Foto</button>
            </form>
        </div>
    </div>
</div>
@endsection