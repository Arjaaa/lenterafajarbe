@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Manajemen Kelas /</span> Detail Group Shadow: {{ $shadow->name }}
        </h4>
        <a href="{{ route('koor.dataShadowGroup') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title text-white mb-0">Info Group Shadow</h6>
                </div>
                <div class="card-body pt-4">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted ps-0" width="45%">Nama Group</td>
                            <td class="fw-semibold">: {{ $shadow->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Asal Sekolah</td>
                            <td class="fw-semibold">: {{ $shadow->school_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">PJ Shadow</td>
                            <td class="fw-semibold text-primary">: {{ $shadow->pic->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Partner Shadow</td>
                            <td class="fw-semibold text-primary">: {{ $shadow->partner->name ?? 'Tidak Ada' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title text-white mb-0">Data Anak Didik (Eksklusif)</h6>
                </div>

                @if($shadow->student)
                    <div class="table-responsive text-nowrap mt-2">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>NAMA ANAK</th>
                                    <th>KEBUTUHAN KHUSUS</th>
                                    <th>USIA / TGL LAHIR</th>
                                    <th>NAMA ORANG TUA</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($shadow->student->photo)
                                                    <img src="{{ asset('storage/' . $shadow->student->photo) }}"
                                                        class="rounded-circle" style="object-fit: cover;">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-secondary"><i
                                                            class="bx bx-user"></i></span>
                                                @endif
                                            </div>
                                            <strong>{{ $shadow->student->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-danger">{{ $shadow->student->special_needs ?? 'Belum ada data' }}</span>
                                    </td>
                                    <td>{{ $shadow->student->birth_date ? \Carbon\Carbon::parse($shadow->student->birth_date)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td>{{ $shadow->student->father_name ?? $shadow->student->mother_name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top mt-3">
                        <small class="text-muted text-uppercase d-block mb-2">Catatan Diagnosis Khusus:</small>
                        <div class="text-dark bg-light p-3 rounded" style="min-height: 80px;">
                            {!! nl2br(e($shadow->student->diagnosis_notes ?? 'Tidak ada catatan medis.')) !!}
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <h6 class="text-muted">Belum ada anak didik yang ditugaskan ke group ini.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection