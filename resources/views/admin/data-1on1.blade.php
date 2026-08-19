@extends('layouts.admin')

@section('content')
{{-- CSS Khusus untuk Striping Tabel & Mencegah Teks/Tombol Turun Baris --}}
<style>
    .custom-table-striped tbody tr:nth-of-type(even) {
        background-color: #f4f8ff !important;
    }

    .custom-table-striped tbody tr td {
        border-bottom: none !important;
    }
    
    /* Mencegah teks dan tombol turun baris (bikin baris bengkak ke bawah) */
    .custom-table-striped th, .custom-table-striped td {
        white-space: nowrap !important;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- HEADER: Judul & Tanggal --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">Sesi Terapi 1 on 1</h4>
        
        <div class="bg-white px-4 py-2 d-flex align-items-center" style="border-radius: 50px; border: 1px solid #f0f0f0;">
            <i class="bx bx-calendar text-success me-2 fs-5" style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
            <span class="fw-semibold text-dark">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y') }}
            </span>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong> </strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong>Ada yang salah:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- CARD UTAMA & TABEL --}}
    <div class="card bg-white" style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">
        
        {{-- HEADER TABLE: Search Bar & Tombol Add --}}
        {{-- HEADER TABLE: Search Bar & Tombol Add --}}
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">
            
            {{-- Kiri: Form Search & Tombol Reset --}}
            <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 500px;">
                <div class="input-group" style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                    <span class="input-group-text bg-transparent border-0 pe-1">
                        <i class="bx bx-search" style="color: #1e293b;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="form-control border-0 shadow-none px-2" placeholder="Cari siswa atau terapis..." 
                        style="background: transparent;">
                </div>
                
                <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0" 
                    style="background-color: #5b9cf6; border: none;">Cari</button>

                {{-- Tombol Reset Cuma Muncul Kalau Lagi Nyari Sesuatu --}}
                @if(request()->filled('search'))
                    <a href="{{ url()->current() }}" class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                        style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <i class="bx bx-x me-1"></i> Reset
                    </a>
                @endif
            </form>
            
            {{-- Kanan: Tombol Buat Sesi Baru --}}
            <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none flex-shrink-0" 
                data-bs-toggle="modal" data-bs-target="#modalTambah1on1" style="background-color: #5b9cf6; border: none;">
                <i class="bx bx-plus-circle me-1 fs-5"></i> Buat Sesi Baru
            </button>
        </div>

        {{-- ISI TABEL (DIBUNGKUS RESPONSIVE AGAR BISA SCROLL KE SAMPING) --}}
        <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                <thead style="border-bottom: 2px solid #f0f4f9;">
                    <tr>
                        <th class="text-center py-3 text-muted fw-semibold">No</th>
                        <th class="py-3 text-muted fw-semibold">Nama Kelas Terapi</th>
                        <th class="py-3 text-muted fw-semibold">Nama Terapis</th>
                        <th class="py-3 text-muted fw-semibold">Nama Siswa</th>
                        <th class="text-center py-3 text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($oneOnOnes ?? [] as $index => $sesi)
                        <tr>
                            <td class="text-center align-middle text-dark fw-medium">{{ $index + 1 }}</td>
                            
                            {{-- Nama Kelas Terapi --}}
                            <td class="align-middle text-dark fw-bold">{{ $sesi->name ?? '-' }}</td>
                            
                            {{-- Nama Terapis --}}
                            <td class="align-middle text-dark fw-medium">
                                {{ $sesi->teacher->name ?? 'Data Hilang' }}
                            </td>
                            
                            {{-- Nama Siswa --}}
                            <td class="align-middle text-dark fw-medium">
                                {{ $sesi->student->name ?? 'Data Hilang' }}
                            </td>

                            {{-- Kolom Aksi (3 Tombol Pastel) --}}
                            <td class="text-center align-middle">

                                {{-- Button View (Hijau Soft) --}}
                                <a href="{{ route('koor.detail1on1', ['id' => $sesi->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1]) }}"
                                    class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                    <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                </a>

                                {{-- Button Edit (Biru Soft) --}}
                                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #60a5fa; border: none;" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit1on1{{ $sesi->id ?? 0 }}" title="Edit">
                                    <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                </button>

                                {{-- Button Delete (Merah Soft) --}}
                                <form action="{{ route('koor.destroy1on1', $sesi->id ?? 0) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus data sesi {{ addslashes($sesi->name ?? 'ini') }}?');">
                                    @csrf
                                    @method('DELETE')
                                   <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#modalDelete1on1{{ $sesi->id ?? 0 }}">
                                    <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        {{-- Dummy data pengisi jika data dari API masih kosong --}}
                        @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td colspan="5" class="py-4"></td>
                            </tr>
                        @endfor
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white border-top text-center py-4">
            <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm" style="border-radius: 50px;">
                <a href="{{ ($pagination['current_page'] ?? 1) > 1 ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) - 1]) : 'javascript:void(0)' }}"
                    class="text-dark text-decoration-none me-3 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 pe-none' : '' }}">
                    <i class="bx bx-chevron-left fs-5"></i>
                </a>
                <span class="fw-semibold text-dark mx-2" style="font-size: 0.9rem;">
                    {{ $pagination['current_page'] ?? 1 }} / {{ $pagination['last_page'] ?? 1 }}
                </span>
                <a href="{{ ($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1) ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) + 1]) : 'javascript:void(0)' }}"
                    class="text-dark text-decoration-none ms-3 {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 pe-none' : '' }}">
                    <i class="bx bx-chevron-right fs-5"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- SEMUA MODAL DITARUH DI SINI (LUAR TABEL) AGAR LAYOUT AMAN --}}
    {{-- ========================================================== --}}
    @foreach ($oneOnOnes ?? [] as $sesi)

    {{-- =============================================== --}}
{{-- MODAL KONFIRMASI DELETE SESI 1 ON 1 --}}
{{-- =============================================== --}}
<div class="modal fade" id="modalDelete1on1{{ $sesi->id ?? 0 }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center" style="border-radius: 20px; border: none;">
            
            {{-- Class text-wrap biar teks aman di dalam tabel --}}
            <div class="modal-body p-4 text-wrap">
                
                {{-- Icon Trash Merah --}}
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                    style="width: 60px; height: 60px; background-color: #ffe6e6;">
                    <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2">Hapus Sesi?</h5>
                
                {{-- Class text-wrap di teks paragraf --}}
                <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                    Data sesi <strong>{{ $sesi->name ?? 'ini' }}</strong> akan dihapus permanen dan tidak dapat dikembalikan.
                </p>

                <form action="{{ route('koor.destroy1on1', $sesi->id ?? 0) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn rounded-pill px-4 shadow-none"
                            data-bs-dismiss="modal"
                            style="background-color: #f1f5f9; color: #475569; border: none; font-weight: 600;">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn rounded-pill px-4 text-white shadow-none"
                            style="background-color: #ff5b5c; border: none; font-weight: 600;">
                            Hapus
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>
        {{-- MODAL DETAIL 1 ON 1 --}}
        <x-modal id="modalDetail1on1{{ $sesi->id ?? 0 }}" title="Detail Sesi Terapi">
            <div class="modal-body text-wrap text-start">
                <h6 class="fw-bold mb-3 text-primary">Informasi Sesi Terapi</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="ps-0"><strong>Nama Kelas Terapi</strong></td>
                        <td>: {{ $sesi->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Terapis (Guru)</strong></td>
                        <td>: {{ $sesi->teacher->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Siswa (Anak)</strong></td>
                        <td>: {{ $sesi->student->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </x-modal>

        {{-- MODAL EDIT 1 ON 1 --}}
        <x-modal id="modalEdit1on1{{ $sesi->id ?? 0 }}" title="Edit Sesi 1 on 1">
            <form action="{{ route('koor.update1on1', $sesi->id ?? 0) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Nama Program / Terapi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $sesi->name ?? '' }}" placeholder="Contoh: Terapi Wicara" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Pilih Anak <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Anak --</option>
                            @foreach($students ?? [] as $student)
                                @if(!in_array($student->id, $busyStudentIds ?? []) || ($sesi->student_id ?? 0) == $student->id)
                                    <option value="{{ $student->id }}" {{ ($sesi->student_id ?? 0) == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-dark fw-semibold">Pilih Terapis <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Pilih Terapis --</option>
                            @foreach($teachers ?? [] as $teacher)
                                @if(!in_array($teacher->id, $busyTeacherIds ?? []) || ($sesi->teacher_id ?? 0) == $teacher->id)
                                    <option value="{{ $teacher->id }}" {{ ($sesi->teacher_id ?? 0) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>

    @endforeach

    {{-- MODAL TAMBAH SESI 1 ON 1 BARU --}}
    <x-modal id="modalTambah1on1" title="Buat Sesi 1 on 1 Baru">
        <form action="{{ route('koor.store1on1') }}" method="POST">
            @csrf
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Program / Terapi <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Terapi Okupasi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Pilih Anak <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Pilih Anak --</option>
                        @foreach($students ?? [] as $student)
                            @if(!in_array($student->id, $busyStudentIds ?? []))
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label text-dark fw-semibold">Pilih Terapis <span class="text-danger">*</span></label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- Pilih Terapis --</option>
                        @foreach($teachers ?? [] as $teacher)
                            @if(!in_array($teacher->id, $busyTeacherIds ?? []))
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Buat Sesi</button>
            </div>
        </form>
    </x-modal>

</div>
@endsection