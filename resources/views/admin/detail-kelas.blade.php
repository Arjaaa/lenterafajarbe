@extends('layouts.admin')

@section('content')
    <div class="mb-4">
        <a href="{{ route('koor.dataKelas') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-chevron-left me-1"></i> Kembali ke Daftar Kelas
        </a>
    </div>

    <h4 class="fw-bold py-3 mb-2"><span class="text-muted fw-light">Manajemen Kelas /</span> Kelola Murid:
        {{ $class->name }}
    </h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Yay! 🎉</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Info Ruang Kelas</h5>
                </div>
                <div class="card-body pt-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="ps-0" width="40%"><strong>Nama Kelas</strong></td>
                            <td>: {{ $class->name }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Wali Kelas 1</strong></td>
                            <td>: {{ $class->homeroomTeacher->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Wali Kelas 2</strong></td>
                            <td>: {{ $class->homeroomTeacher2->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Total Murid</strong></td>
                            <td>: <span class="badge bg-label-primary">{{ $class->students->count() }} Anak</span></td>
                        </tr>
                    </table>
                    <hr>

                    <form action="{{ route('koor.tambahMuridKeKelas', $class->id) }}" method="POST">
                        @csrf
                        <label class="form-label fw-bold mb-2">Pilih Murid untuk Dimasukkan:</label>

                        <div class="p-2 border rounded bg-light mb-3" style="max-height: 250px; overflow-y: auto;">
                            @php
                                // Ambil ID semua anak yang sudah masuk ke kelas ini agar bisa kita skip/sembunyikan dari daftar centang
                                $currentStudentIds = $class->students->pluck('id')->toArray();
                            @endphp

                            @forelse($allStudents as $mhs)
                                {{-- Hanya tampilkan anak yang BELUM masuk ke kelas ini --}}
                                @if(!in_array($mhs->id, $currentStudentIds))
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $mhs->id }}"
                                            id="chkStudent{{ $mhs->id }}">
                                        <label class="form-check-input-label text-wrap" for="chkStudent{{ $mhs->id }}">
                                            <strong>{{ $mhs->name }}</strong>
                                            <small class="text-muted d-block">({{ $mhs->special_needs ?? 'Umum' }})</small>
                                        </label>
                                    </div>
                                @endif
                            @empty
                                <p class="text-muted text-center mb-0 py-2">Tidak ada data anak.</p>
                            @endforelse

                            {{-- Jika semua anak di database sudah masuk ke kelas ini --}}
                            @if(count($allStudents) == count($currentStudentIds))
                                <p class="text-success text-center mb-0 py-2"><i class="bx bx-check-circle me-1"></i> Semua anak
                                    sudah masuk kelas</p>
                            @endif
                        </div>

                        @if(count($allStudents) != count($currentStudentIds))
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bx bx-plus me-1"></i> Masukkan yang Dicentang
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white">
                    <h5 class="mb-0 text-white">Daftar Murid Kelas {{ $class->name }}</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anak</th>
                                <th>Kebutuhan Khusus</th>
                                <th>Asal Sekolah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($class->students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $student->name }}</strong></td>
                                    <td><span class="badge bg-label-danger">{{ $student->special_needs ?? '-' }}</span></td>
                                    <td>{{ $student->school_name ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('koor.keluarkanMurid', [$class->id, $student->id]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Keluarkan {{ $student->name }} dari kelas {{ $class->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Keluarkan dari Kelas">
                                                <i class="bx bx-log-out me-1"></i> Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada murid di kelas ini. Pilih
                                        murid di panel sebelah kiri untuk menambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection