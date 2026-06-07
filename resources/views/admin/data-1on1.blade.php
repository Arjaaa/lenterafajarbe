@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Sesi Terapi 1 on 1</h4>

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
        <h5 class="mb-0 text-white">Daftar Kelas 1 on 1</h5>
        <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#modalTambah1on1">
            <i class="bx bx-plus me-1"></i> Buat Sesi Baru
        </button>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Program / Terapi</th>
                    <th>Nama Anak</th>
                    <th>Terapis (Guru)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($oneOnOnes as $index => $sesi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $sesi->name }}</strong></td>
                    
                    <td><span class="text-primary fw-semibold">{{ $sesi->student->name ?? 'Data Hilang' }}</span></td>
                    <td><span class="text-info fw-semibold">{{ $sesi->teacher->name ?? 'Data Hilang' }}</span></td>
                    
                    <td>
                        <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit1on1{{ $sesi->id }}" title="Edit">
                            <i class="bx bx-edit-alt"></i>
                        </button>

                        <form action="{{ route('koor.destroy1on1', $sesi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kamu yakin ingin menghapus sesi {{ $sesi->name }} ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                        </form>

                        <x-modal id="modalEdit1on1{{ $sesi->id }}" title="Edit Sesi 1 on 1">
                            <form action="{{ route('koor.update1on1', $sesi->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Program / Terapi <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $sesi->name }}" placeholder="Contoh: Terapi Wicara" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Anak <span class="text-danger">*</span></label>
                                        <select name="student_id" class="form-select" required>
                                            <option value="">-- Pilih Anak --</option>
                                            @foreach($students as $student)
                                                {{-- LOGIKA EDIT ANAK --}}
                                                @if(!in_array($student->id, $busyStudentIds) || $sesi->student_id == $student->id)
                                                    <option value="{{ $student->id }}" {{ $sesi->student_id == $student->id ? 'selected' : '' }}>
                                                        {{ $student->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Pilih Terapis <span class="text-danger">*</span></label>
                                        <select name="teacher_id" class="form-select" required>
                                            <option value="">-- Pilih Terapis --</option>
                                            @foreach($teachers as $teacher)
                                                {{-- LOGIKA EDIT GURU --}}
                                                @if(!in_array($teacher->id, $busyTeacherIds) || $sesi->teacher_id == $teacher->id)
                                                    <option value="{{ $teacher->id }}" {{ $sesi->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                        {{ $teacher->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-top pt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada sesi Terapi 1 on 1 yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-modal id="modalTambah1on1" title="Buat Sesi 1 on 1 Baru">
    <form action="{{ route('koor.store1on1') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Program / Terapi <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Terapi Okupasi" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Anak <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                    <option value="">-- Pilih Anak --</option>
                    @foreach($students as $student)
                        {{-- LOGIKA TAMBAH ANAK --}}
                        @if(!in_array($student->id, $busyStudentIds))
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="mb-0">
                <label class="form-label">Pilih Terapis <span class="text-danger">*</span></label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">-- Pilih Terapis --</option>
                    @foreach($teachers as $teacher)
                        {{-- LOGIKA TAMBAH GURU --}}
                        @if(!in_array($teacher->id, $busyTeacherIds))
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer border-top pt-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Buat Sesi</button>
        </div>
    </form>
</x-modal>
@endsection