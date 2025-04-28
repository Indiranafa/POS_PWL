@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <!-- Tombol untuk membuka form create stok via AJAX -->
                <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    Tambah Penjualan
                </button>
                {{-- <button onclick="modalAction('{{ url('penjualan/import') }}')" class="btn btn-sm btn-info mt-1">
                    <i class="fa fa-file-excel mr-1"></i>Import Data Penjualan
                </button> --}}
                <a href="{{ url('penjualan/export_excel') }}" class="btn btn-sm btn-primary mt-1">
                    <i class="fa fa-file-excel mr-1"></i>Export Excel
                </a>
                <a href="{{ url('penjualan/export_pdf') }}" class="btn btn-sm btn-warning mt-1">
                    <i class="fa fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Penjualan Kode</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Harga Total</th> 
                        <th>Aksi</th>
                    </tr>
                </thead>
                    
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }
        var tablePenjualan;
        $(document).ready(function () {
            tablePenjualan = $('#table_penjualan').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('penjualan.list') }}",
                    type: "POST"
                },
                columns: [
                    { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                    { data: "penjualan_kode", orderable: true, searchable: true },
                    { data: "pembeli", orderable: true, searchable: true },
                    { data: "penjualan_tanggal", orderable: true, searchable: true },
                    { data: "user_name", orderable: false, searchable: false },
                    { data: "harga_total", orderable: true, searchable: true },
                    { data: "aksi", className: "text-center", orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush