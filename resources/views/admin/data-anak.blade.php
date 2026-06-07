@extends('layouts.admin')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Pengguna /</span> Data Anak</h4>

    {{-- Notifikasi Error/Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Yay! 🎉</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oops! Ada yang salah:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white mb-3">
            <h5 class="mb-0 text-white">Daftar Data Anak</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahAnak">
                <i class="bx bx-plus me-1"></i> Tambah Anak
            </button>
        </div>

        {{-- Panggil Potongan Tabel di Sini --}}
        @include('admin.anak._table')
    </div>

    {{-- Panggil Potongan Modal Tambah di Sini --}}
    @include('admin.anak._modal-tambah')

@endsection

{{-- Panggil Potongan Script di Sini --}}
@include('admin.anak._scripts')