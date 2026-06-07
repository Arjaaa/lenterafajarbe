@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Manajemen Kelas /</span> Detail Sesi 1 on 1: {{ $oneOnOne->name }}
        </h4>
        <a href="{{ route('koor.data1on1') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title text-white mb-0">Info Sesi Terapi</h6>
                </div>
                <div class="card-body pt-4">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted ps-0" width="40%">Nama Sesi</td>
                            <td class="fw-semibold">: {{ $oneOnOne->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Terapis</td>
                            <td class="fw-semibold text-primary">: {{ $oneOnOne->teacher->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status</td>
                            <td>: <span class="badge bg-label-success">Aktif Berjalan</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title text-white mb-0">Data Anak Didik Privat</h6>
                </div>

                @if($oneOnOne->student)
                    <div class="table-responsive text-nowrap mt-2">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>NAMA ANAK</th>
                                    <th>KEBUTUHAN KHUSUS</th>
                                    <th>ASAL SEKOLAH</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($oneOnOne->student->photo)
                                                    <img src="{{ asset('storage/' . $oneOnOne->student->photo) }}"
                                                        class="rounded-circle" style="object-fit: cover;">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-secondary"><i
                                                            class="bx bx-user"></i></span>
                                                @endif
                                            </div>
                                            <strong>{{ $oneOnOne->student->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-warning">{{ $oneOnOne->student->special_needs ?? '-' }}</span>
                                    </td>
                                    <td>{{ $oneOnOne->student->school_name ?? 'Lentera Fajar' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top mt-3">
                        <small class="text-muted text-uppercase d-block mb-2">Kontak Darurat Orang Tua:</small>
                        <div class="text-dark bg-light p-3 rounded d-inline-block">
                            <i class="bx bx-phone me-1 text-success"></i>
                            <strong>{{ $oneOnOne->student->parent_phone ?? 'Tidak ada data nomor HP' }}</strong>
                            <span
                                class="text-muted fw-normal ms-2">({{ $oneOnOne->student->father_name ?? $oneOnOne->student->mother_name ?? 'Wali' }})</span>
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <h6 class="text-muted">Belum ada anak yang dimasukkan ke sesi terapi ini.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection