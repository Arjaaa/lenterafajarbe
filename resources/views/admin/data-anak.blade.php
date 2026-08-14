@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ======================================
        HEADER: Judul & Tanggal Hari Ini
        ====================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Daftar Semua Siswa</h4>

            {{-- Badge Tanggal (Warna & Radius disesuaikan foto) --}}
            <div class="bg-white px-4 py-2 d-flex align-items-center"
                style="border-radius: 50px; border: 1px solid #f0f0f0;">
                <i class="bx bx-calendar text-success me-2 fs-5"
                    style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y') }}
                </span>
            </div>
        </div>

        {{-- Notifikasi Error/Success --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong></strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Ada yang salah:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- BUNGKUS CARD UTAMA (Outline Biru Persis Desain) --}}
        <div class="card bg-white"
            style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- Panggil Potongan Tabel di Sini --}}
            @include('admin.anak._table')

        </div>

        {{-- Panggil Potongan Modal Tambah di Sini --}}
        @include('admin.anak._modal-tambah')

    </div>
@endsection

{{-- Panggil Potongan Script di Sini --}}
@include('admin.anak._scripts')